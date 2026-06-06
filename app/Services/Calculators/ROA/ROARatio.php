<?php

namespace App\Services\Utilities\Ratios;

use App\Models\Bourse\Instrument;

class ROARatio implements RatioInterface
{
    public function calculate(Instrument $instrument): ?float
    {
        $income = $instrument->incomeStatement;
        $balance = $instrument->balanceSheet;

        if (!$income || !$balance) {
            return null;
        }

        if (!$balance->total_assets) {
            return null;
        }

        return round(
            ($income->net_profit_loss / $balance->total_assets) * 100,
            2
        );
    }
}
