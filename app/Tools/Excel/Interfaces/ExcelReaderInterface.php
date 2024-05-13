<?php

namespace App\Tools\Excel\Interfaces;

interface ExcelReaderInterface
{
    public function purify(string $path);

    public function rows(): array;

    public function read(): array;

    public function records(): array;

    public function header(): array;

    public function body(): array;
}
