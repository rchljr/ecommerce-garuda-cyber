<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

//---ADMIN ROUTES---//

// Landing Page
Route::get('/', function () {
    return view('landing-page.index');
})->name('beranda');


// Auth
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register/step0', [AuthController::class, 'registerStep0'])->name('register.step0');
Route::post('/register/step1', [AuthController::class, 'registerStep1'])->name('register.step1');
Route::post('/register/step2', [AuthController::class, 'registerStep2'])->name('register.step2');
Route::post('/register/step3', [AuthController::class, 'registerStep3'])->name('register.step3');

Route::get('/login', function () {
    return view('landing-page.auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// Dashboard
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard-admin.dashboard');
    })->name('dashboard');
    Route::get('kelola-mitra', function () {
        return view('dashboard-admin.kelola-mitra');
    })->name('kelola-mitra');
    Route::get('verifikasi-mitra', function () {
        return view('dashboard-admin.verifikasi-mitra');
    })->name('verifikasi-mitra');
    Route::get('kelola-landing', function () {
        return view('dashboard-admin.kelola-landing');
    })->name('kelola-landing');
    Route::get('kelola-paket', function () {
        return view('dashboard-admin.kelola-paket');
    })->name('kelola-paket');
    Route::get('kelola-pendapatan', function () {
        return view('dashboard-admin.kelola-pendapatan');
    })->name('kelola-pendapatan');
    Route::get('kelola-voucher', function () {
        return view('dashboard-admin.kelola-voucher');
    })->name('kelola-voucher');
    Route::get('kelola-testimoni', function () {
        return view('dashboard-admin.kelola-testimoni');
    })->name('kelola-testimoni');
});

//---CUSTOMER ROUTES---//

// Auth
Route::get('/login-cust', function () {
    return view('customer.auth.login');
})->name('login-cust');

Route::get('/register-cust', function () {
    return view('customer.auth.register');
})->name('register-cust');
