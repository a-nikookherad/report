<?php

namespace App\Observers;

use App\Models\Bourse\FinancialStatementsAnalysis;
use App\Models\Bourse\IncomeStatement;
use App\Models\Instruments\Gold;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\Date;

class IncomeStatementObserver
{
    /**
     * Handle the IncomeStatement "created" event.
     */
    public function created(IncomeStatement $incomeStatement): void
    {
        //get financial analyses if exists
        $record = [];
        $record["gross_profit_percent"] = number_format(($incomeStatement->gross_profit / $incomeStatement->total_revenue) * 100, 1);
        $record["net_profit_percent"] = number_format(($incomeStatement->net_profit / $incomeStatement->total_revenue) * 100, 1);
        $record["net_profit_year_predict"] = $incomeStatement->net_profit / $incomeStatement->order * 12;


        //Find gold
        $date = Verta::parse(Verta::parse($incomeStatement->financialPeriod->solar_end_date)->format("Y") . "-" . $incomeStatement->order)->endMonth()->datetime();
        $gold = Gold::query()
            ->whereYear("date_time", $date->format("Y"))
            ->whereMonth("date_time", $date->format("m"))
            ->latest("date_time")
            ->first();
        if ($gold) {
            $record["net_profit_to_gold"] = number_format($incomeStatement->net_profit / $gold->close, 1);
            $record["net_profit_year_predict_to_gold"] = number_format($record["net_profit_year_predict"] / $gold->close, 1);
        }

        $record["instrument_id"] = $incomeStatement->instrument_id;
        $record["financial_period_id"] = $incomeStatement->financial_period_id;
        $record["order"] = $incomeStatement->order;
        FinancialStatementsAnalysis::query()
            ->updateOrCreate([
                "instrument_id" => $record["instrument_id"],
                "financial_period_id" => $record["financial_period_id"],
                "order" => $record["order"],
            ], $record);
    }

    /**
     * Handle the IncomeStatement "updated" event.
     */
    public function updated(IncomeStatement $incomeStatement): void
    {
        //
    }

    /**
     * Handle the IncomeStatement "deleted" event.
     */
    public function deleted(IncomeStatement $incomeStatement): void
    {
        //
    }

    /**
     * Handle the IncomeStatement "restored" event.
     */
    public function restored(IncomeStatement $incomeStatement): void
    {
        //
    }

    /**
     * Handle the IncomeStatement "force deleted" event.
     */
    public function forceDeleted(IncomeStatement $incomeStatement): void
    {
        //
    }
}
