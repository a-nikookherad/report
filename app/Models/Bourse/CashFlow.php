<?php

namespace App\Models\Bourse;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashFlow extends Model
{
    use HasFactory;

    protected $fillable = [
        "cash_from_operation",
        "cash_from_investing",
        "receipts_from_facilities",
        "payments_for_principle_facilities",
        "payments_for_interest_facilities",
        "dividend_payments",
        "cash_from_financing",
        "foreign_exchange_effect",
        "net_income_cash",
        "order",
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
