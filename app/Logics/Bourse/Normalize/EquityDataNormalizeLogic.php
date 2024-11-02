<?php

namespace App\Logics\Bourse\Normalize;

use App\Models\Bourse\FinancialPeriod;
use Hekmatinasser\Verta\Facades\Verta;

class EquityDataNormalizeLogic extends NormalizeAbstract
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
        dd($this->data);
        foreach ($this->data as $coordinate => $value) {
            switch ($value) {
                case "نقد حاصل از عمليات":
                case "نقد حاصل از عملیات":
                    $cashFromOperationRowNumber = trim($coordinate, "A");
                    break;
                case "جريان خالص ورود (خروج) نقد حاصل از فعاليت‌هاي سرمايه‌گذاري":
                case "جريان خالص ورود (خروج) نقد حاصل از فعاليت‌های سرمایه‌گذاری":
                    $cashFromInvestingRowNumber = trim($coordinate, "A");
                    break;
                case "دريافت‌هاي نقدي حاصل از تسهيلات":
                case "دریافت‌های نقدی حاصل از تسهيلات":
                    $receiptsFromFacilitiesRowNumber = trim($coordinate, "A");
                    break;
                case "پرداخت‌هاي نقدي بابت اصل تسهيلات":
                case "پرداخت‌های نقدی بابت اصل تسهيلات":
                    $paymentForPrincipleFacilitiesRowNumber = trim($coordinate, "A");
                    break;
                case "پرداخت‌هاي نقدي بابت سود تسهيلات":
                case "پرداخت‌های نقدی بابت سود تسهيلات":
                    $paymentForInterestFacilitiesRowNumber = trim($coordinate, "A");
                    break;
                case "پرداخت‌هاي نقدي بابت سود سهام":
                case "پرداخت‌های نقدی بابت سود سهام":
                    $dividendPaymentRowNumber = trim($coordinate, "A");
                    break;
                case "جريان خالص ورود (خروج) نقد حاصل از فعاليت‌هاي تامين مالي":
                case "جريان خالص ورود (خروج) نقد حاصل از فعالیت‌های تامين مالی":
                    $cashFromFinancingRowNumber = trim($coordinate, "A");
                    break;
                case "تاثير تغييرات نرخ ارز":
                    $foreignExchangeEffectRowNumber = trim($coordinate, "A");
                    break;
                case "خالص افزايش (کاهش) در موجودي نقد":
                case "خالص افزايش (کاهش) در موجودی نقد":
                    $netIncomeCashRowNumber = trim($coordinate, "A");
                    break;
            }
            foreach ($this->cols as $col) {
                if (!empty($value) && !empty($netIncomeCashRowNumber) && $coordinate == $col . $netIncomeCashRowNumber) {
                    $neededCol->push($col);
                }
            }
        }

        if (!empty($cashFromOperationRowNumber)) {
            $record["cash_from_operation"] = $this->data[$neededCol->first() . $cashFromOperationRowNumber] * 100000;
        }
        if (!empty($cashFromInvestingRowNumber)) {
            $record["cash_from_investing"] = $this->data[$neededCol->first() . $cashFromInvestingRowNumber] * 100000;
        }
        if (!empty($receiptsFromFacilitiesRowNumber)) {
            $record["receipts_from_facilities"] = $this->data[$neededCol->first() . $receiptsFromFacilitiesRowNumber] * 100000;
        }
        if (!empty($paymentForPrincipleFacilitiesRowNumber)) {
            $record["payments_for_principle_facilities"] = $this->data[$neededCol->first() . $paymentForPrincipleFacilitiesRowNumber] * 100000;
        }
        if (!empty($paymentForInterestFacilitiesRowNumber)) {
            $record["payments_for_interest_facilities"] = $this->data[$neededCol->first() . $paymentForInterestFacilitiesRowNumber] * 100000;
        }
        if (!empty($dividendPaymentRowNumber)) {
            $record["dividend_payments"] = $this->data[$neededCol->first() . $dividendPaymentRowNumber] * 100000;
        }
        if (!empty($cashFromFinancingRowNumber)) {
            $record["cash_from_financing"] = $this->data[$neededCol->first() . $cashFromFinancingRowNumber] * 100000;
        }
        if (!empty($foreignExchangeEffectRowNumber)) {
            $record["foreign_exchange_effect"] = $this->data[$neededCol->first() . $foreignExchangeEffectRowNumber] * 100000;
        }
        if (!empty($netIncomeCashRowNumber)) {
            $record["net_income_cash"] = $this->data[$neededCol->first() . $netIncomeCashRowNumber] * 100000;
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
