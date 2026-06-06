<?php

namespace App\Models\Bourse;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instrument extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "symbol",
        "slug",
        "description",
        "mofid_url",
        "tse_tmc_code",
        "industry_id",
        "group_id",
        "financial_period_id",
    ];

    public function financialPeriods()
    {
        return $this->belongsTo(FinancialPeriod::class, "financial_period_id");
    }

    public function industry()
    {
        return $this->belongsTo(Industry::class, "industry_id");
    }

    public function group()
    {
        return $this->belongsTo(Group::class, "group_id");
    }

    public function balanceSheets()
    {
        return $this->hasMany(BalanceSheet::class, "instrument_id");
    }

    public function balanceSheet()
    {
        return $this->hasOne(BalanceSheet::class, "instrument_id")
            ->latest();
    }

    public function incomeStatements()
    {
        return $this->hasMany(IncomeStatement::class, "instrument_id");
    }

    public function incomeStatement()
    {
        return $this->hasOne(IncomeStatement::class, "instrument_id")
            ->latest();
    }

    public function cashFlows()
    {
        return $this->hasMany(CashFlow::class, "instrument_id");
    }

    public function equities()
    {
        return $this->hasMany(Equity::class, "instrument_id");
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, "instrument_id");
    }

    public function capitalIncreases()
    {
        return $this->hasMany(CapitalIncrease::class, "instrument_id");
    }

    public function prices()
    {
        return $this->hasMany(InstrumentPrice::class, "instrument_id");
    }

    public function lastPrice()
    {
        return $this->hasOne(InstrumentPrice::class, "instrument_id")
            ->orderBy("timestamp", "desc");
    }
}
