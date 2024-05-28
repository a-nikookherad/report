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
        "peg",
        "order",
        "instrument_id",
        "financial_period_id",
    ];
}
