<?php

namespace App\Models;

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
        "industry_id",
        "group_id",
    ];

    public function financialPeriods()
    {
        return $this->hasMany(FinancialPeriod::class, "instrument_id");
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

    public function incomeStatements()
    {
        return $this->hasMany(IncomeStatement::class, "instrument_id");
    }

    public function cashFlows()
    {
        return $this->hasMany(CashFlow::class, "instrument_id");
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, "instrument_id");
    }

    public function ratios()
    {
        return $this->hasMany(Ratio::class, "instrument_id");
    }

    public function histories()
    {
        return $this->hasMany(History::class, "instrument_id");
    }
}
