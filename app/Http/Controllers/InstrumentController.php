<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstrumentInfoStoreRequest;
use App\Http\Requests\StoreInstrumentRequest;
use App\Logics\Bourse\Normalize\ActivityDataNormalizeLogic;
use App\Logics\Bourse\Normalize\BalanceSheetDataNormalizeLogic;
use App\Logics\Bourse\Normalize\CashFlowDataNormalizeLogic;
use App\Logics\Bourse\Normalize\IncomeStatementDataNormalizeLogic;
use App\Models\Bourse\Activity;
use App\Models\Bourse\BalanceSheet;
use App\Models\Bourse\CashFlow;
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
            ->with(["financialPeriods.incomeStatements", "incomeStatement", "balanceSheet"])
            ->first();
        $startDateTime = Date::now()->subYears(config("financial.how_many_years_ago_for_report"))->timestamp;
        $endDateTime = Date::now()->timestamp;
        $url = config("financial.mofid_url") . "?resolution=1D&" . $instrumentInstance->mofid_url;
        $url .= "&from=" . $startDateTime;
        $url .= "&to=" . $endDateTime;

        $response = Http::withToken(config("financial.mofid_token"))
            ->get($url);

        if (!$response->successful() || $response->object()->s != "ok") {
            dd("please set mofid token");
        }
        set_time_limit(5000);
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
                    ->updateOrCreate([
                        "instrument_id" => $record["instrument_id"],
                        "financial_period_id" => $record["financial_period_id"],
                        "date_time" => $record["date_time"],
                    ], $record);
            } catch (\Exception $exception) {
//                        dd($exception->getMessage());
                continue;
            }
        }
        return redirect()->back();
    }

    public function ratio()
    {
        $instrumentInstance = Instrument::query()
            ->where("id", request("instrument_id"))
            ->with(["financialPeriods.incomeStatements", "incomeStatement", "balanceSheet"])
            ->first();
        $startDateTime = Date::now()->subYear()->timestamp;
        $endDateTime = Date::now()->timestamp;
        $url = config("financial.mofid_url") . "?" . $instrumentInstance->mofid_url;
        $url .= "&from=" . $startDateTime;
        $url .= "&to=" . $endDateTime;

        $res = \Illuminate\Support\Facades\Http::withHeaders([
            "accept" => "application/json",
            "Authorization" => "Bearer " . config("financial.mofid_token")
        ])->get($url)
            ->object();
        if (!empty($res->s) && $res->s == "ok") {
            foreach ($res->t as $index => $time) {
                $date = \Illuminate\Support\Facades\Date::createFromTimestamp($time)->format("Y-m-d");
                foreach ($instrumentInstance->financialPeriods as $financialPeriod) {
                    try {
                        if ($financialPeriod->start_date < $date && $financialPeriod->end_date > $date) {
                            $fund = $financialPeriod->incomeStatements->where("fund", "!=", null)->last()?->fund;
                            $shareCount = !empty($fund) ? $fund / 100 : $financialPeriod->share_count;
                            $record = [
                                "open" => $res->o[$index],
                                "high" => $res->h[$index],
                                "low" => $res->l[$index],
                                "close" => $res->c[$index],
                                "volume" => $res->v[$index],
                                "share_count" => $shareCount,
                                "tarikh" => verta($date)->format("Y-m-d") ?? null,
                                "date_time" => $date,
                                "timestamp" => $res->t[$index],
                                "financial_period_id" => $financialPeriod->id,
                                "instrument_id" => $instrumentInstance->id,
                            ];
                            History::query()->updateOrCreate([
                                "instrument_id" => $record["instrument_id"],
                                "financial_period_id" => $record["financial_period_id"],
                                "timestamp" => $record["timestamp"],
                                "date_time" => $record["date_time"],
                            ], $record);
                        }
                    } catch (\Exception $exception) {
//                        dd($exception->getMessage());
                        continue;
                    }
                }
            }
        } else {
            dd("please set valid mofid token");
        }

        //ratio
        $historyInstance = History::query()
            ->where("instrument_id", $instrumentInstance->id)
            ->orderBy("date_time", "desc")
            ->first();

        $incomeStatement = $instrumentInstance->incomeStatement;
        $balanceSheet = $instrumentInstance->balanceSheet;
        $lastYearLatestBalanceSheet = BalanceSheet::query()
            ->whereHas("financialPeriod", function (Builder $query) use ($balanceSheet) {
                return $query->where("solar_end_date", Verta::parse($balanceSheet->financialPeriod->solar_end_date)->subYear()->endYear()->format("Y-m-d"));
            })->orderBy("order", "desc")
            ->first();

        $price = $historyInstance->close / 10 * $historyInstance->share_count;
        $earn = $incomeStatement->net_profit / $incomeStatement->order * 12;

        $ratio = [];
        $ratio["gross"] = number_format($incomeStatement->gross_profit / $incomeStatement->total_revenue * 100) . "%";
        $ratio["net"] = number_format($incomeStatement->net_profit / $incomeStatement->total_revenue * 100) . "%";

        //calculate P/E
        $ratio["P/E"] = number_format($price / $earn, 1);

        //calculate P/S
        $activity = Activity::query()
            ->where("instrument_id", $instrumentInstance->id)
            ->orderBy("id", "desc")
            ->first();
        $ratio["P/S"] = number_format($price / $activity->predict_year_sales, 1);

        //calculate P/A
        $ratio["P/A"] = number_format($price / $balanceSheet->total_assets, 1);

        //calculate P/B
        $ratio["P/B"] = number_format($price / $balanceSheet->total_equity, 1);

        //calculate R/A
        $ratio["RC/A"] = number_format($balanceSheet->receivable_claim / $balanceSheet->total_assets * 100) . "%";

        //calculate E/E or ROE
        $ratio["ROE"] = number_format($earn / (($lastYearLatestBalanceSheet->total_equity * 2 + $earn) / 2) * 100) . "%";

        //calculate E/E or ROE
        $ratio["ROA"] = number_format($earn / (($lastYearLatestBalanceSheet->total_assets * 2 + $earn) / 2) * 100) . "%";

        //calculate IRR/XUD
        $gold = Gold::query()
            ->orderBy("id", "desc")
            ->first();
        $ratio["P/XUD"] = ($price / $gold->close / 1000000) . " T";

        //calculate IRR/USD
        $dollar = Dollar::query()
            ->orderBy("id", "desc")
            ->first();
        $ratio["P/USD"] = number_format($price / ($dollar->close / 10), null, null, ",");


        dd($ratio);
        return view("contents.instruments.ratio", compact("instrumentInstance"));
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
                $this->makeXlsxFromActivityAndStoreInDatabase($dataSource, $instrumentInstance, $xlsxFile);
            } elseif ($request->financial_report_type == "balance_sheet") {
                $this->makeXlsxFromBalanceSheetAndStoreInDatabase($dataSource, $instrumentInstance, $xlsxFile);
            } elseif ($request->financial_report_type == "income_statement") {
                $this->makeXlsxFromIncomeStatementAndStoreInDatabase($dataSource, $instrumentInstance, $xlsxFile);
            } elseif ($request->financial_report_type == "cash_flow") {
                $this->makeXlsxFromCashFlowAndStoreInDatabase($dataSource, $instrumentInstance, $xlsxFile);
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

    private function makeXlsxFromActivityAndStoreInDatabase(mixed $dataSource, $instrumentInstance, string $xlsxFile)
    {
        $activityLogic = resolve(ActivityDataNormalizeLogic::class, [
            "dataSource" => $dataSource
        ]);

        $record = $activityLogic->normalize();
        $record["instrument_id"] = $instrumentInstance->id;
        Activity::query()
            ->updateOrCreate(["order" => $record["order"], "financial_period_id" => $record["financial_period_id"]], $record);
        Excel::write($xlsxFile, $activityLogic->getData());
    }

    private function makeXlsxFromIncomeStatementAndStoreInDatabase(mixed $dataSource, $instrumentInstance, string $xlsxFile)
    {
        $incomeStatementLogic = resolve(IncomeStatementDataNormalizeLogic::class, [
            "dataSource" => $dataSource
        ]);

        $record = $incomeStatementLogic->normalize();
        $record["instrument_id"] = $instrumentInstance->id;
        IncomeStatement::query()
            ->updateOrCreate(["financial_period_id" => $record["financial_period_id"], "order" => $record["order"]], $record);

        Excel::write($xlsxFile, $incomeStatementLogic->getData());
    }

    private function makeXlsxFromBalanceSheetAndStoreInDatabase(mixed $dataSource, $instrumentInstance, string $xlsxFile): void
    {
        $balanceSheetLogic = resolve(BalanceSheetDataNormalizeLogic::class, [
            "dataSource" => $dataSource
        ]);

        $record = $balanceSheetLogic->normalize();
        $record["instrument_id"] = $instrumentInstance->id;
        BalanceSheet::query()
            ->updateOrCreate(["financial_period_id" => $record["financial_period_id"], "order" => $record["order"]], $record);

        Excel::write($xlsxFile, $balanceSheetLogic->getData());
    }

    private function makeXlsxFromCashFlowAndStoreInDatabase(mixed $dataSource, $instrumentInstance, string $xlsxFile)
    {
        $cashFlowLogic = resolve(CashFlowDataNormalizeLogic::class, [
            "dataSource" => $dataSource
        ]);

        $record = $cashFlowLogic->normalize();
        $record["instrument_id"] = $instrumentInstance->id;
        CashFlow::query()
            ->updateOrCreate(["financial_period_id" => $record["financial_period_id"], "order" => $record["order"]], $record);

        Excel::write($xlsxFile, $cashFlowLogic->getData());
    }
}
