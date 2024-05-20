<?php

namespace App\Models\Bourse;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ratio extends Model
{
    use HasFactory;

    protected $fillable = [
        "p_e",
        "p_s",
        "p_a",
        "p_b",
        "r_a",
        "nav",
        "usd_nav",
        "peg",
        "roe",
        "roa",
        "instrument_id",
        "financial_period_id",
        "fis",
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
