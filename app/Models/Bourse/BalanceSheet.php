<?php

namespace App\Models\Bourse;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalanceSheet extends Model
{
    use HasFactory;

    protected $fillable = [
        "total_non_current_assets",
        "receivable_claim",
        "trade_and_other_receivables",
        "total_current_assets",
        "total_assets",
        "capital",
        "retained_earnings",
        "total_equity",
        "total_non_current_liabilities",
        "total_current_liabilities",
        "total_liabilities",
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
