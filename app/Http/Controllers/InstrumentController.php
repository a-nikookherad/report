<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstrumentInfoStoreRequest;
use App\Http\Requests\StoreInstrumentRequest;
use App\Logics\Bourse\Normalize\ActivityDataNormalizeLogic;
use App\Logics\Bourse\Normalize\BalanceSheetDataNormalizeLogic;
use App\Logics\Bourse\Normalize\CashFlowDataNormalizeLogic;
use App\Logics\Bourse\Normalize\EquityDataNormalizeLogic;
use App\Logics\Bourse\Normalize\IncomeStatementDataNormalizeLogic;
use App\Models\Bourse\Activity;
use App\Models\Bourse\BalanceSheet;
use App\Models\Bourse\CashFlow;
use App\Models\Bourse\Equity;
use App\Models\Bourse\FinancialPeriod;
use App\Models\Bourse\Group;
use App\Models\Bourse\History;
use App\Models\Bourse\IncomeStatement;
use App\Models\Bourse\Industry;
use App\Models\Bourse\Instrument;
use App\Models\Instruments\Dollar;
use App\Models\Instruments\Gold;
use App\Tools\Excel\Facades\Excel;
use Hekmatinasser\Verta\Facades\Verta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class InstrumentController extends Controller
{
    public function list()
    {
        $instruments = Instrument::query()
            ->with("industry")
            ->get();
        return view('contents.instruments.list', compact("instruments"));
    }

    public function updateHistory()
    {
        $instrumentInstance = Instrument::query()
            ->where("id", request("instrument_id"))
            ->with(["financialPeriods.incomeStatements", "incomeStatement", "balanceSheet", "lastHistory"])
            ->first();
        if ($timeStamp = $instrumentInstance->lastHistory?->timestamp) {
            $startDateTime = $timeStamp;
        } else {
            $startDateTime = Date::now()->subYears(config("financial.how_many_years_ago_for_report"))->timestamp;
        }

        $endDateTime = Date::now()->timestamp;
        $url = config("financial.mofid_url") . "?resolution=1D&" . $instrumentInstance->mofid_url;
        $url .= "&from=" . $startDateTime;
        $url .= "&to=" . $endDateTime;

        $response = Http::withToken(config("financial.mofid_token"))
            ->get($url);

        if (!$response->successful()) {
            dd("please check mofid token");
        }

        set_time_limit(50000);
        $response = $response->object();
        foreach ($response->t as $index => $time) {
            $date = Date::createFromTimestamp($time)->format("Y-m-d");
            $solarDate = Verta::parse(verta($time))->endYear()->format("Y-m-d");
            $financialPeriod = FinancialPeriod::query()
                ->where("instrument_id", $instrumentInstance->id)
                ->where("solar_end_date", $solarDate)
                ->with("incomeStatements")
                ->firstOr(function () use ($instrumentInstance, $time) {
                    $date = Verta::parse(verta($time))->subYear()->endYear()->format("Y-m-d");
                    return FinancialPeriod::query()
                        ->where("instrument_id", $instrumentInstance->id)
                        ->where("solar_end_date", $date)
                        ->with("incomeStatements")
                        ->first();
                });
            try {
                if (!$financialPeriod) {
                    continue;
                }
                $fund = $financialPeriod->incomeStatements->where("fund", "!=", null)->last()?->fund;
                $shareCount = !empty($fund) ? $fund / 100 : $financialPeriod->share_count;
                $record = [
                    "open" => $response->o[$index],
                    "high" => $response->h[$index],
                    "low" => $response->l[$index],
                    "close" => $response->c[$index],
                    "volume" => $response->v[$index],
                    "share_count" => $shareCount,
                    "tarikh" => verta($date)->format("Y-m-d") ?? null,
                    "date_time" => $date,
                    "timestamp" => $response->t[$index],
                    "financial_period_id" => $financialPeriod->id,
                    "instrument_id" => $instrumentInstance->id,
                ];
                History::query()
                    ->create($record);
            } catch (\Exception $exception) {
//                        dd($exception->getMessage());
                continue;
            }
        }
        return redirect()->back();
    }

    public function add()
    {
        $groups = Group::query()
            ->get();
        $industries = Industry::query()
            ->get();
        return view('contents.instruments.add', compact("groups", "industries"));
    }

    public function store(StoreInstrumentRequest $request)
    {
        try {
            DB::beginTransaction();
            $this->checkOrCreateFolder($request->slug);

            //Insert instrument to database
            $instrumentInstance = Instrument::query()
                ->updateOrCreate(["symbol" => $request->symbol], $request->except(["_token", "financial_period"]));

            //Create financial periods
            $this->addFinancialPeriods($instrumentInstance->id, $request->financial_period);

            DB::commit();
            return response()->json([
                "message" => "Instrument successfully added!",
                "data" => "success",
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                "message" => "something went wrong",
                "error" => $exception->getMessage() ?? null,
            ], 403);
        }
    }

    public function addInfo()
    {
        $instrumentInstance = Instrument::query()
            ->where("id", request("instrument_id"))
            ->with("financialPeriods")
            ->first();
        return view("contents.instruments.add_info", compact("instrumentInstance"));
    }

    public function storeInfo(InstrumentInfoStoreRequest $request)
    {

        if ($request->instrument_json) {
            $dataSource = json_decode($request->instrument_json, true);

            if (!strstr(strtolower($dataSource["sheets"][0]["title_En"]), str_replace("_", " ", $request->financial_report_type))) {
                return response()->json([
                    "message" => "please select right financial report type",
                    "error" => []
                ], 400);
            }

            //Get instrument
            $instrumentInstance = Instrument::query()
                ->where("id", $request->instrument_id)
                ->with("financialPeriods")
                ->first();

            $date = Verta::parse($dataSource["periodEndToDate"])->format("Y-m-d");

            //Make related folder if not exists
            $filePath = storage_path(config("financial.folder") . "/" . $instrumentInstance->slug);
            $jsonFile = $filePath . "/json/" . $request->financial_report_type;
            $this->makeJsonFileIfNotExists($jsonFile, $date, $dataSource);

            //Make xlsx file
            $xlsxFile = $filePath . "/xlsx/" . $request->financial_report_type . "/" . $date . ".xlsx";

            if ($request->financial_report_type == "activity") {
                $data = $this->storeActivityInDatabase($dataSource, $instrumentInstance);
            } elseif ($request->financial_report_type == "balance_sheet") {
                $data = $this->storeBalanceSheetInDatabase($dataSource, $instrumentInstance);
            } elseif ($request->financial_report_type == "income_statement") {
                $data = $this->storeIncomeStatementInDatabase($dataSource, $instrumentInstance);
            } elseif ($request->financial_report_type == "cash_flow") {
                $data = $this->storeCashFlowInDatabase($dataSource, $instrumentInstance);
            } elseif ($request->financial_report_type == "changes_in_property_rights") {
                $data = $this->storeEquityInDatabase($dataSource, $instrumentInstance);
            }

            if (!empty($data)) {
                Excel::write($xlsxFile, $data);
            }
        }
        return response()->json([
            "message" => "information insert successfully",
            "data" => []
        ], 200);
    }

    private function checkOrCreateFolder(mixed $path)
    {
        $financialFolder = config("financial.folder") . DIRECTORY_SEPARATOR . $path;
        if (!file_exists(storage_path($financialFolder))) {
            mkdir(storage_path($financialFolder . DIRECTORY_SEPARATOR . "json"), "0777", true);
            mkdir(storage_path($financialFolder . DIRECTORY_SEPARATOR . "xlsx"), "0777", true);
        }
    }

    private function addFinancialPeriods($instrument_id, mixed $financial_period)
    {
        for ($i = 0; $i <= 6; $i++) {
            if (Verta::parse($financial_period)->addYears($i)->format("m-d") == "12-29") {
                $startSolar = Verta::parse($financial_period)->addYears($i)->startYear()->format("Y-m-d");
                $endSolar = Verta::parse($financial_period)->addYears($i)->endYear()->format("Y-m-d");
            } else {
                $endSolar = Verta::parse($financial_period)->addYears($i)->format("Y-m-d");
                $startSolar = Verta::parse($endSolar)->subDays(364)->format("Y-m-d");
            }
            $date = [
                "solar_start_date" => $startSolar,
                "solar_end_date" => $endSolar,
                "start_date" => Verta::parse($startSolar)->datetime()->format("Y-m-d"),
                "end_date" => Verta::parse($endSolar)->datetime()->format("Y-m-d"),
                "share_count" => null,
                "instrument_id" => $instrument_id,
            ];

            FinancialPeriod::query()
                ->updateOrCreate(["solar_end_date" => $date["solar_end_date"], "instrument_id" => $date["instrument_id"]], $date);
        }
    }

    private function makeJsonFileIfNotExists($filePath, $date, mixed $dataSource): void
    {
        if (!file_exists($filePath)) {
            mkdir($filePath, 0777, true);
        }
        $fileName = $filePath . "/" . $date . ".json";
        if (!file_exists($fileName)) {
            file_put_contents($fileName, json_encode($dataSource));
        }
    }

    private function storeActivityInDatabase(mixed $dataSource, $instrumentInstance)
    {
        $activityLogic = resolve(ActivityDataNormalizeLogic::class, [
            "dataSource" => $dataSource
        ]);

        $record = $activityLogic->normalize();
        $record["instrument_id"] = $instrumentInstance->id;
        Activity::query()
            ->updateOrCreate(["instrument_id" => $record["instrument_id"], "order" => $record["order"], "financial_period_id" => $record["financial_period_id"]], $record);
        return $activityLogic->getData();

    }

    private function storeIncomeStatementInDatabase(mixed $dataSource, $instrumentInstance)
    {
        $incomeStatementLogic = resolve(IncomeStatementDataNormalizeLogic::class, [
            "dataSource" => $dataSource
        ]);

        $record = $incomeStatementLogic->normalize();
        $record["instrument_id"] = $instrumentInstance->id;
        IncomeStatement::query()
            ->updateOrCreate(["instrument_id" => $record["instrument_id"], "financial_period_id" => $record["financial_period_id"], "order" => $record["order"]], $record);

        return $incomeStatementLogic->getData();
    }

    private function storeBalanceSheetInDatabase(mixed $dataSource, $instrumentInstance)
    {
        $balanceSheetLogic = resolve(BalanceSheetDataNormalizeLogic::class, [
            "dataSource" => $dataSource
        ]);

        $record = $balanceSheetLogic->normalize();
        $record["instrument_id"] = $instrumentInstance->id;
        BalanceSheet::query()
            ->updateOrCreate(["instrument_id" => $record["instrument_id"], "financial_period_id" => $record["financial_period_id"], "order" => $record["order"]], $record);
        return $balanceSheetLogic->getData();
    }

    private function storeCashFlowInDatabase(mixed $dataSource, $instrumentInstance)
    {
        $cashFlowLogic = resolve(CashFlowDataNormalizeLogic::class, [
            "dataSource" => $dataSource
        ]);

        $record = $cashFlowLogic->normalize();
        $record["instrument_id"] = $instrumentInstance->id;
        CashFlow::query()
            ->updateOrCreate(["financial_period_id" => $record["financial_period_id"], "order" => $record["order"]], $record);
        return $cashFlowLogic->getData();
    }

    private function storeEquityInDatabase(mixed $dataSource, $instrumentInstance)
    {
        $equityLogic = resolve(EquityDataNormalizeLogic::class, [
            "dataSource" => $dataSource
        ]);

        $record = $equityLogic->normalize();
        $record["instrument_id"] = $instrumentInstance->id;
        Equity::query()
            ->updateOrCreate(["instrument_id" => $record["instrument_id"], "financial_period_id" => $record["financial_period_id"], "order" => $record["order"]], $record);
        return $equityLogic->getData();
    }
}
