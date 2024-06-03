<?php

namespace App\Observers;

use App\Models\Bourse\CashFlow;
use App\Models\Bourse\FinancialStatementsAnalysis;
use App\Models\Bourse\IncomeStatement;
use App\Models\Instruments\Gold;
use Hekmatinasser\Verta\Verta;

class CashFlowObserver
{
    /**
     * Handle the CashFlow "created" event.
     */
    public function created(CashFlow $cashFlow): void
    {
        $record = [];
        //Find gold
        $date = Verta::parse(Verta::parse($cashFlow->financialPeriod->solar_end_date)->format("Y") . "-" . $cashFlow->order)->endMonth()->datetime();
        $gold = Gold::query()
            ->whereYear("date_time", $date->format("Y"))
            ->whereMonth("date_time", $date->format("m"))
            ->orderBy("date_time", "desc")
            ->first();

        if ($gold) {
            $record["dividend_to_gold"] = number_format($cashFlow->dividend_payments / $gold->close, 1);
        }

        $incomeStatement = IncomeStatement::query()
            ->where("order", $cashFlow->order)
            ->where("financial_period_id", $cashFlow->financial_period_id)
            ->where("instrument_id", $cashFlow->instrument_id)
            ->first();
        if ($incomeStatement) {
            $record["dividend_percent"] = number_format(($cashFlow->dividend_payments / $incomeStatement->net_profit) * 100, 1);
        }

        if (empty($record)) {
            return;
        }

        $record["instrument_id"] = $cashFlow->instrument_id;
        $record["financial_period_id"] = $cashFlow->financial_period_id;
        $record["order"] = $cashFlow->order;
        FinancialStatementsAnalysis::query()
            ->updateOrCreate([
                "instrument_id" => $record["instrument_id"],
                "financial_period_id" => $record["financial_period_id"],
                "order" => $record["order"],
            ], $record);
    }

    /**
     * Handle the CashFlow "updated" event.
     */
    public function updated(CashFlow $cashFlow): void
    {
        //
    }

    /**
     * Handle the CashFlow "deleted" event.
     */
    public function deleted(CashFlow $cashFlow): void
    {
        //
    }

    /**
     * Handle the CashFlow "restored" event.
     */
    public function restored(CashFlow $cashFlow): void
    {
        //
    }

    /**
     * Handle the CashFlow "force deleted" event.
     */
    public function forceDeleted(CashFlow $cashFlow): void
    {
        //
    }
}
