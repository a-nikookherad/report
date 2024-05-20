<?php

return [

    /*
    |--------------------------------------------------------------------------
    | USD to AED
    |--------------------------------------------------------------------------
    |
    | This option controls the default mailer that is used to send all email
    |
    */

    'usd_aed' => env('usd_aed', 3.67),

    /*
    |--------------------------------------------------------------------------
    | Financial folder path
    |--------------------------------------------------------------------------
    |
    | Default path from laravel storage_path
    |
    */
    'folder' => env('financial_folder', "instruments"),

    /*
    |--------------------------------------------------------------------------
    | Mofid bearer token
    |--------------------------------------------------------------------------
    |
    | Token for get history of instruments
    |
    */
    'mofid_token' => env('mofid_token'),
    'mofid_url' => env('mofid_url',"https://api-mts.orbis.easytrader.ir/chart/api/datafeed/history"),
];
