<?php

namespace App\Tools\Excel\Interfaces;

use App\Tools\Excel\ExcelService;

interface ExcelInterface
{
    public function read(string $path): ExcelService;

    public function write(string $path, array $data): bool;
}
