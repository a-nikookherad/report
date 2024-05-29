<?php

namespace App\Observers;

use App\Models\Bourse\History;
use App\Models\Bourse\PricesAnalysis;

class PriceObserver
{
    /**
     * Handle the History "created" event.
     */
    public function created(History $history): void
    {
        $instrumentInstance = $history->instrument;
        $financialPeriodInstance = $history->financialPeriod;
        if (!$instrumentInstance || !$financialPeriodInstance) {
            exit();
        }
        try {

            $price = $history->close / 10 * $financialPeriodInstance->share_count;
            if (empty($price)) {
                return;
            }
            $date = \Illuminate\Support\Facades\Date::createFromTimestamp($history->timestamp);
            $month = (int)$date->format("m");
            $incomeStatement = $financialPeriodInstance->incomeStatements?->whereIn("order", [$month, $month + 1, $month - 1])->first();
            $balanceSheet = $financialPeriodInstance->balanceSheets?->whereIn("order", [$month, $month + 1, $month - 1])->first();
            $cashFlow = $financialPeriodInstance->cashFlows?->whereIn("order", [$month, $month + 1, $month - 1])->first();
            $activity = $financialPeriodInstance->activities?->where("order", $month)->first();

            $ratio = [];
            //Find net profit from income statement
            if ($incomeStatement) {
                $pe = $price / ($incomeStatement->net_profit / $incomeStatement->order * 12);
                $ratio["p_e"] = number_format($pe, 2);
            }

            //Find total asset from balance sheet
            if ($balanceSheet) {
                $pa = $price / $balanceSheet->total_assets;
                $ratio["p_a"] = number_format($pa, 2);
            }

            //Find total equity from balance sheet
            if ($balanceSheet) {
                $pb = $price / $balanceSheet->total_equity;
                $ratio["p_b"] = number_format($pb, 2);
            }

            //Find dividend share from cash flow
            if ($cashFlow) {
                $pd = $price / abs($cashFlow->dividend_payments);
                $ratio["p_d"] = number_format($pd, 2);
            }

            //Find annual sale from activity
            if ($activity && !empty($activity->predict_year_sales)) {
                $ps = $price / $activity->predict_year_sales;
                $ratio["p_s"] = number_format($ps, 2);
            } elseif ($activity) {
                $predictYearSale = $price / $activity->this_month_sales * 12;
                $ratio["p_s"] = number_format($predictYearSale, 2);
            }

            $ratio["financial_statements_order"] = $incomeStatement?->order ?? $balanceSheet?->order ?? $cashFlow?->order;
            $ratio["activity_id"] = $activity?->id;
            $ratio["history_id"] = $history?->id;
            $ratio["financial_period_id"] = $financialPeriodInstance?->id;
            $ratio["instrument_id"] = $instrumentInstance->id;
            PricesAnalysis::query()
                ->updateOrCreate([
                    "history_id" => $ratio["history_id"],
                    "financial_period_id" => $ratio["financial_period_id"],
                    "instrument_id" => $ratio["instrument_id"],
                ], $ratio);

        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }
        /*$ratio = [];
        $ratio["gross"] = number_format($incomeStatement->gross_profit / $incomeStatement->total_revenue * 100) . "%";
        $ratio["net"] = number_format($incomeStatement->net_profit / $incomeStatement->total_revenue * 100) . "%";

//calculate P/E
        $ratio["P/E"] = number_format($price / $earn, 1);

//calculate P/S
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
        $ratio["P/USD"] = number_format($price / ($dollar->close / 10), null, null, ",");*/

    }

    /**
     * Handle the History "updated" event.
     */
    public
    function updated(History $history): void
    {
    }

    /**
     * Handle the History "deleted" event.
     */
    public
    function deleted(History $history): void
    {
        //
    }

    /**
     * Handle the History "restored" event.
     */
    public
    function restored(History $history): void
    {
        //
    }

    /**
     * Handle the History "force deleted" event.
     */
    public
    function forceDeleted(History $history): void
    {
        //
    }
}
