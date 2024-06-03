<?php

namespace App\Observers;

use App\Models\Bourse\BalanceSheet;
use App\Models\Bourse\FinancialStatementsAnalysis;
use App\Models\Bourse\IncomeStatement;
use App\Models\Instruments\Gold;
use Hekmatinasser\Verta\Verta;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Date;

class BalanceSheetObserver
{
    /**
     * Handle the BalanceSheet "created" event.
     */
    public function created(BalanceSheet $balanceSheet): void
    {

        $lastBalanceSheetYear = \verta($balanceSheet->financialPeriod->end_date)->subYear()->endMonth()->format("Y-m-d");
        $lastBalanceSheet = BalanceSheet::query()
            ->where("order", 12)
            ->where("instrument_id", $balanceSheet->instrument_id)
            ->whereHas("financialPeriod", function (Builder $builder) use ($lastBalanceSheetYear) {
                return $builder->where("solar_end_date", $lastBalanceSheetYear);
            })
            ->first();

        //get financial analyses if exists
        $record = [];
        $record["a_l"] = number_format($balanceSheet->total_current_assets / $balanceSheet->total_current_liabilities, 1);
        $record["rc_a"] = number_format(($balanceSheet->receivable_claim / $balanceSheet->total_assets) * 100, 1);

        $incomeStatement = IncomeStatement::query()
            ->where("order", $balanceSheet->order)
            ->where("financial_period_id", $balanceSheet->financial_period_id)
            ->where("instrument_id", $balanceSheet->instrument_id)
            ->first();
        if ($incomeStatement && $lastBalanceSheet) {
            $record["roe"] = number_format($incomeStatement->net_profit / ((($lastBalanceSheet->total_equity * 2) + $incomeStatement->net_profit) / 2) * 100, 1);
            $record["roa"] = number_format($incomeStatement->net_profit / ((($lastBalanceSheet->total_assets * 2) + $incomeStatement->net_profit) / 2) * 100, 1);
        }
        $record["nav"] = $balanceSheet->total_equity;


        //Find gold
        $date = Verta::parse(Verta::parse($balanceSheet->financialPeriod->solar_end_date)->format("Y") . "-" . $balanceSheet->order)->endMonth()->datetime();
        $gold = Gold::query()
            ->whereYear("date_time", $date->format("Y"))
            ->whereMonth("date_time", $date->format("m"))
            ->latest("date_time")
            ->first();

        if ($gold && !empty($record["nav"])) {
            $record["nav_gold"] = number_format($record["nav"] / $gold->close, 1);
        }

        $record["instrument_id"] = $balanceSheet->instrument_id;
        $record["financial_period_id"] = $balanceSheet->financial_period_id;
        $record["order"] = $balanceSheet->order;
        FinancialStatementsAnalysis::query()
            ->updateOrCreate([
                "instrument_id" => $record["instrument_id"],
                "financial_period_id" => $record["financial_period_id"],
                "order" => $record["order"],
            ], $record);
    }

    /**
     * Handle the BalanceSheet "updated" event.
     */
    public function updated(BalanceSheet $balanceSheet): void
    {
        //
    }

    /**
     * Handle the BalanceSheet "deleted" event.
     */
    public function deleted(BalanceSheet $balanceSheet): void
    {
        //
    }

    /**
     * Handle the BalanceSheet "restored" event.
     */
    public function restored(BalanceSheet $balanceSheet): void
    {
        //
    }

    /**
     * Handle the BalanceSheet "force deleted" event.
     */
    public function forceDeleted(BalanceSheet $balanceSheet): void
    {
        //
    }
}
