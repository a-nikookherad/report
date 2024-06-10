<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Start Year
    |--------------------------------------------------------------------------
    |
    | Specify how many years ago for all data reports start with
    |
    */

    'how_many_years_ago_for_report' => env('how_many_years_ago_for_report', 12),

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
    'mofid_url' => env('mofid_url', "https://api-mts.orbis.easytrader.ir/chart/api/datafeed/history"),

    /*
    |--------------------------------------------------------------------------
    | Market total Price to Earn
    |--------------------------------------------------------------------------
    |
    | Market default price to earn
    |
    */
    'market_price_to_earn' => env('market_price_to_earn', 7),
];
