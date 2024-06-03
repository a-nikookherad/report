<?php

namespace App\Observers;

use App\Models\Bourse\ActivitiesAnalysis;
use App\Models\Bourse\Activity;
use App\Models\Instruments\Gold;
use Illuminate\Support\Facades\Date;

class ActivityObserver
{
    /**
     * Handle the Activity "created" event.
     */
    public function created(Activity $activity): void
    {
        $year = Date::createFromDate($activity->financialPeriod->end_date)->format("Y");
        $month = $activity->order;
        $date = Date::createFromDate($year, $month)->endOfMonth();
        $record = [];
        $record["predict_year_sales"] = $activity->total_sales_for_now / $activity->order * 12;

        if (in_array($activity->order, [1, 4, 7, 10])) {         //two month predict
            $record["season_sales_predict"] = $activity->this_month_sales * 3;
        } elseif (in_array($activity->order, [2, 5, 8, 11])) {  //one month predict
            $record["season_sales_predict"] = Activity::query()
                ->whereIn("order", [$activity->order, $activity->order - 1])
                ->where("financial_period_id", $activity->financial_period_id)
                ->where("instrument_id", $activity->instrument_id)
                ->average("this_month_sales");
        } else {                                                //no need predict
            $record["season_sales_predict"] = Activity::query()
                ->whereIn("order", [$activity->order, $activity->order - 1, $activity->order - 2])
                ->where("financial_period_id", $activity->financial_period_id)
                ->where("instrument_id", $activity->instrument_id)
                ->average("this_month_sales");
        }

        //Find gold
        $gold = Gold::query()
            ->whereBetween("date_time", [
                $date->format("Y-m-d"),
                $date->addDays(2)->format("Y-m-d"),
            ])
            ->orderBy("date_time")
            ->firstOr(function () use ($date) {
                return Gold::query()
                    ->whereBetween("date_time", [
                        $date->subDays(3)->format("Y-m-d"),
                        $date->addDays(3)->format("Y-m-d"),
                    ])
                    ->orderBy("date_time")
                    ->first();
            });

        if ($gold) {
            $record["predict_year_sales_to_gold"] = $record["predict_year_sales"] / $gold->close;
            $record["this_month_sale_to_gold"] = $activity->this_month_sales / $gold->close;
            $record["season_sales_predict_to_gold"] = $record["season_sales_predict"] / $gold->close;
        }
        $record["order"] = $activity->order;
        $record["activity_id"] = $activity->id;
        $record["financial_period_id"] = $activity->financial_period_id;
        $record["instrument_id"] = $activity->instrument_id;

        ActivitiesAnalysis::query()
            ->updateOrCreate([
                "order" => $record["order"],
                "activity_id" => $record["activity_id"],
                "financial_period_id" => $record["financial_period_id"],
                "instrument_id" => $record["instrument_id"],
            ], $record);
    }

    /**
     * Handle the Activity "updated" event.
     */
    public
    function updated(Activity $activity): void
    {
        //
    }

    /**
     * Handle the Activity "deleted" event.
     */
    public
    function deleted(Activity $activity): void
    {
        //
    }

    /**
     * Handle the Activity "restored" event.
     */
    public
    function restored(Activity $activity): void
    {
        //
    }

    /**
     * Handle the Activity "force deleted" event.
     */
    public
    function forceDeleted(Activity $activity): void
    {
        //
    }
}
