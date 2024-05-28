<?php

namespace Database\Seeders;

use App\Logics\Bourse\Normalize\ActivityDataNormalizeLogic;
use App\Models\Bourse\Activity;
use App\Models\Bourse\Instrument;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class UpdateActivitiesFromJsonFilesSeeder extends Seeder
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
            $activityDataSources = glob($jsonFile . DIRECTORY_SEPARATOR . "json" . DIRECTORY_SEPARATOR . "activity" . DIRECTORY_SEPARATOR . "*");
            foreach ($activityDataSources as $activityDataSource) {
                if (!file_exists($activityDataSource)) {
                    continue;
                }
                $dataSource = json_decode(file_get_contents($activityDataSource), true);
                $activityLogic = App::make(ActivityDataNormalizeLogic::class, [
                    "dataSource" => $dataSource
                ]);
                $record = $activityLogic->normalize();
                $record["instrument_id"] = $instrumentInstance->id;
                Activity::query()
                    ->updateOrCreate(["financial_period_id" => $record["financial_period_id"], "order" => $record["order"]], $record);
            }
        }
    }
}
