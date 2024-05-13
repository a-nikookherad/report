<?php

namespace App\Spiders;
use Symfony\Component\DomCrawler\Crawler;

use Generator;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;
use RoachPHP\Extensions\LoggerExtension;
use RoachPHP\Extensions\StatsCollectorExtension;
use RoachPHP\Http\Response;
use RoachPHP\Spider\ParseResult;
use RoachPHP\Spider\BasicSpider;

class GetActivitiesSpider extends BasicSpider
{
    public array $startUrls = [
//        "https://codal.ir/Reports/MonthlyActivity.aspx?LetterSerial=bVOrUc8pFbPov4IN3PZW6w%3d%3d",
//        "https://codal.ir/Reports/Decision.aspx?LetterSerial=s3hE1fx8sFteUCDYoYh1%2bQ%3d%3d&rt=0&let=6&ct=0&ft=-1&sheetId=1"
        "https://codal.ir/Reports/Decision.aspx?LetterSerial=gQQQaQQQjStFCy6X1VaRiA5XQ30w%3d%3d&rt=0&let=6&ct=0&ft=-1"
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

    /**
     * @return Generator<ParseResult>
     */
    public function parse(Response $response): Generator
    {
        $res=$response->getResponse()->getBody();
//            ->each(function (Crawler $node) {
//            dump($node->text());
//            return $node;
//        });
        dd($res);
    }
}
