<?php

use Illuminate\Support\Facades\Route;

Route::get("", function () {
//    $res = \Illuminate\Support\Facades\Http::withHeaders([
//        "accept" => "application/json",
//        "Authorization" => "Bearer " . config("financial.mofid_token")
//    ])->get(\App\Models\Bourse\Instrument::query()->where("slug", "fameli")->first()->mofid_url);
//    $res->object();
    dd("this is test page");
});
Route::group([
    "prefix" => "dashboard"
], function () {
    Route::get('/', function () {
        return view('contents.dashboard');
    })->name("dashboard");

    Route::get('/instruments', [\App\Http\Controllers\InstrumentController::class, "list"]);

    Route::get('/instruments/add', [\App\Http\Controllers\InstrumentController::class, "add"])
        ->name("instrument.add");
    Route::post('/instruments/add', [\App\Http\Controllers\InstrumentController::class, "store"])
        ->name("instrument.store");

    Route::get('/instruments/add/info', [\App\Http\Controllers\InstrumentController::class, "addInfo"])
        ->name("instrument.add.info");
    Route::post('/instruments/add/info', [\App\Http\Controllers\InstrumentController::class, "storeInfo"])
        ->name("instrument.add.info.store");

    Route::get('/instruments/{instrument_id}/ratio', [\App\Http\Controllers\InstrumentController::class, "ratio"])
        ->name("instrument.ratio");
});

Route::group([
    "template"
], function () {
    Route::get('form', function () {
        return view('contents.templates.form');
    });

    Route::get('chart', function () {
        return view('contents.templates.chart');
    })->name("chart");

    Route::get('table', function () {
        return view('contents.templates.table');
    })->name("table");

    Route::get('widget', function () {
        return view('contents.templates.widget');
    })->name("widget");

    Route::get('button', function () {
        return view('contents.templates.button');
    })->name("button");

    Route::get('element', function () {
        return view('contents.templates.element');
    })->name("element");

    Route::get('typography', function () {
        return view('contents.templates.typography');
    })->name("typography");

    Route::get('blank', function () {
        return view('contents.templates.blank');
    })->name("blank");
});
