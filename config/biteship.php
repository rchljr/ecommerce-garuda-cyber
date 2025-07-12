<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Biteship API Configuration
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for the Biteship API.
    | You should obtain your API key from your Biteship dashboard.
    |
    */

    'api_key' => env('BITESHIP_API_KEY'),

    'base_url' => env('BITESHIP_BASE_URL', 'https://api.biteship.com'),
];
