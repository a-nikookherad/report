<?php

namespace Trade\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use HasFactory;

    protected $table = "trades";
    protected $fillable = [
        "fund",
        "risk",
        "type",
        "strategy_id",
        "win_rate",
        "loss_rate",
        "pay_of_ratio",
        "profit_factor",
        "expectancy",
        "financial_period_started_at",
        "financial_period_ended_at",
    ];
}
