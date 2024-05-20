<?php

namespace App\Models\Bourse;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomeStatement extends Model
{
    use HasFactory;

    protected $fillable = [
        "total_revenue",
        "cost_of_revenue",
        "gross_profit",
        "operation_expenses",
        "operating_income",
        "other_operating_income",
        "other_income",
        "net_income",
        "order",
        "financial_cost",
        "tax",
        "fund",
        "script",
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
}
