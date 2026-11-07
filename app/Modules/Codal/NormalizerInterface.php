<?php

namespace App\Modules\Codal;

interface NormalizerInterface
{
    public function extract(mixed $crawler): array;
}
