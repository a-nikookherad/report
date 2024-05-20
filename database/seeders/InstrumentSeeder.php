<?php

namespace Database\Seeders;

use App\Models\Bourse\FinancialPeriod;
use App\Models\Bourse\Group;
use App\Models\Bourse\Industry;
use App\Models\Bourse\Instrument;
use Hekmatinasser\Verta\Facades\Verta;
use Illuminate\Database\Seeder;

class InstrumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $instruments = [
            [
                "name" => "فولاد مبارکه اصفهان",
                "slug" => "folad",
                "symbol" => "فولاد",
                "mofid_url" => "https://api-mts.orbis.easytrader.ir/chart/api/datafeed/history?symbol=IRO1FOLD0001%3A1&resolution=1D&from=1669334400&to=1716163200&countback=388",
                "financial_period" => "1398-12-29",
                "group_id" => Group::where("name", "فلزات اساسی")->first()->id ?? null,
                "industry_id" => Industry::where("name", "فلزات اساسی")->first()->id ?? null,
                "description" => "",
            ],
//            [
//                "name" => "پویا زرکان آق دره",
//                "slug" => "fazar",
//                "symbol" => "فزر",
//                "financial_period" => "1398-09-30",
//                "group_id" => null,
//                "industry_id" => Industry::where("name", "استخراج کانه های فلزی")->first()->id,
//                "description" => "",
//            ],
            [
                "name" => "ملی صنایع مس ایران",
                "slug" => "fameli",
                "symbol" => "فملی",
                "mofid_url" => "Request URL: https://api-mts.orbis.easytrader.ir/chart/api/datafeed/history?symbol=IRO1MSMI0001%3A1&resolution=1D&from=1645142400&to=1716163200&countback=588",
                "financial_period" => "1398-12-29",
                "group_id" => Group::where("name", "فلزات اساسی")->first()->id,
                "industry_id" => Industry::where("name", "فلزات اساسی")->first()->id,
                "description" => "",
            ],
            [
                "name" => "ایران خودرو دیزل",
                "slug" => "khavar",
                "symbol" => "خاور",
                "financial_period" => "1398-12-29",
                "group_id" => null,
                "industry_id" => Industry::where("name", "سایر صنایع")->first()->id,
                "description" => "",
            ],
            [
                "name" => "مس شهید باهنر",
                "slug" => "fabahonar",
                "symbol" => "فباهنر",
                "financial_period" => "1398-12-29",
                "group_id" => Group::where("name", "فلزات اساسی")->first()->id,
                "industry_id" => Industry::where("name", "فلزات اساسی")->first()->id,
                "description" => "",
            ],
            [
                "name" => "پارس فولاد سبزوار",
                "slug" => "fesabzevar",
                "symbol" => "فسبزوار",
                "financial_period" => "1398-09-30",
                "group_id" => Group::where("name", "فلزات اساسی")->first()->id,
                "industry_id" => Industry::where("name", "فلزات اساسی")->first()->id,
                "description" => "",
            ],
            [
                "name" => "فولاد هرمزگان جنوب",
                "slug" => "hormoz",
                "symbol" => "هرمز",
                "financial_period" => "1398-12-29",
                "group_id" => Group::where("name", "فلزات اساسی")->first()->id,
                "industry_id" => Industry::where("name", "فلزات اساسی")->first()->id,
                "description" => "",
            ],
        ];

        foreach ($instruments as $instrument) {
            $financialFolder = config("financial.folder") . DIRECTORY_SEPARATOR . $instrument["slug"];
            if (!file_exists(storage_path($financialFolder))) {
                mkdir(storage_path($financialFolder . DIRECTORY_SEPARATOR . "json"), "0777", true);
                mkdir(storage_path($financialFolder . DIRECTORY_SEPARATOR . "xlsx"), "0777", true);
            }
            $financialPeriod = $instrument["financial_period"];
            unset($instrument["financial_period"]);

            //Insert instrument to database
            $instrumentInstance = Instrument::query()
                ->updateOrCreate(["symbol" => $instrument["symbol"]], $instrument);

            for ($i = 0; $i <= 6; $i++) {
                if (Verta::parse($financialPeriod)->addYears($i)->format("m-d") == "12-29") {
                    $startSolar = Verta::parse($financialPeriod)->addYears($i)->startYear()->format("Y-m-d");
                    $endSolar = Verta::parse($financialPeriod)->addYears($i)->endYear()->format("Y-m-d");
                } else {
                    $endSolar = Verta::parse($financialPeriod)->addYears($i)->format("Y-m-d");
                    $startSolar = Verta::parse($endSolar)->subDays(364)->format("Y-m-d");
                }
                $date = [
                    "solar_start_date" => $startSolar,
                    "solar_end_date" => $endSolar,
                    "start_date" => Verta::parse($startSolar)->datetime()->format("Y-m-d"),
                    "end_date" => Verta::parse($endSolar)->datetime()->format("Y-m-d"),
                    "share_count" => null,
                    "instrument_id" => $instrumentInstance->id,
                ];
                FinancialPeriod::query()
                    ->updateOrCreate(["solar_end_date" => $date["solar_end_date"], "instrument_id" => $date["instrument_id"]], $date);
            }
        }
    }
}
