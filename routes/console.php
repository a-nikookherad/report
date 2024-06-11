<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
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
    if (Cache::get("successAttempt") >= 1 || !Carbon::now()->isBetween($startTime, $endTime)) {
//    if (false) {
        return 0;
    }
    $ipoLogic = new \App\Logics\IPO\IPOLogic([
        "symbolIsin" => "IRO3GOFZ0001",
        "price" => "7680",
        "quantity" => "5345",
        "side" => 0,
        "validityType" => 0,
        "validityDate" => null,
        "orderFrom" => 34,
    ]);
    $response=$ipoLogic->send()
//    $response = $ipoLogic->fakeSend()
        ->log()
        ->response();
    if ($response->successful() && $response->object()->isSuccessful) {
        if (Cache::has("successAttempt")) {
            Cache::put("successAttempt", Cache::get("successAttempt") + 1, 600);
        } else {
            Cache::put("successAttempt", 1, 600);
        }
    }
    return 0;
})
    ->dailyAt("8")
//    ->everySecond()
    ->everyMiroSecond()
//    ->runInBackground()
    ->name("ipo");
//    ->withoutOverlapping();

