<?php

namespace App\Modules\Codal;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class CrawlerClientAdapter implements CrawlerClientInterface
{
    private const BASE_URL = 'https://www.codal.ir/Reports/Decision.aspx';
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'cookies' => true,
            'allow_redirects' => true,
            'verify' => true,
            'timeout' => 120,
            'connect_timeout' => 20,

            'headers' => [
                'User-Agent' =>
                    'Mozilla/5.0 (X11; Linux x86_64) '
                    . 'AppleWebKit/537.36 '
                    . '(KHTML, like Gecko) '
                    . 'Chrome/156.0.0.0 Safari/537.36',

                'Accept' =>
                    'text/html,application/xhtml+xml,'
                    . 'application/xml;q=0.9,*/*;q=0.8',

                'Accept-Language' => 'fa-IR,fa;q=0.9,en;q=0.8',
            ],
        ]);
    }

    public function crawl(string $url, array $queryParams, ?string $sheetId): ResponseInterface
    {
        return $this->client->get(
            self::BASE_URL,
            [
                'query' => array_merge(
                    $queryParams,
                    ['sheetId' => $sheetId]
                ),

                'headers' => [
                    'Referer' => $url,
                ],
            ]
        );
    }

    /**
     * Request a financial statement using GET.
     */
    public function requestStatement(
        string  $url,
        array   $queryParams,
        ?string $sheetId
    ): string
    {
        $response = $this->client->get(
            self::BASE_URL,
            [
                'query' => array_merge(
                    $queryParams,
                    ['sheetId' => $sheetId]
                ),

                'headers' => [
                    'Referer' => $url,
                ],
            ]
        );

        return (string)$response->getBody();
    }
}
