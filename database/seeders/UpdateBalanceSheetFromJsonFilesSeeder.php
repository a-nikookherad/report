<?php

namespace Database\Seeders;

use App\Logics\Bourse\Normalize\IncomeStatementDataNormalizeLogic;
use App\Models\Bourse\IncomeStatement;
use App\Models\Bourse\Instrument;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            $incomeStatementsDataSources = glob($jsonFile . DIRECTORY_SEPARATOR . "json" . DIRECTORY_SEPARATOR . "income_statement" . DIRECTORY_SEPARATOR . "*");
            foreach ($incomeStatementsDataSources as $incomeStatementsDataSource) {
                if (!file_exists($incomeStatementsDataSource)) {
                    continue;
                }
                $dataSource = json_decode(file_get_contents($incomeStatementsDataSource), true);
                $incomeStatementLogic = App::make(IncomeStatementDataNormalizeLogic::class, [
                    "dataSource" => $dataSource
                ]);
                $record = $incomeStatementLogic->normalize();
                $record["instrument_id"] = $instrumentInstance->id;
                IncomeStatement::query()
                    ->updateOrCreate(["financial_period_id" => $record["financial_period_id"], "order" => $record["order"]], $record);
            }
        }
    }
}
