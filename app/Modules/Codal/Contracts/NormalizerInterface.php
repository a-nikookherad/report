<?php

namespace App\Modules\Codal\Contracts;

interface NormalizerInterface
{
    public function extract(mixed $crawler): array;
}
