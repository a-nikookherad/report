<?php

use Illuminate\Support\Facades\Route;

Route::get("", function () {
    $jsonFile = file_get_contents(storage_path("instruments/folad/json/1402-06-31.json"));
//    dd($jsonFile);
    $records = json_decode($jsonFile, true);
    dd($records);

    $logic = resolve(\App\Tools\Excel\ExcelWriter::class);
    $logic->write("");
    dd("done");
    $logic = resolve(\App\Logics\ImportData\ImportInstrumentDataLogic::class);
    $records = $logic->getBalanceSheet("instruments/folad/folad.xls", "xls");
    dd($records);
});
Route::group([
    "prefix" => "dashboard"
], function () {
    Route::get('/', function () {
        return view('contents.dashboard');
    });

    Route::get('/instruments', [\App\Http\Controllers\InstrumentController::class, "list"]);

    Route::get('/instruments/add', [\App\Http\Controllers\InstrumentController::class, "add"])
        ->name("instrument.add");
    Route::post('/instruments/add', [\App\Http\Controllers\InstrumentController::class, "store"])
        ->name("instrument.store");

    Route::get('/instruments/add/info', [\App\Http\Controllers\InstrumentController::class, "addInfo"])
        ->name("instrument.add.info");
    Route::post('/instruments/add/info', [\App\Http\Controllers\InstrumentController::class, "storeInfo"])
        ->name("instrument.add.info.store");


});

Route::group([
    "template"
], function () {
    Route::get('form', function () {
        return view('contents.templates.form');
    });

    Route::get('chart', function () {
        return view('contents.templates.chart');
    });

    Route::get('table', function () {
        return view('contents.templates.table');
    });

    Route::get('widget', function () {
        return view('contents.templates.widget');
    });

    Route::get('button', function () {
        return view('contents.templates.button');
    });

    Route::get('element', function () {
        return view('contents.templates.element');
    });

    Route::get('typography', function () {
        return view('contents.templates.typography');
    });

    Route::get('blank', function () {
        return view('contents.templates.blank');
    });
});
