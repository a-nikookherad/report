<?php

namespace App\Spiders;

use Generator;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;
use RoachPHP\Extensions\LoggerExtension;
use RoachPHP\Extensions\StatsCollectorExtension;
use RoachPHP\Http\Response;
use RoachPHP\Spider\ParseResult;
use RoachPHP\Spider\BasicSpider;

class TestSpider extends BasicSpider
{
    public array $startUrls = [
//        "https://www.shakhesban.com/markets/stock/%D9%81%D9%85%D9%84%DB%8C",
        "https://www.shakhesban.com/markets/stock/%D9%81%D9%88%D9%84%D8%A7%D8%AF"
    ];

    public array $downloaderMiddleware = [
        RequestDeduplicationMiddleware::class,
    ];

    public array $spiderMiddleware = [
        //
    ];

    public array $itemProcessors = [
        //
    ];

    public array $extensions = [
        LoggerExtension::class,
        StatsCollectorExtension::class,
    ];

    public int $concurrency = 2;

    public int $requestDelay = 1;


    public function parse(Response $response): Generator
    {
        $marketValue = (int)$response->filter('[data-col="trades.arzesh_bazar"]')->getNode(0)->firstChild->textContent * 100000000000;

        dd(number_format($marketValue,null,null,",") );
    }
}
