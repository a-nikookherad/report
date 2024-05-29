<?php

namespace App\Console\Commands;

use App\Models\Bourse\Instrument;
use App\Models\Bourse\IPO;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SubmitIPOOrderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'submit:ipo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = Carbon::createFromTime(8, 44, 59)->format("Y-m-d H:i:s");
        $endTime = Carbon::createFromTime(8, 45, 3)->format("Y-m-d H:i:s");
        if (Cache::get("successAttempt") >= 1 || !Carbon::now()->isBetween($startTime, $endTime)) {
            exit();
        }
        $data = [
            "order" => [
                "symbolIsin" => "IRO3GOFZ0001",
                "price" => "6720",
                "quantity" => "23216",
                "side" => 0,
                "validityType" => 0,
                "validityDate" => null,
                "orderFrom" => 34,
            ]
        ];
        $url = "https://api-mts.orbis.easytrader.ir/core/api/v2/order";
        $headers = [
            "sec-ch-ua" => '"Not_A Brand";v="99", "Google Chrome";v="109", "Chromium";v="109"',
            "User-Agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36",
            "Content-Type" => "application/json",
            "Accept" => "application/json",
            "Referer" => "https://d.orbis.easytrader.ir",
            "sec-ch-ua-platform" => "Windows",
        ];
        /*$response = Http::fake(function () {
            return [
                "isSuccessful" => true,
                "id" => "1121AE0tpkf1WqFp",
                "message" => ""
            ];
        })->post($url, $data);*/
        $response = Http::withHeaders($headers)
            ->withToken(config("financial.mofid_token"))
            ->retry(3, 100)
            ->post($url, $data);

        if ($response->successful() && $response->object()->isSuccessful) {
            if (Cache::has("successAttempt")) {
                Cache::put("successAttempt", Cache::get("successAttempt") + 1, 600);
            } else {
                Cache::put("successAttempt", 1, 600);
            }
            $record["success"] = $response->successful();
        }

        $record["symbol"] = $data["order"]["symbolIsin"] ?? null;
        $record["price"] = $data["order"]["price"];
        $record["quantity"] = $data["order"]["quantity"];
        $record["status"] = $response->status();
        $record["body"] = $response->body();

        IPO::query()
            ->create($record);
    }
}
