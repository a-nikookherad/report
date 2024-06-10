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
        "p_g",
        "p_f",
        "grows_potential_percent",
        "financial_statements_order",
        "history_id",
        "activity_id",
        "financial_period_id",
        "instrument_id",
    ];

    public function instrument()
    {
        return $this->belongsTo(Instrument::class, "instrument_id");
    }

    public function financialPeriod()
    {
        return $this->belongsTo(FinancialPeriod::class, "financial_period_id");
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class, "activity_id");
    }
}
