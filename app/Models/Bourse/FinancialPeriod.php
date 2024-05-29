<?php

namespace App\Models\Bourse;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialPeriod extends Model
{
    use HasFactory;

    protected $with = [
        "balanceSheets",
        "incomeStatements",
        "cashFlows",
        "activities",
    ];
    protected $fillable = [
        "solar_start_date",
        "solar_end_date",
        "start_date",
        "end_date",
        "share_count",
        "close",
        "industry_pe",
        "instrument_id",
    ];

    public function instrument()
    {
        return $this->belongsTo(Instrument::class, "instrument_id");
    }

    public function balanceSheets()
    {
        return $this->hasMany(BalanceSheet::class, "financial_period_id");
    }

    public function incomeStatements()
    {
        return $this->hasMany(IncomeStatement::class, "financial_period_id");
    }

    public function cashFlows()
    {
        return $this->hasMany(CashFlow::class, "financial_period_id");
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, "financial_period_id");
    }

    public function ratios()
    {
        return $this->hasMany(Ratio::class, "financial_period_id");
    }
}
