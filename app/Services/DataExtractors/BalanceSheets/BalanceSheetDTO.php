<?php

namespace App\Services\DataExtractors\BalanceSheets;

class BalanceSheetDTO
{
    private array $data;

    public function __construct(
        int  $financialPeriod_id,
        int  $instrument_id,
        ?int $totalNonCurrentAssets,
        ?int $tradeAndOtherReceivables,
        ?int $totalCurrentAssets,
        ?int $totalAssets,
        ?int $capital,
        ?int $retainedEarnings,
        ?int $totalEquity,
        ?int $totalNonCurrentLiabilities,
        ?int $totalCurrentLiabilities,
        ?int $totalLiabilities
    )
    {
        $this->data["financial_period_id"] = $financialPeriod_id;
        $this->data["instrument_id"] = $instrument_id;
        $this->data["total_non_current_assets"] = $totalNonCurrentAssets;
        $this->data["trade_and_other_receivables"] = $tradeAndOtherReceivables;
        $this->data["total_current_assets"] = $totalCurrentAssets;
        $this->data["total_assets"] = $totalAssets;
        $this->data["capital"] = $capital;
        $this->data["retained_earnings"] = $retainedEarnings;
        $this->data["total_equity"] = $totalEquity;
        $this->data["total_non_current_liabilities"] = $totalNonCurrentLiabilities;
        $this->data["total_current_liabilities"] = $totalCurrentLiabilities;
        $this->data["total_liabilities"] = $totalLiabilities;
    }

    public static function fromExtractor(array $records): static
    {
        return new static(
            financialPeriod_id: $records["financial_period_id"],
            instrument_id: $records["instrument_id"],
            totalNonCurrentAssets: $records["total_non_current_assets"] ?? null,
            tradeAndOtherReceivables: $records["trade_and_other_receivables"] ?? null,
            totalCurrentAssets: $records["total_current_assets"] ?? null,
            totalAssets: $records["total_assets"] ?? null,
            capital: $records["capital"] ?? null,
            retainedEarnings: $records["retained_earnings"] ?? null,
            totalEquity: $records["total_equity"] ?? null,
            totalNonCurrentLiabilities: $records["total_non_current_liabilities"] ?? null,
            totalCurrentLiabilities: $records["total_current_liabilities"] ?? null,
            totalLiabilities: $records["total_liabilities"] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter($this->data, fn($value) => $value !== null);
    }
}
