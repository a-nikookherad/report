<?php

namespace App\Models\Bourse;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashFlow extends Model
{
    use HasFactory;

    protected $fillable = [
        "financial_period_id",
        "instrument_id",
        "period_order",
        "period_end_date",
        "year_end_date",
        "cash_flow_from_operating_activities",
        "cash_paid_for_income_tax",
        "net_cash_inflow_outflow_from_operating_activities",
        "cash_received_from_sale_of_property_plant_and_equipment",
        "cash_paid_for_purchase_of_property_plant_and_equipment",
        "cash_received_from_sale_of_long_term_investments",
        "cash_paid_for_acquisition_of_long_term_investments",
        "cash_received_from_sale_of_short_term_investments",
        "cash_paid_for_acquisition_of_short_term_investments",
        "cash_received_from_dividends",
        "cash_received_from_other_investments",
        "net_cash_inflow_outflow_from_investing_activities",
        "net_cash_before_financing_activities",
        "cash_received_from_increase_in_capital",
        "cash_received_from_sale_of_treasury_shares",
        "cash_paid_for_purchase_of_treasury_shares",
        "cash_received_from_facilities",
        "cash_paid_for_principal_of_facilities",
        "cash_paid_for_interest_of_facilities",
        "cash_received_from_bonds_issued",
        "cash_paid_for_principal_of_bonds",
        "cash_paid_for_interest_of_bonds",
        "cash_paid_for_dividends",
        "net_cash_inflow_outflow_from_financing_activities",
        "effect_of_exchange_rate_changes",
        "net_increase_decrease_in_cash",
        "cash_balance_at_beginning_of_year",
        "cash_balance_at_end_of_year",
        "non_cash_transactions",
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
