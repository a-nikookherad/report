<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('report:import', function () {
    Artisan::call("report:import-dollar");
    Artisan::call("report:import-gold18");
    Artisan::call("report:import-sekee");
    Artisan::call("report:import-aed");
    Artisan::call("report:import-tether");
    $this->comment("everything be good");
});

Schedule::command("submit:ipo")
    ->dailyAt('8:28')
    ->everySecond();

