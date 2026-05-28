<?php

namespace App\Models\Bourse;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equity extends Model
{
    protected $fillable = [
        "declared_dividend",
        "capital_increase",
        "increase_in_working_capital",
        "buy_treasury_stock",
        "sell_treasury_stock",
        "allocate_to_legal_reserve",
        "allocate_to_other_reserves",
        "period_order",
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
}
