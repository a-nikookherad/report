<?php

namespace App\Services\Utilities\Ratios;

use App\Models\Bourse\Instrument;

interface RatioInterface
{
    public function calculate(Instrument $instrument): ?float;
}
