<?php

namespace App\Models\Bourse;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivitiesAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        "predict_year_sales",
        "predict_year_sales_to_gold",
        "this_month_sale_to_gold",
        "season_sales_predict",
        "season_sales_predict_to_gold",
        "order",
        "activity_id",
        "financial_period_id",
        "instrument_id",
    ];
}
