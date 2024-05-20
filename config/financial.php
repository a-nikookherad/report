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
    'mofid_token' => env('mofid_token',"eyJhbGciOiJSUzI1NiIsImtpZCI6ImI3MmYyMjczZTE4YTQ0YjQ5OTFmMDg3ODIzNzQyYmI1IiwidHlwIjoiYXQrand0In0.eyJuYmYiOjE3MTYwMzM3NjUsImV4cCI6MTcxNjA1NTM2NSwiaXNzIjoiaHR0cHM6Ly9hY2NvdW50LmVtb2ZpZC5jb20iLCJhdWQiOlsiZWFzeTJfYXBpIiwibXRzX2FwaSIsImh0dHBzOi8vYWNjb3VudC5lbW9maWQuY29tL3Jlc291cmNlcyJdLCJjbGllbnRfaWQiOiJlYXN5X3BrY2UiLCJzdWIiOiIyNmZlMjdhMy1kN2MzLTRiNzYtOTE2Ny1lMWQyMTBjYjRiZGUiLCJhdXRoX3RpbWUiOjE3MTYwMzM3NjEsImlkcCI6ImxvY2FsIiwicGsiOiIyNmZlMjdhMy1kN2MzLTRiNzYtOTE2Ny1lMWQyMTBjYjRiZGUiLCJ0d29fZmFjdG9yX2VuYWJsZWQiOiJmYWxzZSIsInByZWZlcnJlZF91c2VybmFtZSI6IjI2ZmUyN2EzLWQ3YzMtNGI3Ni05MTY3LWUxZDIxMGNiNGJkZSIsIm5hbWUiOiIyNmZlMjdhMy1kN2MzLTRiNzYtOTE2Ny1lMWQyMTBjYjRiZGUiLCJwaG9uZV9udW1iZXIiOiIwOTM2MDU1MTcwMSIsInBob25lX251bWJlcl92ZXJpZmllZCI6dHJ1ZSwiZGlzcGxheV9uYW1lIjoi2LnZhNuM2LHYttinINi52YjYp9i32YEg2LnYqNin2LPbjCIsImZpcnN0bmFtZSI6Iti52YTbjNix2LbYpyIsImxhc3RuYW1lIjoi2LnZiNin2LfZgSDYudio2KfYs9uMIiwibmF0aW9uYWxfaWQiOiIwMzcyMTg0MDIyIiwibmF0aW9uYWxfaWRfdmVyaWZpZWQiOiJ0cnVlIiwiY3VzdG9tZXJfaXNpbiI6IjExMjkwMzcyMTg0MDIyIiwiYm91cnNlX2NvZGUiOiLYuSDaqSDYqiAzMzYyOSIsImNvbnRyYWN0IjpbIlRlc3RDb250cmFjdF8xLjAiLCJFY29udHJhY3RfMi4wIiwiT21zTW9maWRfMS4wIiwiV2FsbGV0Q29udHJhY3RfMS4xIl0sImlzZm9yY2VfY29udHJhY3QiOiJmYWxzZSIsInNjb3BlIjpbIm9wZW5pZCIsInByb2ZpbGUiLCJlYXN5Ml9hcGkiLCJtdHNfYXBpIiwib2ZmbGluZV9hY2Nlc3MiXSwiYW1yIjpbInB3ZCJdfQ.rzhnxj3TDnlIR3TGqI2yor11ksjvi7Fa4-Tm44RPoID3RpiiuSfp0yyLvEYrfH0KqPrmc_son4zmFMklFVax4ROKrp_EMprmzrnejlAZavBMi5s7AY_prLahpKX7LsU1f1flkEKmfjJSNB-SiIwPC_OJDsLS5GBS2zeS-YiH_IZ4SxjyTA32k4GcLPOwX-Hf5Iir4lByFB0xTjqpRcWmR8lc_6DCVTzHFzsEtz89YzTiVzx4NM1ERO2asUn7oeT8OoiPGVr83hSkLXiFBPE_Q1z-dhF6UayDAatw7Pfvb2Iqn_4_RMvWOcUEx_EKkgSlB5SDfrB92Nrex-tmp-w99A"),
];
