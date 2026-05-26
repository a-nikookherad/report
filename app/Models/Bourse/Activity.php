<?php

namespace App\Models\Bourse;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        "energy_cost",
        "feed_cost",
        "domestic_sales",
        "export_sales",
        "total_sales",
        "order",
        "period_end_date",
        "year_end_date",
        "instrument_id",
        "financial_period_id",
    ];

    public function instrument()
    {
        return $this->belongsTo(Instrument::class, "instrument_id");
    }

    public function financialPeriod()
    {
        return $this->belongsTo(FinancialPeriod::class, "financial_period_id");
    }

    //todo remove these lines of code
    /*    public function analyze()
        {
            return $this->hasOne(ActivitiesAnalysis::class, "activity_id");
        }*/
}
