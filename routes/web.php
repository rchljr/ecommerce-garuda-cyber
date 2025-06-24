<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\TestimoniController;
use App\Http\Controllers\Admin\MitraController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PendapatanController;
use App\Http\Controllers\SubscriptionPackageController;
use App\Http\Controllers\Admin\HeroSectionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\PageController; 
use App\Http\Controllers\Admin\PageSectionController;
use App\Models\HeroSection;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

//== RUTE PUBLIK & AUTENTIKASI ==//
Route::get('/', [LandingPageController::class, 'home'])->name('beranda');
Route::post('/testimonials', [TestimoniController::class, 'submitFromLandingPage'])->name('testimonials.store');

// Auth (Proses Registrasi dan Login)
Route::get('/login', fn() => view('landing-page.auth.login'))->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Proses Registrasi Multi-Langkah (Publik)
Route::prefix('register')->name('register.')->group(function () {
    Route::get('/', [AuthController::class, 'showRegisterForm'])->name('form');
    Route::post('/step0', [AuthController::class, 'registerStep0'])->name('step0');
    Route::post('/step1', [AuthController::class, 'registerStep1'])->name('step1');
    Route::post('/step2', [AuthController::class, 'registerStep2'])->name('step2');
    Route::post('/step3', [AuthController::class, 'registerStep3'])->name('step3');
});

//Mitra Sementara
Route::prefix('dashboard-mitra')->name('mitra.')->group(function () {
    // Rute dashboard utama
    Route::get('/', function () {
        return view('dashboard-mitra.dashboardmitra');
    })->name('dashboard'); // Ini akan menjadi 'mitra.dashboard'

    // Rute /produk (jika ini untuk daftar produk statis/tanpa controller ProductController)
    // Jika Anda ingin menggunakan ProductController@index untuk /produk, hapus ini
     Route::get('/produk', [ProductController::class, 'index'])->name('produk');
     Route::get('/panel', [HeroSectionController::class, 'index'])->name('panel');
    

     Route::resource('hero_sections', HeroSectionController::class);

     Route::resource('pages', PageController::class); // Ini akan membuat mitra.pages.* routes

    // Manajemen Seksi Halaman (PageSectionController) - Nested Resource
     Route::resource('pages.sections', PageSectionController::class)->except(['show']); // Tidak butuh show untuk seksi

    // Rute untuk mendapatkan partial form dinamis via AJAX
    Route::get('get-section-form-partial/{sectionType}', function ($sectionType) {
        $content = request('content', []); // Untuk edit, menerima konten yang ada
        if (view()->exists('dashboard-mitra.page_sections.partials.' . $sectionType . '_form')) {
            return view('dashboard-mitra.page_sections.partials.' . $sectionType . '_form', compact('content'));
        }
        return response('', 404);
    })->name('get-section-form-partial');

    // CRUD route untuk produk menggunakan ProductController
    // Penting: URI 'products' saja karena sudah ada prefix 'dashboard-mitra'
    Route::resource('products', ProductController::class)->names([
        'index'   => 'products.index',    // Ini akan menjadi 'mitra.products.index'
        'create'  => 'products.create',   // Ini akan menjadi 'mitra.products.create'
        'store'   => 'products.store',    // Ini akan menjadi 'mitra.products.store'
        'show'    => 'products.show',     // Ini akan menjadi 'mitra.products.show'
        'edit'    => 'products.edit',     // Ini akan menjadi 'mitra.products.edit'
        'update'  => 'products.update',   // Ini akan menjadi 'mitra.products.update'
        'destroy' => 'products.destroy',  // Ini akan menjadi 'mitra.products.destroy'
    ]);    
});


// Template1
// Route::get('/toko', function () {
//     return view('template1.home'); // Mengarahkan ke resources/views/template1/home.blade.php
// });

// // Contoh rute untuk halaman lain di dalam template1
// Route::get('/shop', function () {
//     return view('template1.shop'); // Anda perlu membuat file template1/shop.blade.php
// });

// Route::get('/about', function () {
//     return view('template1.about'); // Anda perlu membuat file template1/about.blade.php
// });

Route::get('/home', [HomeController::class, 'index']); // Mengarahkan ke metode index di HomeController
Route::get('/{slug}', [HomeController::class, 'showPage'])->name('page.show');

//== MIDTRANS WEBHOOK (TIDAK MEMERLUKAN AUTH/CSRF) ==//
//dikomen karena masih menggunakan route API, digunakan jika sudah hosting
//Route::post('/midtrans/webhook', [PaymentController::class, 'handleWebhook'])->name('midtrans.webhook');

// == GRUP RUTE UNTUK PENGGUNA YANG SUDAH LOGIN ==
Route::middleware(['auth'])->group(function () {
    /// Rute yang bisa diakses oleh semua user yang login
    Route::post('/voucher/apply', [VoucherController::class, 'apply'])->name('voucher.apply');
    Route::get('/payment/token', [PaymentController::class, 'generateSnapToken'])->name('payment.token');

    ///== ADMIN ROUTES ==//
    // Hanya user dengan role 'admin' yang bisa mengakses grup ini.
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Verifikasi Mitra
        Route::prefix('verifikasi-mitra')->name('mitra.')->group(function () {
            Route::get('/', [VerificationController::class, 'index'])->name('verifikasi');
            Route::put('/{user}/approve', [VerificationController::class, 'approve'])->name('approve');
            Route::delete('/{user}/reject', [VerificationController::class, 'reject'])->name('reject');
        });

        // Kelola Mitra
        Route::get('kelola-mitra', [MitraController::class, 'index'])->name('mitra.kelola');
        Route::patch('kelola-mitra/{user}/status', [MitraController::class, 'updateStatus'])->name('mitra.updateStatus');

        // Kelola Landing Page
        Route::get('/landing-page', [LandingPageController::class, 'adminLanding'])->name('landing-page.statistics');
        Route::put('/landing-page/update', [LandingPageController::class, 'update'])->name('landing-page.statistics.update');

        // Kelola Paket Subscription
        Route::prefix('paket')->name('paket.')->group(function () {
            Route::get('/', [SubscriptionPackageController::class, 'index'])->name('index');
            Route::post('/', [SubscriptionPackageController::class, 'store'])->name('store');
            Route::get('/{id}/json', [SubscriptionPackageController::class, 'showJson'])->name('showJson');
            Route::put('/{id}', [SubscriptionPackageController::class, 'update'])->name('update');
            Route::delete('/{id}', [SubscriptionPackageController::class, 'destroy'])->name('destroy');
        });

        // Kelola Kategori
        Route::prefix('kategori')->name('kategori.')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('index');
            Route::post('/', [CategoryController::class, 'store'])->name('store');
            Route::get('/{id}/json', [CategoryController::class, 'showJson'])->name('showJson');
            Route::put('/{id}', [CategoryController::class, 'update'])->name('update');
            Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy');
        });

        // Kelola Voucher
        Route::prefix('voucher')->name('voucher.')->group(function () {
            Route::get('/', [VoucherController::class, 'index'])->name('index');
            Route::post('/', [VoucherController::class, 'store'])->name('store');
            Route::get('/{id}/json', [VoucherController::class, 'showJson'])->name('json');
            Route::put('/{id}', [VoucherController::class, 'update'])->name('update');
            Route::delete('/{id}', [VoucherController::class, 'destroy'])->name('destroy');
        });

        // Kelola Testimoni
        Route::prefix('testimoni')->name('testimoni.')->group(function () {
            Route::get('/', [TestimoniController::class, 'index'])->name('index');
            Route::post('/', [TestimoniController::class, 'store'])->name('store');
            Route::get('/{id}/json', [TestimoniController::class, 'showJson'])->name('showJson');
            Route::put('/{id}', [TestimoniController::class, 'update'])->name('update');
            Route::put('/{id}/status', [TestimoniController::class, 'updateStatus'])->name('updateStatus');
            Route::delete('/{id}', [TestimoniController::class, 'destroy'])->name('destroy');
        });

        // Kelola Pendapatan
        Route::prefix('pendapatan')->name('pendapatan.')->group(function () {
            Route::get('/', [PendapatanController::class, 'index'])->name('index');
            Route::get('/export', [PendapatanController::class, 'export'])->name('export');
        });
    });

    //== MITRA ROUTES ==//
    // Middleware untuk memastikan hanya role 'mitra' yang bisa akses
    // Route::middleware(['role:mitra'])->prefix('mitra')->name('mitra.')->group(function () {
    //     Route::get('/dashboard', function () {
    //         return view('dashboard-mitra.dashboardmitra');
    //     })->name('dashboard');
    //     // CRUD route untuk produk
    //     Route::resource('products', ProductController::class);
    // });

    // //== CUSTOMER ROUTES (PROTECTED) ==//
    // // Contoh jika Anda butuh halaman profil untuk customer
    // Route::middleware(['role:customer'])->prefix('customer')->name('customer.')->group(function () {
    //     Route::get('/profile', function () { /* ... */})->name('profile');
    //     // ... rute lain untuk customer yang sudah login ...
    // });
});