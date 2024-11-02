<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('report:import', function () {
//    Artisan::call("report:import-dollar");
    set_time_limit(50000);
    Artisan::call("report:import-gold18");
    Artisan::call("report:import-sekee");
    Artisan::call("report:import-ons");
    Artisan::call("report:import-aed");
    Artisan::call("report:calculate-instrument");
    $this->comment("everything is good");
});

Schedule::call(function () {
    $startTime = Carbon::createFromTime(8, 45, 0)->format("Y-m-d H:i:s");
    $endTime = Carbon::createFromTime(8, 45, 2)->format("Y-m-d H:i:s");
    if (!Carbon::now()->isBetween($startTime, $endTime)) {
        exit();
    }
    $data = [
        "order" => [
            "symbolIsin" => "IRO3GOFZ0001",
            "price" => "7530",
            "quantity" => "5452",
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

    Http::withHeaders($headers)
        ->withToken(config("financial.mofid_token"))
        ->post($url, $data);

})
    ->dailyAt("9")
    ->everySecond()
//    ->everyMiroSecond()
    ->name("ipo_khavar")
    ->withoutOverlapping();

Schedule::command("submit:ipo")
    ->dailyAt("8")
//    ->everySecond()
    ->everyMiroSecond()
    ->runInBackground()
    ->name("ipo");
//    ->withoutOverlapping();

