<?php

namespace App\Modules\Codal\Extractors\Equities;

use App\Models\Bourse\Equity;
use App\Modules\Codal\PersistenceServices\StorableInterface;

class EquityDTO implements StorableInterface
{
    private array $data;

    public function __construct(
        int     $financialPeriod_id,
        int     $instrument_id,
        ?string $periodOrder,
        ?string $periodEndDate,
        ?string $yearEndDate,
        ?int    $declaredDividend,
        ?int    $capitalIncrease,
        ?int    $increaseInWorkingCapital,
        ?int    $buyTreasuryStock,
        ?int    $sellTreasuryStock,
        ?int    $allocateToLegalReserve,
        ?int    $allocateToOtherReserve,
    )
    {
        $this->data["financial_period_id"] = $financialPeriod_id;
        $this->data["instrument_id"] = $instrument_id;
        $this->data["period_order"] = $periodOrder;
        $this->data["period_end_date"] = $periodEndDate;
        $this->data["year_end_date"] = $yearEndDate;
        $this->data["declared_dividend"] = $declaredDividend;
        $this->data["capital_increase"] = $capitalIncrease;
        $this->data["increase_in_working_capital"] = $increaseInWorkingCapital;
        $this->data["buy_treasury_stock"] = $buyTreasuryStock;
        $this->data["sell_treasury_stock"] = $sellTreasuryStock;
        $this->data["allocate_to_legal_reserve"] = $allocateToLegalReserve;
        $this->data["allocate_to_other_reserves"] = $allocateToOtherReserve;
    }

    public static function fromExtractor(array $records): static
    {
        return new static(
            financialPeriod_id: $records["financial_period_id"],
            instrument_id: $records["instrument_id"],
            periodOrder: $records["period_order"] ?? null,
            periodEndDate: $records["period_end_date"] ?? null,
            yearEndDate: $records["year_end_date"] ?? null,
            declaredDividend: $records["declared_dividend"] ?? null,
            capitalIncrease: $records["capital_increase"] ?? null,
            increaseInWorkingCapital: $records["increase_in_working_capital"] ?? null,
            buyTreasuryStock: $records["buy_treasury_stock"] ?? null,
            sellTreasuryStock: $records["sell_treasury_stock"] ?? null,
            allocateToLegalReserve: $records["allocate_to_legal_reserve"] ?? null,
            allocateToOtherReserve: $records["allocate_to_other_reserves"] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter($this->data, fn($value) => $value !== null);
    }

    public function getModel(): string
    {
        return Equity::class;
    }
}
