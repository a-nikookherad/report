<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInstrumentReqquest;
use App\Models\FinancialPeriod;
use App\Models\Group;
use App\Models\Industry;
use App\Models\Instrument;
use Hekmatinasser\Verta\Facades\Verta;
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

    public function add()
    {
        $groups = Group::query()
            ->get();
        $industries = Industry::query()
            ->get();
        return view('contents.instruments.add', compact("groups", "industries"));
    }

    public function store(StoreInstrumentReqquest $request)
    {
        try {
            if (!$request->ajax()) {
                throw new \Exception("this request not an ajax request");
            }

            DB::beginTransaction();
            $this->checkOrCreateFolder($request->folder_name);

            //Insert instrument to database
            $instrumentInstance = Instrument::query()
                ->updateOrCreate(["symbol" => $request->symbol], $request->except(["_token", "folder_name", "financial_period"]));

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
        return view("contents.instruments.add_info");
    }

    public function storeInfo()
    {
        dd("storeinfo");
    }

    private function checkOrCreateFolder(string|null $path)
    {
        $financialFolder = config("financial.folder") . DIRECTORY_SEPARATOR . $path;
        if (!file_exists(storage_path($financialFolder))) {
            mkdir(storage_path($financialFolder . DIRECTORY_SEPARATOR . "ajax"), "0777", true);
            mkdir(storage_path($financialFolder . DIRECTORY_SEPARATOR . "xlsx"), "0777", true);
        }
    }

    private function addFinancialPeriods($instrument_id, mixed $financial_period)
    {
        for ($i = 0; $i <= 6; $i++) {
            $date = Verta::parse($financial_period)->addYears($i)->format("Y-m-d");
            $date = [
                "solar_start_date" => Verta::parse($date)->subDays(364)->format("Y-m-d"),
                "solar_end_date" => $date,
                "start_date" => Verta::parse($date)->subDays(364)->datetime()->format("Y-m-d"),
                "end_date" => Verta::parse($date)->datetime()->format("Y-m-d"),
                "share_count" => null,
                "instrument_id" => $instrument_id,
            ];
            FinancialPeriod::query()
                ->updateOrCreate(["solar_end_date" => $date["solar_end_date"]], $date);
        }
    }
}
