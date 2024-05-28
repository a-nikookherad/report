<?php

namespace Database\Seeders;

use App\Logics\Bourse\Normalize\BalanceSheetDataNormalizeLogic;
use App\Models\Bourse\BalanceSheet;
use App\Models\Bourse\Instrument;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class UpdateBalanceSheetFromJsonFilesSeeder extends Seeder
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
            $balanceSheetDataSources = glob($jsonFile . DIRECTORY_SEPARATOR . "json" . DIRECTORY_SEPARATOR . "balance_sheet" . DIRECTORY_SEPARATOR . "*");
            foreach ($balanceSheetDataSources as $balanceSheetDataSource) {
                if (!file_exists($balanceSheetDataSource)) {
                    continue;
                }
                $dataSource = json_decode(file_get_contents($balanceSheetDataSource), true);
                $balanceSheetLogic = App::make(BalanceSheetDataNormalizeLogic::class, [
                    "dataSource" => $dataSource
                ]);
                $record = $balanceSheetLogic->normalize();
                $record["instrument_id"] = $instrumentInstance->id;
                BalanceSheet::query()
                    ->updateOrCreate(["financial_period_id" => $record["financial_period_id"], "order" => $record["order"]], $record);
            }
        }
    }
}
