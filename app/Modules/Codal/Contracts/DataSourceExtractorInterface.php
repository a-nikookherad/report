<?php

namespace App\Modules\Codal\Contracts;

interface DataSourceExtractorInterface
{
    public function extractDatasource(string $html): ?array;
}
