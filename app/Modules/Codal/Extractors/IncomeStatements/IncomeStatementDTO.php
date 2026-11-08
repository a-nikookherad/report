<?php

namespace App\Modules\Codal\Extractors\IncomeStatements;

use App\Models\Bourse\IncomeStatement;
use App\Modules\Codal\PersistenceServices\StorableInterface;

class IncomeStatementDTO implements StorableInterface
{
    private array $data;

    public function __construct(
        int     $financialPeriod_id,
        int     $instrument_id,
        ?int    $operating_revenue,
        ?int    $cost_of_operating_revenue,
        ?int    $gross_profit_loss,
        ?int    $selling_general_administrative_expenses,
        ?int    $other_operating_income,
        ?int    $other_operating_expenses,
        ?int    $operating_profit_loss,
        ?int    $finance_costs,
        ?int    $other_non_operating_income_expenses,
        ?int    $non_operating_investment_income,
        ?int    $non_operating_miscellaneous_items,
        ?int    $current_year_tax,
        ?int    $prior_years_tax,
        ?int    $net_profit_loss_from_continuing_operations,
        ?int    $net_profit_loss,
        ?int    $capital,
        ?string $period_order,
        ?string $period_end_date,
        ?string $year_end_date,
    )
    {
        $this->data["financial_period_id"] = $financialPeriod_id;
        $this->data["instrument_id"] = $instrument_id;
        $this->data["operating_revenue"] = $operating_revenue;
        $this->data["cost_of_operating_revenue"] = $cost_of_operating_revenue;
        $this->data["gross_profit_loss"] = $gross_profit_loss;
        $this->data["selling_general_administrative_expenses"] = $selling_general_administrative_expenses;
        $this->data["other_operating_income"] = $other_operating_income;
        $this->data["other_operating_expenses"] = $other_operating_expenses;
        $this->data["operating_profit_loss"] = $operating_profit_loss;
        $this->data["finance_costs"] = $finance_costs;
        $this->data["other_non_operating_income_expenses"] = $other_non_operating_income_expenses;
        $this->data["non_operating_investment_income"] = $non_operating_investment_income;
        $this->data["non_operating_miscellaneous_items"] = $non_operating_miscellaneous_items;
        $this->data["current_year_tax"] = $current_year_tax;
        $this->data["prior_years_tax"] = $prior_years_tax;
        $this->data["net_profit_loss_from_continuing_operations"] = $net_profit_loss_from_continuing_operations;
        $this->data["net_profit_loss"] = $net_profit_loss;
        $this->data["capital"] = $capital;
        $this->data["period_order"] = $period_order;
        $this->data["period_end_date"] = $period_end_date;
        $this->data["year_end_date"] = $year_end_date;
    }

    public static function fromExtractor(array $records): static
    {
        return new static(
            financialPeriod_id: $records["financial_period_id"],
            instrument_id: $records["instrument_id"],
            operating_revenue: $records["operating_revenue"] ?? null,
            cost_of_operating_revenue: $records["cost_of_operating_revenue"] ?? null,
            gross_profit_loss: $records["gross_profit_loss"] ?? null,
            selling_general_administrative_expenses: $records["selling_general_administrative_expenses"] ?? null,
            other_operating_income: $records["other_operating_income"] ?? null,
            other_operating_expenses: $records["other_operating_expenses"] ?? null,
            operating_profit_loss: $records["operating_profit_loss"] ?? null,
            finance_costs: $records["finance_costs"] ?? null,
            other_non_operating_income_expenses: $records["other_non_operating_income_expenses"] ?? null,
            non_operating_investment_income: $records["non_operating_investment_income"] ?? null,
            non_operating_miscellaneous_items: $records["non_operating_miscellaneous_items"] ?? null,
            current_year_tax: $records["current_year_tax"] ?? null,
            prior_years_tax: $records["prior_years_tax"] ?? null,
            net_profit_loss_from_continuing_operations: $records["net_profit_loss_from_continuing_operations"] ?? null,
            net_profit_loss: $records["net_profit_loss"] ?? null,
            capital: $records["capital"] ?? null,
            period_order: $records["period_order"] ?? null,
            period_end_date: $records["period_end_date"] ?? null,
            year_end_date: $records["year_end_date"] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter($this->data, fn($value) => $value !== null);
    }

    public function getModel(): string
    {
        return IncomeStatement::class;
    }
}
