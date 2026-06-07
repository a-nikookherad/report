<?php

namespace App\Services\Calculators\ROA;

use App\Models\Bourse\Instrument;

interface RatioInterface
{
    public function calculate(Instrument $instrument): ?float;
}
