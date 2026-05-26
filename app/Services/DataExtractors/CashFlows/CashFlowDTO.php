<?php

namespace App\Services\DataExtractors\CashFlows;

class CashFlowDTO
{
    private array $data;

    public function __construct(
        int    $financialPeriod_id,
        int    $instrument_id,
        string $periodOrder,
        string $periodEndDate,
        string $yearEndDate,
        ?int   $cashFlowFromOperatingActivities,
        ?int   $cashPaidForIncomeTax,
        ?int   $netCashInflowOutflowFromOperatingActivities,
        ?int   $cashReceivedFromSaleOfPropertyPlantAndEquipment,
        ?int   $cashPaidForPurchaseOfPropertyPlantAndEquipment,
        ?int   $cashReceivedFromSaleOfLongTermInvestments,
        ?int   $cashPaidForAcquisitionOfLongTermInvestments,
        ?int   $cashReceivedFromSaleOfShortTermInvestments,
        ?int   $cashPaidForAcquisitionOfShortTermInvestments,
        ?int   $cashReceivedFromDividends,
        ?int   $cashReceivedFromOtherInvestments,
        ?int   $netCashInflowOutflowFromInvestingActivities,
        ?int   $netCashBeforeFinancingActivities,
        ?int   $cashReceivedFromIncreaseInCapital,
        ?int   $cashReceivedFromSaleOfTreasuryShares,
        ?int   $cashPaidForPurchaseOfTreasuryShares,
        ?int   $cashReceivedFromFacilities,
        ?int   $cashPaidForPrincipalOfFacilities,
        ?int   $cashPaidForInterestOfFacilities,
        ?int   $cashReceivedFromBondsIssued,
        ?int   $cashPaidForPrincipalOfBonds,
        ?int   $cashPaidForInterestOfBonds,
        ?int   $cashPaidForDividends,
        ?int   $netCashInflowOutflowFromFinancingActivities,
        ?int   $effectOfExchangeRateChanges,
        ?int   $netIncreaseDecreaseInCash,
        ?int   $cashBalanceAtBeginningOfYear,
        ?int   $cashBalanceAtEndOfYear,
        ?int   $nonCashTransactions
    )
    {
        $this->data["financial_period_id"] = $financialPeriod_id;
        $this->data["instrument_id"] = $instrument_id;
        $this->data["period_order"] = $periodOrder;
        $this->data["period_end_date"] = $periodEndDate;
        $this->data["year_end_date"] = $yearEndDate;
        $this->data["cash_flow_from_operating_activities"] = $cashFlowFromOperatingActivities;
        $this->data["cash_paid_for_income_tax"] = $cashPaidForIncomeTax;
        $this->data["net_cash_inflow_outflow_from_operating_activities"] = $netCashInflowOutflowFromOperatingActivities;
        $this->data["cash_received_from_sale_of_property_plant_and_equipment"] = $cashReceivedFromSaleOfPropertyPlantAndEquipment;
        $this->data["cash_paid_for_purchase_of_property_plant_and_equipment"] = $cashPaidForPurchaseOfPropertyPlantAndEquipment;
        $this->data["cash_received_from_sale_of_long_term_investments"] = $cashReceivedFromSaleOfLongTermInvestments;
        $this->data["cash_paid_for_acquisition_of_long_term_investments"] = $cashPaidForAcquisitionOfLongTermInvestments;
        $this->data["cash_received_from_sale_of_short_term_investments"] = $cashReceivedFromSaleOfShortTermInvestments;
        $this->data["cash_paid_for_acquisition_of_short_term_investments"] = $cashPaidForAcquisitionOfShortTermInvestments;
        $this->data["cash_received_from_dividends"] = $cashReceivedFromDividends;
        $this->data["cash_received_from_other_investments"] = $cashReceivedFromOtherInvestments;
        $this->data["net_cash_inflow_outflow_from_investing_activities"] = $netCashInflowOutflowFromInvestingActivities;
        $this->data["net_cash_before_financing_activities"] = $netCashBeforeFinancingActivities;
        $this->data["cash_received_from_increase_in_capital"] = $cashReceivedFromIncreaseInCapital;
        $this->data["cash_received_from_sale_of_treasury_shares"] = $cashReceivedFromSaleOfTreasuryShares;
        $this->data["cash_paid_for_purchase_of_treasury_shares"] = $cashPaidForPurchaseOfTreasuryShares;
        $this->data["cash_received_from_facilities"] = $cashReceivedFromFacilities;
        $this->data["cash_paid_for_principal_of_facilities"] = $cashPaidForPrincipalOfFacilities;
        $this->data["cash_paid_for_interest_of_facilities"] = $cashPaidForInterestOfFacilities;
        $this->data["cash_received_from_bonds_issued"] = $cashReceivedFromBondsIssued;
        $this->data["cash_paid_for_principal_of_bonds"] = $cashPaidForPrincipalOfBonds;
        $this->data["cash_paid_for_interest_of_bonds"] = $cashPaidForInterestOfBonds;
        $this->data["cash_paid_for_dividends"] = $cashPaidForDividends;
        $this->data["net_cash_inflow_outflow_from_financing_activities"] = $netCashInflowOutflowFromFinancingActivities;
        $this->data["effect_of_exchange_rate_changes"] = $effectOfExchangeRateChanges;
        $this->data["net_increase_decrease_in_cash"] = $netIncreaseDecreaseInCash;
        $this->data["cash_balance_at_beginning_of_year"] = $cashBalanceAtBeginningOfYear;
        $this->data["cash_balance_at_end_of_year"] = $cashBalanceAtEndOfYear;
        $this->data["non_cash_transactions"] = $nonCashTransactions;
    }

    public static function fromExtractor(array $records): static
    {
        return new static(
            financialPeriod_id: $records["financial_period_id"],
            instrument_id: $records["instrument_id"],
            periodOrder: $records["period_order"],
            periodEndDate: $records["period_end_date"],
            yearEndDate: $records["year_end_date"],
            cashFlowFromOperatingActivities: $records["cash_flow_from_operating_activities"] ?? null,
            cashPaidForIncomeTax: $records["cash_paid_for_income_tax"] ?? null,
            netCashInflowOutflowFromOperatingActivities: $records["net_cash_inflow_outflow_from_operating_activities"] ?? null,
            cashReceivedFromSaleOfPropertyPlantAndEquipment: $records["cash_received_from_sale_of_property_plant_and_equipment"] ?? null,
            cashPaidForPurchaseOfPropertyPlantAndEquipment: $records["cash_paid_for_purchase_of_property_plant_and_equipment"] ?? null,
            cashReceivedFromSaleOfLongTermInvestments: $records["cash_received_from_sale_of_long_term_investments"] ?? null,
            cashPaidForAcquisitionOfLongTermInvestments: $records["cash_paid_for_acquisition_of_long_term_investments"] ?? null,
            cashReceivedFromSaleOfShortTermInvestments: $records["cash_received_from_sale_of_short_term_investments"] ?? null,
            cashPaidForAcquisitionOfShortTermInvestments: $records["cash_paid_for_acquisition_of_short_term_investments"] ?? null,
            cashReceivedFromDividends: $records["cash_received_from_dividends"] ?? null,
            cashReceivedFromOtherInvestments: $records["cash_received_from_other_investments"] ?? null,
            netCashInflowOutflowFromInvestingActivities: $records["net_cash_inflow_outflow_from_investing_activities"] ?? null,
            netCashBeforeFinancingActivities: $records["net_cash_before_financing_activities"] ?? null,
            cashReceivedFromIncreaseInCapital: $records["cash_received_from_increase_in_capital"] ?? null,
            cashReceivedFromSaleOfTreasuryShares: $records["cash_received_from_sale_of_treasury_shares"] ?? null,
            cashPaidForPurchaseOfTreasuryShares: $records["cash_paid_for_purchase_of_treasury_shares"] ?? null,
            cashReceivedFromFacilities: $records["cash_received_from_facilities"] ?? null,
            cashPaidForPrincipalOfFacilities: $records["cash_paid_for_principal_of_facilities"] ?? null,
            cashPaidForInterestOfFacilities: $records["cash_paid_for_interest_of_facilities"] ?? null,
            cashReceivedFromBondsIssued: $records["cash_received_from_bonds_issued"] ?? null,
            cashPaidForPrincipalOfBonds: $records["cash_paid_for_principal_of_bonds"] ?? null,
            cashPaidForInterestOfBonds: $records["cash_paid_for_interest_of_bonds"] ?? null,
            cashPaidForDividends: $records["cash_paid_for_dividends"] ?? null,
            netCashInflowOutflowFromFinancingActivities: $records["net_cash_inflow_outflow_from_financing_activities"] ?? null,
            effectOfExchangeRateChanges: $records["effect_of_exchange_rate_changes"] ?? null,
            netIncreaseDecreaseInCash: $records["net_increase_decrease_in_cash"] ?? null,
            cashBalanceAtBeginningOfYear: $records["cash_balance_at_beginning_of_year"] ?? null,
            cashBalanceAtEndOfYear: $records["cash_balance_at_end_of_year"] ?? null,
            nonCashTransactions: $records["non_cash_transactions"] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter($this->data, fn($value) => $value !== null);
    }
}
