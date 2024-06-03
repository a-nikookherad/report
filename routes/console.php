<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
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

Schedule::command("submit:ipo")
    ->dailyAt('8:40')
    ->everySecond()
    ->name("ipo");

