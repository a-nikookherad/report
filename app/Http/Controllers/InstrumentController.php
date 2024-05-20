<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstrumentInfoStoreRequest;
use App\Http\Requests\StoreInstrumentRequest;
use App\Models\Bourse\Activity;
use App\Models\Bourse\BalanceSheet;
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

class InstrumentController extends Controller
{
    public function list()
    {
        $instruments = Instrument::query()
            ->with("industry")
            ->get();
        return view('contents.instruments.list', compact("instruments"));
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
        if ($res->s == "ok") {
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
        $earn = $incomeStatement->net_income / $incomeStatement->order * 12;

        $ratio = [];
        //calculate P/E
        $ratio["P/E"] = number_format($price / $earn,1);

        //calculate P/S
        $activity = Activity::query()
            ->where("instrument_id", $instrumentInstance->id)
            ->orderBy("id", "desc")
            ->first();
        $ratio["P/S"] = number_format($price / $activity->predict_year_sales,1);

        //calculate P/A
        $ratio["P/A"] = number_format($price / $balanceSheet->total_assets,1);

        //calculate P/B
        $ratio["P/B"] = number_format($price / $balanceSheet->total_equity,1);

        //calculate R/A
        $ratio["R/A"] = number_format($balanceSheet->receivable_claim / $balanceSheet->total_assets * 100) . "%";

        //calculate E/E or ROE
        $ratio["ROE"] = number_format($earn / (($lastYearLatestBalanceSheet->total_equity * 2 + $earn) / 2) * 100) . "%";

        //calculate E/E or ROE
        $ratio["ROA"] = number_format($earn / (($lastYearLatestBalanceSheet->total_assets * 2 + $earn) / 2) * 100) . "%";

        //calculate IRR/XUD
        $gold = Gold::query()
            ->orderBy("id", "desc")
            ->first();
        $ratio["INS/XUD"] = ($price / $gold->close / 1000000) . " T";

        //calculate IRR/USD
        $dollar = Dollar::query()
            ->orderBy("id", "desc")
            ->first();
        $ratio["TMM/USD"] = number_format($price / ($dollar->close / 10), null, null, ",");


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
            $data = [];
            $cols = [
                "B",
                "C",
                "D",
                "E",
                "F",
                "G",
                "H",
                "I",
                "J",
                "K",
                "L",
                "M",
                "N",
                "O",
                "P",
                "Q",
                "R",
                "S",
                "T",
                "U",
                "V",
                "W",
                "X",
                "Y",
                "Z",
            ];

            if ($request->financial_report_type == "activity") {
                $this->makeXlsxFromActivityAndStoreInDatabase($dataSource, $data, $xlsxFile, $cols, $instrumentInstance);
            } elseif ($request->financial_report_type == "balance_sheet") {
                $this->makeXlsxFromBalanceSheetAndStoreInDatabase($dataSource, $data, $xlsxFile, $cols, $instrumentInstance);
            } elseif ($request->financial_report_type == "income_statement") {
                $this->makeXlsxFromIncomeStatementAndStoreInDatabase($dataSource, $data, $xlsxFile, $cols, $instrumentInstance);
            } elseif ($request->financial_report_type == "cash_flow") {

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

    private function makeXlsxFromActivityAndStoreInDatabase(mixed $dataSource, array $data, string $xlsxFile, array $cols, $instrumentInstance)
    {
        foreach ($dataSource["sheets"][0]["tables"][0]["cells"] as $cel) {
            switch ($cel["value"]) {
                case "جمع فروش داخلی":
                    $domesticRowNumber = trim($cel["address"], "A");
                    break;
                case "جمع فروش صادراتی":
                    $exportRowNumber = trim($cel["address"], "A");
                    break;
                case "جمع":
                    $totalRowNumber = trim($cel["address"], "A");
            }
            $data[$cel["address"]] = $cel["value"];
        }
        Excel::write($xlsxFile, $data);

        //insert into database
        $neededCol = collect([]);
        foreach ($data as $coordinate => $value) {
            foreach ($cols as $col) {
                if (!empty($value) && $coordinate == $col . $totalRowNumber) {
                    $neededCol->push($col);
                }
            }
        }

        if ($neededCol->count() == 5) {
            $neededCol->forget([0, 1, 4]);
        } elseif ($neededCol->count() == 3) {
            $neededCol->forget([2]);
        }

        $start_date = Verta::parse($dataSource["yearEndToDate"])->subDays(365)->format("Y-m-d");
        $end_date = Verta::parse($dataSource["yearEndToDate"])->format("Y-m-d");
        $financialPeriod = FinancialPeriod::query()
            ->where("solar_start_date", ">=", $start_date)
            ->where("solar_end_date", "<=", $end_date)
            ->first();

        $record = [];
        $record["instrument_id"] = $instrumentInstance->id;
        $record["financial_period_id"] = $financialPeriod->id;
        $record["script"] = json_encode($dataSource);
        if ($domesticRowNumber) {
            $record["this_month_domestic_sales"] = $data[$neededCol->first() . $domesticRowNumber] * 100000;
            $record["total_domestic_sales_for_now"] = $data[$neededCol->last() . $domesticRowNumber] * 100000;
        }
        if ($exportRowNumber) {
            $record["this_month_export_sales"] = $data[$neededCol->first() . $exportRowNumber] * 100000;
            $record["total_export_sales_for_now"] = $data[$neededCol->last() . $exportRowNumber] * 100000;
        }

        $record["this_month_sales"] = $data[$neededCol->first() . $totalRowNumber] * 100000;
        $record["total_sales_for_now"] = $data[$neededCol->last() . $totalRowNumber] * 100000;

        $record["order"] = (int)Verta::parse($dataSource["periodEndToDate"])->format("m");

        $record["average_sales"] = $record["total_sales_for_now"] / $record["order"];
        $record["predict_year_sales"] = $record["total_sales_for_now"] / $record["order"] * 12;
        Activity::query()
            ->updateOrCreate(["order" => $record["order"], "financial_period_id" => $record["financial_period_id"]], $record);
    }

    private function makeXlsxFromIncomeStatementAndStoreInDatabase(mixed $dataSource, array $data, string $xlsxFile, array $cols, $instrumentInstance)
    {
        foreach ($dataSource["sheets"][0]["tables"][0]["cells"] as $cel) {
            switch ($cel["value"]) {
                case "درآمدهاي عملياتي":
                case "درآمدهای عملیاتی":
                    $totalRevenueRowNumber = trim($cel["address"], "A");
                    break;
                case "بهاى تمام شده درآمدهاي عملياتي":
                case "بهاى تمام شده درآمدهای عملیاتی":
                    $costOfRevenueRowNumber = trim($cel["address"], "A");
                    break;
                case "سود(زيان) ناخالص":
                case "سود (زيان) ناخالص":
                    $grossProfitRowNumber = trim($cel["address"], "A");
                    break;
                case "هزينه‏ هاى فروش، ادارى و عمومى":
                case "هزينه‏‌هاى فروش، ادارى و عمومى":
                    $operationExpensesRowNumber = trim($cel["address"], "A");
                    break;
                case "ساير درآمدها":
                case "سایر درآمدها و هزینه‌های عملیاتی":
                    $otherOperatingIncomeRowNumber = trim($cel["address"], "A");
                    break;
                case "سود(زيان) عملياتى":
                case "سود (زيان) عملياتي":
                    $operatingIncomeRowNumber = trim($cel["address"], "A");
                    break;
                case "هزينه‏‌هاى مالى":
                case "هزينه‏ هاى مالى":
                    $financialCostRowNumber = trim($cel["address"], "A");
                    break;
                case "ساير درآمدها و هزينه ‏هاى غيرعملياتى":
                    $otherIncomeRowNumber1 = trim($cel["address"], "A");
                    break;
                case "سایر درآمدها و هزینه‌های غیرعملیاتی- درآمد سرمایه‌گذاری‌ها":
                    $otherIncomeRowNumber2 = trim($cel["address"], "A");
                    break;
                case "سایر درآمدها و هزینه‌های غیرعملیاتی- اقلام متفرقه":
                    $otherIncomeRowNumber3 = trim($cel["address"], "A");
                    break;
                case "سال جاری":
                case "سال جاري":
                    $taxRowNumber = trim($cel["address"], "A");
                    break;
                case "سود(زيان) خالص":
                case "سود (زيان) خالص":
                    $netIncomeRowNumber = trim($cel["address"], "A");
                    break;
                case "سرمايه":
                case "سرمایه":
                    $fundRowNumber = trim($cel["address"], "A");
                    break;
            }
            $data[$cel["address"]] = $cel["value"];
        }
        Excel::write($xlsxFile, $data);

        $neededCol = collect([]);
        foreach ($data as $coordinate => $value) {
            foreach ($cols as $col) {
                if (!empty($value) && $coordinate == $col . $fundRowNumber) {
                    $neededCol->push($col);
                }
            }
        }

        $start_date = Verta::parse($dataSource["yearEndToDate"])->subDays(365)->format("Y-m-d");
        $end_date = Verta::parse($dataSource["yearEndToDate"])->format("Y-m-d");
        $financialPeriod = FinancialPeriod::query()
            ->where("solar_start_date", ">=", $start_date)
            ->where("solar_end_date", "<=", $end_date)
            ->first();

        $record = [];
        if (!empty($fundRowNumber)) {
            $record["fund"] = $data[$neededCol->first() . $fundRowNumber] * 100000;
            $financialPeriod->share_count = $record["fund"] / 100;
            $financialPeriod->save();
        }

        if (!empty($totalRevenueRowNumber)) {
            $record["total_revenue"] = $data[$neededCol->first() . $totalRevenueRowNumber] * 100000;
        }
        if (!empty($costOfRevenueRowNumber)) {
            $record["cost_of_revenue"] = $data[$neededCol->first() . $costOfRevenueRowNumber] * 100000;
        }
        if (!empty($grossProfitRowNumber)) {
            $record["gross_profit"] = $data[$neededCol->first() . $grossProfitRowNumber] * 100000;
        }
        if (!empty($operationExpensesRowNumber)) {
            $record["operation_expenses"] = $data[$neededCol->first() . $operationExpensesRowNumber] * 100000;
        }
        if (!empty($otherOperatingIncomeRowNumber)) {
            $record["other_operating_income"] = $data[$neededCol->first() . $otherOperatingIncomeRowNumber] * 100000;
        }
        if (!empty($operatingIncomeRowNumber)) {
            $record["operating_income"] = $data[$neededCol->first() . $operatingIncomeRowNumber] * 100000;
        }
        if (!empty($financialCostRowNumber)) {
            $record["financial_cost"] = $data[$neededCol->first() . $financialCostRowNumber] * 100000;
        }
        if (!empty($otherIncomeRowNumber1) || !empty($otherIncomeRowNumber2) || !empty($otherIncomeRowNumber3)) {
            $otherIncome = 0;
            !empty($otherIncomeRowNumber1) ? $otherIncome += $data[$neededCol->first() . $otherIncomeRowNumber1] : null;
            !empty($otherIncomeRowNumber2) ? $otherIncome += $data[$neededCol->first() . $otherIncomeRowNumber2] : null;
            !empty($otherIncomeRowNumber3) ? $otherIncome += $data[$neededCol->first() . $otherIncomeRowNumber3] : null;
            $record["other_income"] = $otherIncome * 100000;
        }
        if (!empty($taxRowNumber)) {
            $record["tax"] = $data[$neededCol->first() . $taxRowNumber] * 100000;
        }
        if (!empty($netIncomeRowNumber)) {
            $record["net_income"] = $data[$neededCol->first() . $netIncomeRowNumber] * 100000;
        }


        $record["instrument_id"] = $instrumentInstance->id;
        $record["financial_period_id"] = $financialPeriod->id;
        $record["order"] = (int)Verta::parse($dataSource["periodEndToDate"])->format("m");
        $record["script"] = json_encode($dataSource);
        IncomeStatement::query()
            ->updateOrCreate(["financial_period_id" => $record["financial_period_id"], "order" => $record["order"]], $record);
    }

    private function makeXlsxFromBalanceSheetAndStoreInDatabase(mixed $dataSource, array $data, string $xlsxFile, array $cols, $instrumentInstance): void
    {
        foreach ($dataSource["sheets"][0]["tables"][0]["cells"] as $cel) {
            switch ($cel["value"]) {
                case "جمع دارايي‌هاي غيرجاري":
                case "جمع دارایی‌های غیرجاری":
                    $totalNonCurrentAssetsRowNumber = trim($cel["address"], "A");
                    break;
                case "دريافتني‌هاي تجاري و ساير دريافتني‌ها":
                case "دریافتنی‌های تجاری و سایر دریافتنی‌ها":
                    $receivableClaimRowNumber = trim($cel["address"], "A");
                    break;
                case "جمع دارايي‌هاي جاري":
                case "جمع دارایی‌های جاری":
                    $totalCurrentAssetsRowNumber = trim($cel["address"], "A");
                    break;
                case "جمع دارايي‌ها":
                case "جمع دارایی‌ها":
                    $totalAssetsRowNumber = trim($cel["address"], "A");
                    break;
                case "سرمايه":
                    $fundRowNumber = trim($cel["address"], "A");
                    break;
                case "سود(زيان) انباشته":
                case "سود (زيان) انباشته":
                    $accumulateProfitRowNumber = trim($cel["address"], "A");
                    break;
                case "جمع حقوق مالکانه":
                    $totalEquityRowNumber = trim($cel["address"], "A");
                    break;
                case "جمع بدهي‌هاي غيرجاري":
                case "جمع بدهی‌های غیرجاری":
                    $totalNonCurrentLiabilitiesRowNumber = trim($cel["address"], "A");
                    break;
                case "جمع بدهي‌هاي جاري":
                case "جمع بدهی‌های جاری":
                    $totalCurrentLiabilitiesRowNumber = trim($cel["address"], "A");
                    break;
                case "جمع بدهي‌ها":
                case "جمع بدهی‌ها":
                    $totalLiabilitiesRowNumber = trim($cel["address"], "A");
                    break;

            }
            $data[$cel["address"]] = $cel["value"];
        }
        Excel::write($xlsxFile, $data);

        $neededCol = collect([]);
        foreach ($data as $coordinate => $value) {
            foreach ($cols as $col) {
                if (!empty($value) && $coordinate == $col . $totalAssetsRowNumber) {
                    $neededCol->push($col);
                }
            }
        }

        $record = [];
        if (!empty($totalNonCurrentAssetsRowNumber)) {
            $record["total_non_current_assets"] = $data[$neededCol->first() . $totalNonCurrentAssetsRowNumber] * 100000;
        }
        if (!empty($receivableClaimRowNumber)) {
            $record["receivable_claim"] = $data[$neededCol->first() . $receivableClaimRowNumber] * 100000;
        }
        if (!empty($totalCurrentAssetsRowNumber)) {
            $record["total_current_assets"] = $data[$neededCol->first() . $totalCurrentAssetsRowNumber] * 100000;
        }
        if (!empty($totalAssetsRowNumber)) {
            $record["total_assets"] = $data[$neededCol->first() . $totalAssetsRowNumber] * 100000;
        }
        if (!empty($fundRowNumber)) {
            $record["fund"] = $data[$neededCol->first() . $fundRowNumber] * 100000;
        }
        if (!empty($accumulateProfitRowNumber)) {
            $record["accumulated_profit"] = $data[$neededCol->first() . $accumulateProfitRowNumber] * 100000;
        }
        if (!empty($totalEquityRowNumber)) {
            $record["total_equity"] = $data[$neededCol->first() . $totalEquityRowNumber] * 100000;
        }
        if (!empty($totalNonCurrentLiabilitiesRowNumber)) {
            $record["total_non_current_liabilities"] = $data[$neededCol->first() . $totalNonCurrentLiabilitiesRowNumber] * 100000;
        }
        if (!empty($totalCurrentLiabilitiesRowNumber)) {
            $record["total_current_liabilities"] = $data[$neededCol->first() . $totalCurrentLiabilitiesRowNumber] * 100000;
        }
        if (!empty($totalLiabilitiesRowNumber)) {
            $record["total_liabilities"] = $data[$neededCol->first() . $totalLiabilitiesRowNumber] * 100000;
        }

        $start_date = Verta::parse($dataSource["yearEndToDate"])->subDays(365)->format("Y-m-d");
        $end_date = Verta::parse($dataSource["yearEndToDate"])->format("Y-m-d");
        $financialPeriod = FinancialPeriod::query()
            ->where("solar_start_date", ">=", $start_date)
            ->where("solar_end_date", "<=", $end_date)
            ->first();

        $record["instrument_id"] = $instrumentInstance->id;
        $record["financial_period_id"] = $financialPeriod->id;
        $record["order"] = (int)Verta::parse($dataSource["periodEndToDate"])->format("m");
        $record["script"] = json_encode($dataSource);
        BalanceSheet::query()
            ->updateOrCreate(["financial_period_id" => $record["financial_period_id"], "order" => $record["order"]], $record);
    }
}
