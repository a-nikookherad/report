<?php

namespace App\Models;

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
        "net_income",
        "order",
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
