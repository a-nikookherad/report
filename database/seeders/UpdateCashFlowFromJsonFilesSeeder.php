<?php

namespace Database\Seeders;

use App\Logics\Bourse\Normalize\CashFlowDataNormalizeLogic;
use App\Models\Bourse\CashFlow;
use App\Models\Bourse\Instrument;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class UpdateCashFlowFromJsonFilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $jsonFiles = glob(storage_path(config("financial.folder") . DIRECTORY_SEPARATOR . "*"));
        foreach ($jsonFiles as $jsonFile) {
            $instrumentInstance = Instrument::query()
                ->where("slug", basename($jsonFile))
                ->first();
            if (!$instrumentInstance) {
                continue;
            }
            $cashFlowDataSources = glob($jsonFile . DIRECTORY_SEPARATOR . "json" . DIRECTORY_SEPARATOR . "cash_flow" . DIRECTORY_SEPARATOR . "*");
            foreach ($cashFlowDataSources as $cashFlowDataSource) {
                if (!file_exists($cashFlowDataSource)) {
                    continue;
                }
                $dataSource = json_decode(file_get_contents($cashFlowDataSource), true);
                $cashFlowLogic = App::make(CashFlowDataNormalizeLogic::class, [
                    "dataSource" => $dataSource
                ]);
                $record = $cashFlowLogic->normalize();
                $record["instrument_id"] = $instrumentInstance->id;
                CashFlow::query()
                    ->updateOrCreate(["financial_period_id" => $record["financial_period_id"], "order" => $record["order"]], $record);
            }
        }
    }
}
