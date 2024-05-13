<?php

namespace App\Tools\Excel\Interfaces;

interface ExcelWriterInterface
{
    public function write($path, $data);
}
