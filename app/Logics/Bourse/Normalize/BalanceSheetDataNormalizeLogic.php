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
                case "درآمدهاي عملياتي":
                case "درآمدهای عملیاتی":
                    $totalRevenueRowNumber = trim($coordinate, "A");
                    break;
                case "بهاى تمام شده درآمدهاي عملياتي":
                case "بهاى تمام شده درآمدهای عملیاتی":
                    $costOfRevenueRowNumber = trim($coordinate, "A");
                    break;
                case "سود(زيان) ناخالص":
                case "سود (زيان) ناخالص":
                    $grossProfitRowNumber = trim($coordinate, "A");
                    break;
                case "هزينه‏ هاى فروش، ادارى و عمومى":
                case "هزينه‏‌هاى فروش، ادارى و عمومى":
                    $operationExpensesRowNumber = trim($coordinate, "A");
                    break;
                case "ساير درآمدها":
                case "سایر درآمدها و هزینه‌های عملیاتی":
                    $otherOperatingIncomeRowNumber = trim($coordinate, "A");
                    break;
                case "سود(زيان) عملياتى":
                case "سود (زيان) عملياتي":
                    $operatingIncomeRowNumber = trim($coordinate, "A");
                    break;
                case "هزينه‏‌هاى مالى":
                case "هزينه‏ هاى مالى":
                    $financialCostRowNumber = trim($coordinate, "A");
                    break;
                case "ساير درآمدها و هزينه ‏هاى غيرعملياتى":
                    $otherIncomeRowNumber1 = trim($coordinate, "A");
                    break;
                case "سایر درآمدها و هزینه‌های غیرعملیاتی- درآمد سرمایه‌گذاری‌ها":
                    $otherIncomeRowNumber2 = trim($coordinate, "A");
                    break;
                case "سایر درآمدها و هزینه‌های غیرعملیاتی- اقلام متفرقه":
                    $otherIncomeRowNumber3 = trim($coordinate, "A");
                    break;
                case "سال جاری":
                case "سال جاري":
                    $taxRowNumber = trim($coordinate, "A");
                    break;
                case "سود(زيان) خالص":
                case "سود (زيان) خالص":
                    $netIncomeRowNumber = trim($coordinate, "A");
                    break;
                case "سرمايه":
                case "سرمایه":
                    $fundRowNumber = trim($coordinate, "A");
                    break;
            }
            foreach ($this->cols as $col) {
                if (!empty($value) && !empty($fundRowNumber) && $coordinate == $col . $fundRowNumber) {
                    $neededCol->push($col);
                }
            }
        }

        if (!empty($fundRowNumber)) {
            $record["fund"] = $this->data[$neededCol->first() . $fundRowNumber] * 100000;
        }
        if (!empty($totalRevenueRowNumber)) {
            $record["total_revenue"] = $this->data[$neededCol->first() . $totalRevenueRowNumber] * 100000;
        }
        if (!empty($costOfRevenueRowNumber)) {
            $record["cost_of_revenue"] = $this->data[$neededCol->first() . $costOfRevenueRowNumber] * 100000;
        }
        if (!empty($grossProfitRowNumber)) {
            $record["gross_profit"] = $this->data[$neededCol->first() . $grossProfitRowNumber] * 100000;
        }
        if (!empty($operationExpensesRowNumber)) {
            $record["operation_expenses"] = $this->data[$neededCol->first() . $operationExpensesRowNumber] * 100000;
        }
        if (!empty($otherOperatingIncomeRowNumber)) {
            $record["other_operating_income"] = $this->data[$neededCol->first() . $otherOperatingIncomeRowNumber] * 100000;
        }
        if (!empty($operatingIncomeRowNumber)) {
            $record["operating_income"] = $this->data[$neededCol->first() . $operatingIncomeRowNumber] * 100000;
        }
        if (!empty($financialCostRowNumber)) {
            $record["financial_cost"] = $this->data[$neededCol->first() . $financialCostRowNumber] * 100000;
        }
        if (!empty($otherIncomeRowNumber1) || !empty($otherIncomeRowNumber2) || !empty($otherIncomeRowNumber3)) {
            $otherIncome = 0;
            !empty($otherIncomeRowNumber1) ? $otherIncome += $this->data[$neededCol->first() . $otherIncomeRowNumber1] : null;
            !empty($otherIncomeRowNumber2) ? $otherIncome += $this->data[$neededCol->first() . $otherIncomeRowNumber2] : null;
            !empty($otherIncomeRowNumber3) ? $otherIncome += $this->data[$neededCol->first() . $otherIncomeRowNumber3] : null;
            $record["other_income"] = $otherIncome * 100000;
        }
        if (!empty($taxRowNumber)) {
            $record["tax"] = $this->data[$neededCol->first() . $taxRowNumber] * 100000;
        }
        if (!empty($netIncomeRowNumber)) {
            $record["net_profit"] = $this->data[$neededCol->first() . $netIncomeRowNumber] * 100000;
        }
        $record["order"] = (int)Verta::parse($this->data["periodEndToDate"])->format("m");
        $record["script"] = json_encode($this->dataSource);

        $record = $this->updateFinancialPeriod($record);
        return $record;
    }

    private function updateFinancialPeriod(array $record): array
    {
        $start_date = Verta::parse($this->dataSource["yearEndToDate"])->subDays(365)->format("Y-m-d");
        $end_date = Verta::parse($this->dataSource["yearEndToDate"])->format("Y-m-d");
        $financialPeriod = FinancialPeriod::query()
            ->where("solar_start_date", ">=", $start_date)
            ->where("solar_end_date", "<=", $end_date)
            ->first();
        if ($financialPeriod->share_count < ($record["fund"] / 100)) {
            $financialPeriod->share_count = $record["fund"] / 100;
            $financialPeriod->save();
        }
        $record["financial_period_id"] = $financialPeriod->id;
        return $record;
    }
}
