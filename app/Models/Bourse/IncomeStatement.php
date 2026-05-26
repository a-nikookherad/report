<?php

namespace App\Models\Bourse;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomeStatement extends Model
{
    use HasFactory;

    protected $fillable = [
        "operating_revenue",
        "cost_of_operating_revenue",
        "gross_profit_loss",
        "selling_general_administrative_expenses",
        "other_operating_income",
        "other_operating_expenses",
        "operating_profit_loss",
        "finance_costs",
        "other_non_operating_income_expenses",
        "non_operating_investment_income",
        "non_operating_miscellaneous_items",
        "current_year_tax",
        "prior_years_tax",
        "net_profit_loss_from_continuing_operations",
        "net_profit_loss",
        "capital",
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
