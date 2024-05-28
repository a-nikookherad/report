<?php

namespace App\Models\Bourse;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivitiesAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        "total_year_sales",
        "predict_year_sales",
        "season_sales",
        "season_sales_predict",
        "order",
        "instrument_id",
        "financial_period_id",
        "activity_id",
    ];
}
