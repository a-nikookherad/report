<?php

namespace App\Services\DataExtractors\Activities;

class ActivityDTO
{
    private array $data;

    public function __construct(
        int    $financialPeriod_id,
        int    $instrument_id,
        int    $domesticSales,
        int    $exportSales,
        int    $totalSales,
        int    $order,
        string $periodEndDate,
        string $yearEndDate,
        ?int   $energyCost,
        ?int   $feedCost
    )
    {
        $this->data["financial_period_id"] = $financialPeriod_id;
        $this->data["instrument_id"] = $instrument_id;
        $this->data["domestic_sales"] = $domesticSales;
        $this->data["export_sales"] = $exportSales;
        $this->data["total_sales"] = $totalSales;
        $this->data["order"] = $order;
        $this->data["period_end_date"] = $periodEndDate;
        $this->data["year_end_date"] = $yearEndDate;
        $this->data["energy_cost"] = $energyCost;
        $this->data["feed_cost"] = $feedCost;
    }

    public static function fromExtractor(array $records): static
    {
        return new static(
            financialPeriod_id: $records["financial_period_id"],
            instrument_id: $records["instrument_id"],
            domesticSales: $records["domestic_sales"],
            exportSales: $records["export_sales"],
            totalSales: $records["total_sales"],
            order: $records["order"],
            periodEndDate: $records["period_end_date"],
            yearEndDate: $records["year_end_date"],
            energyCost: $records["energy_cost"] ?? null,
            feedCost: $records["feed_cost"] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter($this->data, fn($value) => $value !== null);
    }
}
