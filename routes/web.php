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
use App\Http\Controllers\Customer\CustomerOrderController;
use App\Http\Controllers\Customer\CustomerProfileController;
use App\Http\Controllers\Customer\CustomerVoucherController;
use App\Http\Controllers\Customer\CustomerNotificationController;
use App\Http\Controllers\Customer\CustomerPointController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Rute didefinisikan dengan urutan prioritas:
| 1. Rute Paling Spesifik (Publik & Auth)
| 2. Rute Grup Berdasarkan Peran (Admin, Mitra, Customer)
| 3. Rute Paling Umum/Catch-All (ditempatkan di paling akhir)
|
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

//Template1 - Contoh rute statis
Route::get('/toko', fn() => view('template1.home'));
Route::get('/shop', fn() => view('template1.shop'));
Route::get('/about', fn() => view('template1.about'));

Route::get('/home', [HomeController::class, 'index']);

//== MIDTRANS WEBHOOK (TIDAK MEMERLUKAN AUTH/CSRF) ==//
//Route::post('/midtrans/webhook', [PaymentController::class, 'handleWebhook'])->name('midtrans.webhook');


// == GRUP RUTE UNTUK PENGGUNA YANG SUDAH LOGIN ==
Route::middleware(['auth'])->group(function () {
    /// Rute yang bisa diakses oleh semua user yang login
    Route::post('/voucher/apply', [VoucherController::class, 'apply'])->name('voucher.apply');
    Route::get('/payment/token', [PaymentController::class, 'generateSnapToken'])->name('payment.token');

    ///== ADMIN ROUTES ==//
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
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
        Route::resource('paket', SubscriptionPackageController::class)->except(['create', 'show']);
        Route::get('paket/{id}/json', [SubscriptionPackageController::class, 'showJson'])->name('paket.showJson');

        // Kelola Kategori
        Route::resource('kategori', CategoryController::class)->except(['create', 'show']);
        Route::get('kategori/{id}/json', [CategoryController::class, 'showJson'])->name('kategori.showJson');

        // Kelola Voucher
        Route::resource('voucher', VoucherController::class)->except(['create', 'show']);
        Route::get('voucher/{id}/json', [VoucherController::class, 'showJson'])->name('voucher.json');

        // Kelola Testimoni
        Route::resource('testimoni', TestimoniController::class)->except(['create', 'show']);
        Route::get('testimoni/{id}/json', [TestimoniController::class, 'showJson'])->name('testimoni.showJson');
        Route::put('testimoni/{id}/status', [TestimoniController::class, 'updateStatus'])->name('testimoni.updateStatus');

        // Kelola Pendapatan
        Route::get('pendapatan', [PendapatanController::class, 'index'])->name('pendapatan.index');
        Route::get('pendapatan/export', [PendapatanController::class, 'export'])->name('pendapatan.export');
    });

    //== MITRA ROUTES ==//
    Route::middleware(['role:mitra'])->prefix('mitra')->name('mitra.')->group(function () {
        Route::get('/', fn() => view('dashboard-mitra.dashboardmitra'))->name('dashboard');

        // Contoh rute lain untuk mitra
        Route::get('/produk', [ProductController::class, 'index'])->name('produk');
        Route::get('/panel', [HeroSectionController::class, 'index'])->name('panel');

        Route::resource('hero_sections', HeroSectionController::class);
        Route::resource('pages', PageController::class);
        Route::resource('pages.sections', PageSectionController::class)->except(['show']);
        Route::resource('products', ProductController::class);

        Route::get('get-section-form-partial/{sectionType}', function ($sectionType) {
            $content = request('content', []);
            if (view()->exists('dashboard-mitra.page_sections.partials.' . $sectionType . '_form')) {
                return view('dashboard-mitra.page_sections.partials.' . $sectionType . '_form', compact('content'));
            }
            abort(404);
        })->name('get-section-form-partial');
    });

    //== CUSTOMER ROUTES ==//
    Route::middleware(['role:customer'])->prefix('customer')->name('customer.')->group(function () {
        Route::get('/', fn() => redirect()->route('customer.profile'))->name('dashboard');

        Route::get('/profile', [CustomerProfileController::class, 'show'])->name('profile');
        Route::post('/profile', [CustomerProfileController::class, 'update'])->name('profile.update');
        Route::get('/orders', [CustomerOrderController::class, 'index'])->name('orders');
        Route::get('/notifications', [CustomerNotificationController::class, 'index'])->name('notifications');
        Route::get('/vouchers', [CustomerVoucherController::class, 'index'])->name('vouchers');
        Route::post('/vouchers-claim', [CustomerVoucherController::class, 'claimVoucher'])->name('vouchers.claim');
        Route::get('/points', [CustomerPointController::class, 'index'])->name('points');
        Route::post('/points-redeem', [CustomerPointController::class, 'redeem'])->name('points.redeem');
    });
});


//== RUTE CATCH-ALL (HARUS DITEMPATKAN PALING AKHIR) ==//
// Rute ini menangani slug halaman dinamis dari database.
// Ditempatkan di akhir agar tidak menimpa rute-rute spesifik seperti /login, /admin, /mitra, dll.
Route::get('/{slug}', [HomeController::class, 'showPage'])->name('page.show');

