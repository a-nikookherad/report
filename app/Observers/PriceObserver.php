<?php

namespace App\Observers;

use App\Models\Bourse\History;
use Illuminate\Database\Eloquent\Builder;

class PriceObserver
{
    /**
     * Handle the History "created" event.
     */
    public function created(History $history): void
    {
        exit();
        $financialPeriodInstance = $history->financialPeriod;
        $price = $history->close / 10 * $financialPeriodInstance->share_count;
        $date = \Illuminate\Support\Facades\Date::createFromTimestamp($history->timestamp)->format("Y-m-d");


        //Find net profit from income statement
        if ($financialPeriodInstance->incomeStatements) {
            $incomeStatement = $financialPeriodInstance->incomeStatment->last();
        } else {
            $newInstrumentInstance = $history->instrument()
                ->with("financialPeriods", function (Builder $builder) {
                    return $builder->whereHas("incomeStatements");
                });
            $financialPeriodInstance = $newInstrumentInstance->financialPeriod->last();
            $incomeStatement = $financialPeriodInstance->incomeStatment->last();
        }


        //Find annual sale from activity

        //Find total asset from balance sheet

        //Find total equity from balance sheet

        //Find dividend share from cash flow

        $historyInstance = $history;
        $instrumentInstance = $history->instrument;
        $incomeStatement = $instrumentInstance->incomeStatement;
        $balanceSheet = $instrumentInstance->balanceSheet;
        $lastYearLatestBalanceSheet = BalanceSheet::query()
            ->whereHas("financialPeriod", function (Builder $query) use ($balanceSheet) {
                return $query->where("solar_end_date", Verta::parse($balanceSheet->financialPeriod->solar_end_date)->subYear()->endYear()->format("Y-m-d"));
            })->orderBy("order", "desc")
            ->first();

        $price = $historyInstance->close / 10 * $historyInstance->share_count;
        $earn = $incomeStatement->net_profit / $incomeStatement->order * 12;

        $ratio = [];
        $ratio["gross"] = number_format($incomeStatement->gross_profit / $incomeStatement->total_revenue * 100) . "%";
        $ratio["net"] = number_format($incomeStatement->net_profit / $incomeStatement->total_revenue * 100) . "%";

        //calculate P/E
        $ratio["P/E"] = number_format($price / $earn, 1);

        //calculate P/S
        $activity = Activity::query()
            ->where("instrument_id", $instrumentInstance->id)
            ->orderBy("id", "desc")
            ->first();
        $ratio["P/S"] = number_format($price / $activity->predict_year_sales, 1);

        //calculate P/A
        $ratio["P/A"] = number_format($price / $balanceSheet->total_assets, 1);

        //calculate P/B
        $ratio["P/B"] = number_format($price / $balanceSheet->total_equity, 1);

        //calculate R/A
        $ratio["RC/A"] = number_format($balanceSheet->receivable_claim / $balanceSheet->total_assets * 100) . "%";

        //calculate E/E or ROE
        $ratio["ROE"] = number_format($earn / (($lastYearLatestBalanceSheet->total_equity * 2 + $earn) / 2) * 100) . "%";

        //calculate E/E or ROE
        $ratio["ROA"] = number_format($earn / (($lastYearLatestBalanceSheet->total_assets * 2 + $earn) / 2) * 100) . "%";

        //calculate IRR/XUD
        $gold = Gold::query()
            ->orderBy("id", "desc")
            ->first();
        $ratio["P/XUD"] = ($price / $gold->close / 1000000) . " T";

        //calculate IRR/USD
        $dollar = Dollar::query()
            ->orderBy("id", "desc")
            ->first();
        $ratio["P/USD"] = number_format($price / ($dollar->close / 10), null, null, ",");

    }

    /**
     * Handle the History "updated" event.
     */
    public function updated(History $history): void
    {
        //
    }

    /**
     * Handle the History "deleted" event.
     */
    public function deleted(History $history): void
    {
        //
    }

    /**
     * Handle the History "restored" event.
     */
    public function restored(History $history): void
    {
        //
    }

    /**
     * Handle the History "force deleted" event.
     */
    public function forceDeleted(History $history): void
    {
        //
    }
}
