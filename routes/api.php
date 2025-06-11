<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

Route::get('/test', function () {
    return response()->json(['message' => 'API route works!']);
});