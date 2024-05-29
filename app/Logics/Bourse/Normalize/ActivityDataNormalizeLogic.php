<?php

namespace App\Logics\Bourse\Normalize;

use App\Models\Bourse\FinancialPeriod;
use Hekmatinasser\Verta\Facades\Verta;

class ActivityDataNormalizeLogic extends NormalizeAbstract
{
    public function __construct($dataSource)
    {
        $this->dataSource = $dataSource;
        $this->prepareData();
    }

    public function getData(): array
    {
        return $this->data;
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
            }
            foreach ($this->cols as $col) {
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

        if (!empty($domesticRowNumber)) {
            $record["this_month_domestic_sales"] = $this->data[$neededCol->first() . $domesticRowNumber] * 100000;
            $record["total_domestic_sales_for_now"] = $this->data[$neededCol->last() . $domesticRowNumber] * 100000;
        }
        if (!empty($exportRowNumber)) {
            $record["this_month_export_sales"] = $this->data[$neededCol->first() . $exportRowNumber] * 100000;
            $record["total_export_sales_for_now"] = $this->data[$neededCol->last() . $exportRowNumber] * 100000;
        }
        $record["this_month_sales"] = $this->data[$neededCol->first() . $totalRowNumber] * 100000;
        $record["total_sales_for_now"] = $this->data[$neededCol->last() . $totalRowNumber] * 100000;
        $record["order"] = (int)Verta::parse($this->data["periodEndToDate"])->format("m");
        $record["average_sales"] = $record["total_sales_for_now"] / $record["order"];
        $record["predict_year_sales"] = $record["total_sales_for_now"] / $record["order"] * 12;

        $record["script"] = json_encode($this->dataSource);
        $record["financial_period_id"] = $this->getFinancialPeriodId();
        return $record;
    }

    private function getFinancialPeriodId()
    {
        $start_date = Verta::parse($this->dataSource["yearEndToDate"])->subDays(365)->format("Y-m-d");
        $end_date = Verta::parse($this->dataSource["yearEndToDate"])->format("Y-m-d");
        return FinancialPeriod::query()
            ->where("solar_start_date", ">=", $start_date)
            ->where("solar_end_date", "<=", $end_date)
            ->first()->id;
    }
}
