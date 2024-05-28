<?php

namespace App\Logics\Bourse\Normalize;

use App\Models\Bourse\FinancialPeriod;
use Hekmatinasser\Verta\Facades\Verta;

class BalanceSheetDataNormalizeLogic extends NormalizeAbstract
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
                case "جمع دارايي‌هاي غيرجاري":
                case "جمع دارایی‌های غیرجاری":
                    $totalNonCurrentAssetsRowNumber = trim($coordinate, "A");
                    break;
                case "دريافتني‌هاي تجاري و ساير دريافتني‌ها":
                case "دریافتنی‌های تجاری و سایر دریافتنی‌ها":
                    $receivableClaimRowNumber = trim($coordinate, "A");
                    break;
                case "جمع دارايي‌هاي جاري":
                case "جمع دارایی‌های جاری":
                    $totalCurrentAssetsRowNumber = trim($coordinate, "A");
                    break;
                case "جمع دارايي‌ها":
                case "جمع دارایی‌ها":
                    $totalAssetsRowNumber = trim($coordinate, "A");
                    break;
                case "سرمايه":
                    $fundRowNumber = trim($coordinate, "A");
                    break;
                case "سود(زيان) انباشته":
                case "سود (زيان) انباشته":
                    $accumulateProfitRowNumber = trim($coordinate, "A");
                    break;
                case "جمع حقوق مالکانه":
                    $totalEquityRowNumber = trim($coordinate, "A");
                    break;
                case "جمع بدهي‌هاي غيرجاري":
                case "جمع بدهی‌های غیرجاری":
                    $totalNonCurrentLiabilitiesRowNumber = trim($coordinate, "A");
                    break;
                case "جمع بدهي‌هاي جاري":
                case "جمع بدهی‌های جاری":
                    $totalCurrentLiabilitiesRowNumber = trim($coordinate, "A");
                    break;
                case "جمع بدهي‌ها":
                case "جمع بدهی‌ها":
                    $totalLiabilitiesRowNumber = trim($coordinate, "A");
                    break;

            }
            foreach ($this->cols as $col) {
                if (!empty($value) && !empty($totalAssetsRowNumber) && $coordinate == $col . $totalAssetsRowNumber) {
                    $neededCol->push($col);
                }
            }
        }

        if (!empty($totalNonCurrentAssetsRowNumber)) {
            $record["total_non_current_assets"] = $this->data[$neededCol->first() . $totalNonCurrentAssetsRowNumber] * 100000;
        }
        if (!empty($receivableClaimRowNumber)) {
            $record["receivable_claim"] = $this->data[$neededCol->first() . $receivableClaimRowNumber] * 100000;
        }
        if (!empty($totalCurrentAssetsRowNumber)) {
            $record["total_current_assets"] = $this->data[$neededCol->first() . $totalCurrentAssetsRowNumber] * 100000;
        }
        if (!empty($totalAssetsRowNumber)) {
            $record["total_assets"] = $this->data[$neededCol->first() . $totalAssetsRowNumber] * 100000;
        }
        if (!empty($fundRowNumber)) {
            $record["fund"] = $this->data[$neededCol->first() . $fundRowNumber] * 100000;
        }
        if (!empty($accumulateProfitRowNumber)) {
            $record["accumulated_profit"] = $this->data[$neededCol->first() . $accumulateProfitRowNumber] * 100000;
        }
        if (!empty($totalEquityRowNumber)) {
            $record["total_equity"] = $this->data[$neededCol->first() . $totalEquityRowNumber] * 100000;
        }
        if (!empty($totalNonCurrentLiabilitiesRowNumber)) {
            $record["total_non_current_liabilities"] = $this->data[$neededCol->first() . $totalNonCurrentLiabilitiesRowNumber] * 100000;
        }
        if (!empty($totalCurrentLiabilitiesRowNumber)) {
            $record["total_current_liabilities"] = $this->data[$neededCol->first() . $totalCurrentLiabilitiesRowNumber] * 100000;
        }
        if (!empty($totalLiabilitiesRowNumber)) {
            $record["total_liabilities"] = $this->data[$neededCol->first() . $totalLiabilitiesRowNumber] * 100000;
        }

        $record["order"] = (int)Verta::parse($this->data["periodEndToDate"])->format("m");
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
