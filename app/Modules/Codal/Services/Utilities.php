<?php

namespace App\Modules\Codal\Services;

class Utilities
{
    public function dateNormalize(string $periodEndDate): string
    {
        return str_replace(
            '/',
            '_',
            $periodEndDate
        );
    }

    public function normalizeSymbol(string $symbol): string
    {
        $symbol = trim($symbol);

        $symbol = str_replace(
            [' ', '-', '/', '\\'],
            '_',
            $symbol
        );

        $symbol = preg_replace('/_+/', '_', $symbol);

        return trim($symbol, '_');
    }
}
