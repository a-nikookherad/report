<?php

namespace App\Services\DataExtractors\Activities;

use App\Models\Bourse\FinancialPeriod;
use App\Services\DataExtractors\NormalizeAbstract;
use App\Tools\PersianDate\PersianDate;
use Hekmatinasser\Verta\Facades\Verta;

class ActivityDataExtractor extends NormalizeAbstract
{
    private const TABLE_NAME_EN = "Production and sales";
    private const TABLE_NAME_FA = "تولید و فروش";
    private static array $fields = [
        "جمع فروش داخلی" => 100000,
        "جمع فروش صادراتی" => 100000,
        "جمع" => 100000
    ];

    public function __construct($dataSource)
    {
        $this->dataSource = $dataSource;

        //todo this line of code is deprecated remove later
        $this->prepareData(self::TABLE_NAME_EN, self::TABLE_NAME_FA);
    }

    public function getFields(): array
    {
        $table = $this->filterTableByName(self::TABLE_NAME_EN, self::TABLE_NAME_FA);
        $response = [];
        foreach (self::$fields as $item => $unit) {
            $response[$item] = $table->getCelByValue($item)["value"] * $unit;
        }
//        $response["order"] = (int)Verta::parse($this->dataSource["periodEndToDate"])->format("m");
        $response["order"] = (int)PersianDate::gregorianToSolar($this->dataSource["periodEndToDate"], "m");
        $response["financial_period_id"] = $this->getFinancialPeriodId();
        return $response;
    }

    public function normalize(): array
    {
        $record = [];
        $neededCol = collect([]);
        foreach ($this->data as $coordinate => $value) {
            switch ($value) {
                case "جمع فروش داخلی":
                    $domesticRowNumber = trim($coordinate, "A");
                    break;
                case "جمع فروش صادراتی":
                    $exportRowNumber = trim($coordinate, "A");
                    break;
                case "جمع":
                    $totalRowNumber = trim($coordinate, "A");
                    break;
            }
            foreach ($this->cols as $col) {
//                dd($coordinate,$col);
//                dump($coordinate,$col.$totalRowNumber);
                if (!empty($value) && !empty($totalRowNumber) && $coordinate == $col . $totalRowNumber) {
                    $neededCol->push($col);
                }
            }
        }

        //todo this happen when early 1398-12-29 because data format are different
        /*        if ($neededCol->isEmpty()) {

                }*/
        if ($neededCol->count() == 5) {
            $neededCol->forget([0, 1, 4]);
        } elseif ($neededCol->count() == 3) {
            $neededCol->forget([2]);
        }


        //فروش داخلی
        if (!empty($domesticRowNumber)) {
            $record["this_month_domestic_sales"] = $this->data[$neededCol->first() . $domesticRowNumber] * 100000;
            $record["total_domestic_sales_for_now"] = $this->data[$neededCol->last() . $domesticRowNumber] * 100000;
        }

        //فروش صادراتی
        if (!empty($exportRowNumber)) {
            $record["this_month_export_sales"] = $this->data[$neededCol->first() . $exportRowNumber] * 100000;
            $record["total_export_sales_for_now"] = $this->data[$neededCol->last() . $exportRowNumber] * 100000;
        }
        $record["this_month_sales"] = $this->data[$neededCol->first() . $totalRowNumber] * 100000;
        $record["total_sales_for_now"] = $this->data[$neededCol->last() . $totalRowNumber] * 100000;
        $record["order"] = (int)Verta::parse($this->data["periodEndToDate"])->format("m");
        $record["average_sales"] = $record["total_sales_for_now"] / $record["order"];
        $record["predict_year_sales"] = $record["total_sales_for_now"] / $record["order"] * 12;

//        $record["script"] = json_encode($this->dataSource);
        $record["financial_period_id"] = $this->getFinancialPeriodId();
        dd($record);
        return $record;
    }

    private function getFinancialPeriodId()
    {
        $start_date = Verta::parse($this->dataSource["yearEndToDate"])->subDays(365)->format("Y-m-d");
        $end_date = Verta::parse($this->dataSource["yearEndToDate"])->format("Y-m-d");
        return FinancialPeriod::query()
            ->where("solar_start_date", ">=", $start_date)
            ->where("solar_end_date", "<=", $end_date)
            ->first()?->id;
    }
}
