<?php

namespace App\Models\Bourse;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricesAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        "p_e",
        "p_s",
        "p_a",
        "p_b",
        "p_d",
        "financial_statements_order",
        "activity_id",
        "financial_period_id",
        "instrument_id",
    ];
}
