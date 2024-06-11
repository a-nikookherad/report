<?php

namespace App\Logics\IPO;

use App\Models\Bourse\IPO;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class IPOLogic
{
    private string $url;
    private array $data;
    private array $headers;
    /**
     * @var Response $response
     */
    private $response;

    public function __construct($order)
    {
        $this->url = "https://api-mts.orbis.easytrader.ir/core/api/v2/order";
        $this->data = [
            "order" => $order
        ];
        $this->headers = [
            "sec-ch-ua" => '"Not_A Brand";v="99", "Google Chrome";v="109", "Chromium";v="109"',
            "User-Agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36",
            "Content-Type" => "application/json",
            "Accept" => "application/json",
            "Referer" => "https://d.orbis.easytrader.ir",
            "sec-ch-ua-platform" => "Windows",
        ];
    }

    public function send()
    {
        $this->response = Http::withHeaders($this->headers)
            ->withToken(config("financial.mofid_token"))
            ->retry(3, 100)
            ->post($this->url, $this->data);
        return $this;
    }

    public function log()
    {
        if ($this->response->successful() && $this->response->object()->isSuccessful) {
            $record["success"] = $this->response->successful();
        }

        $record["symbol"] = $this->data["order"]["symbolIsin"] ?? null;
        $record["price"] = $this->data["order"]["price"];
        $record["quantity"] = $this->data["order"]["quantity"];
        $record["status"] = $this->response->status();
        $record["body"] = $this->response->body();

        IPO::query()
            ->create($record);
        return $this;
    }

    public function response()
    {
        return $this->response;
    }

    public function fakeSend()
    {
        $this->response = Http::fake(function () {
            return [
                "isSuccessful" => true,
                "id" => "1121AE0tpkf1WqFp",
                "message" => ""
            ];
        })->post($this->url, $this->data);

//        time_nanosleep(0, 600000000);

        return $this;
    }
}
