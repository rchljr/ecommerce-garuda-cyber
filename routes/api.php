<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PaymentController; // Pastikan ini ada di atas

Route::post('/payment/webhook', [PaymentController::class, 'handleWebhook'])->name('midtrans.webhook');
