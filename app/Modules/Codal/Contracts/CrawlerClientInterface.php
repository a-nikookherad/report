<?php

namespace App\Modules\Codal\Contracts;

use Psr\Http\Message\ResponseInterface;

interface CrawlerClientInterface
{
    public function crawl(string $url, array $queryParams, ?string $sheetId): ResponseInterface;
}
