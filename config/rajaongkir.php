<?php

// File: config/rajaongkir.php

return [
    /*
    |--------------------------------------------------------------------------
    | RajaOngkir API Key
    |--------------------------------------------------------------------------
    |
    | Kunci API yang Anda dapatkan dari dashboard RajaOngkir.
    | Nilai ini diambil dari file .env Anda.
    |
    */
    'api_key' => env('RAJAONGKIR_API_KEY', null),

    /*
    |--------------------------------------------------------------------------
    | RajaOngkir Base URL
    |--------------------------------------------------------------------------
    |
    | URL dasar untuk API RajaOngkir.
    | 'starter' untuk akun gratis, 'basic' atau 'pro' untuk akun berbayar.
    |
    */
    'base_url' => env('RAJAONGKIR_BASE_URL', 'https://api-sandbox.collaborator.komerce.id'),
];
