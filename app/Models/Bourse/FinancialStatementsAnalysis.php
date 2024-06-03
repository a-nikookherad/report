<?php

namespace App\Models\Bourse;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialStatementsAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        "gross_profit_percent",
        "net_profit_percent",
        "net_profit_predict",
        "rc_a",
        "roe",
        "roa",
        "nav",
        "net_profit_predict_gold",
        "dividend_to_gold",
        "dividend_percent",
        "nav_gold",
        "peg",
        "order",
        "instrument_id",
        "financial_period_id",
    ];
}
