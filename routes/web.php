<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\TestimoniController;
use App\Http\Controllers\Mitra\HeroController;
use App\Http\Controllers\Mitra\TemaController;
use App\Http\Controllers\Mitra\BannerController;
use App\Http\Controllers\Mitra\VarianController;
use App\Http\Controllers\Mitra\ContactController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PendapatanController;
use App\Http\Controllers\Admin\KelolaMitraController;
use App\Http\Controllers\Admin\LandingPageController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\Admin\VerificationController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\SubscriptionPackageController;
use App\Http\Controllers\Mitra\DashboardMitraController;
use App\Http\Controllers\Mitra\TemplateEditorController;
use App\Http\Controllers\Mitra\VoucherProductController;
use App\Http\Controllers\Customer\CustomerOrderController;
use App\Http\Controllers\Customer\CustomerPointController;
use App\Http\Controllers\Mitra\StorePublicationController;
use App\Http\Controllers\Mitra\TestimoniProductController;
use App\Http\Controllers\Customer\CustomerProfileController;
use App\Http\Controllers\Customer\CustomerVoucherController;
use App\Http\Controllers\Customer\CustomerNotificationController;


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

// ===================================================================
// RUTE SUBDOMAIN (HANYA UNTUK TAMPILAN TOKO)
// ===================================================================
Route::prefix('tenant/{subdomain}')
    ->middleware(['web', 'tenant.exists']) // Middleware 'web' menangani session
    ->name('tenant.') // Memberi nama prefix "tenant." untuk semua rute di dalam grup
    ->group(function () {

        // --- RUTE PUBLIK (Dapat diakses semua orang) ---
        Route::get('/home', [HomeController::class, 'index'])->name('home');
        Route::get('/shop', [ShopController::class, 'index'])->name('shop');
        Route::get('/shop/{product:slug}', [ShopController::class, 'show'])->name('product.details');
        Route::get('/contact', [ContactController::class, 'showPublic'])->name('contact');

        // --- RUTE PUBLIK UNTUK AJAX (Tidak perlu login) ---
        // Rute ini diperlukan di halaman checkout sebelum user melakukan pembayaran
        Route::post('/checkout/search-destination', [CheckoutController::class, 'searchDestination'])->name('checkout.search_destination');
        Route::post('/checkout/calculate-shipping', [CheckoutController::class, 'calculateShipping'])->name('checkout.calculate_shipping');
        // Route::get('/checkout/areas', [CheckoutController::class, 'getBiteshipAreas'])->name('checkout.areas');
    
        // Wishlist
        Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
        Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

        // Keranjang Belanja
        Route::prefix('cart')->name('cart.')->group(function () {
            Route::get('/', [CartController::class, 'index'])->name('index');
            Route::post('/add', [CartController::class, 'add'])->name('add');
            Route::patch('/update/{productCartId}', [CartController::class, 'update'])->name('update');
            Route::delete('/remove', [CartController::class, 'removeItems'])->name('remove');
            Route::post('/add-multiple', [CartController::class, 'addMultiple'])->name('addMultiple');
        });

        // --- RUTE OTENTIKASI PELANGGAN ---
        Route::prefix('customer')->name('customer.')->group(function () {
            Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('login.form');
            Route::post('/login', [CustomerAuthController::class, 'login'])->name('login.submit');
            Route::get('/register', [CustomerAuthController::class, 'showRegisterForm'])->name('register.form');
            Route::post('/register', [CustomerAuthController::class, 'register'])->name('register.submit');
        });

        // --- RUTE YANG MEMBUTUHKAN LOGIN PELANGGAN ---
        Route::middleware('auth:customers')->group(function () {

            // Logout
            Route::post('/customer/logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');

            // Checkout
            Route::prefix('checkout')->name('checkout.')->group(function () {
                Route::get('/', [CheckoutController::class, 'index'])->name('index');
                Route::post('/charge', [CheckoutController::class, 'charge'])->name('charge');
                Route::post('/detail', [CheckoutController::class, 'getDetails'])->name('get_details');
            });

            //review
            Route::prefix('customer/reviews')->name('customer.reviews.')->group(function () {
                Route::post('/submit', [TestimoniController::class, 'submitReview'])->name('submit');
                Route::get('/{testimonial}/json', [TestimoniController::class, 'getReviewJson'])->name('json');
                Route::put('/{testimonial}/update', [TestimoniController::class, 'updateReview'])->name('update');
            });

            // Dasbor Pelanggan
            Route::prefix('account')->name('account.')->group(function () {
                Route::get('/profile', [CustomerProfileController::class, 'show'])->name('profile');
                Route::post('/profile/update', [CustomerProfileController::class, 'update'])->name('profile.update');
                Route::get('/orders', [CustomerOrderController::class, 'index'])->name('orders');
                Route::post('/orders/{order}/cancel', [CustomerOrderController::class, 'cancel'])->name('orders.cancel');
                Route::post('/orders/{order}/receive', [CustomerOrderController::class, 'receive'])->name('orders.receive');
                Route::post('/orders/{order}/request-refund', [CustomerOrderController::class, 'requestRefund'])->name('orders.request_refund');
                Route::get('/notifications', [CustomerNotificationController::class, 'index'])->name('notifications');
                Route::get('/points', [CustomerPointController::class, 'index'])->name('points');
                Route::post('/points/redeem/{rewardId}', [CustomerPointController::class, 'redeem'])->name('points.redeem');
                Route::get('/vouchers', [CustomerVoucherController::class, 'index'])->name('vouchers');
                Route::post('/vouchers/claim', [CustomerVoucherController::class, 'claimVoucher'])->name('vouchers.claim');
            });
        });
    });

// ===================================================================
// RUTE UMUM (TANPA SUBDOMAIN)
// ===================================================================

//== RUTE PUBLIK & AUTENTIKASI ==//
Route::get('/', [LandingPageController::class, 'home'])->name('landing');
Route::get('/tenant', [LandingPageController::class, 'allTenants'])->name('tenants.index');
Route::post('/testimonials', [TestimoniController::class, 'submitFromLandingPage'])->name('testimonials.store');


Route::get('/review/{testimonial}', [TestimoniController::class, 'getReviewJson'])->name('review.get')->middleware('auth:customers');
Route::put('/review/{testimonial}', [TestimoniController::class, 'updateReview'])->name('review.update')->middleware('auth:customers');

// Auth (Proses Registrasi dan Login)
// Rute untuk menampilkan form login & register admin dan mitra
Route::get('/login', fn() => view('landing-page.auth.login'))->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute untuk Lupa Password (Mitra & Admin)
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Proses Registrasi Multi-Langkah (Publik)
Route::prefix('register')->name('register.')->group(function () {
    Route::get('/', [AuthController::class, 'showRegisterForm'])->name('form');
    Route::post('/package', [AuthController::class, 'submitPackage'])->name('package.submit');
    Route::post('/subdomain', [AuthController::class, 'submitSubdomain'])->name('subdomain.submit');
    Route::post('/check-subdomain', [AuthController::class, 'checkSubdomain'])->name('subdomain.check');
    Route::post('/user', [AuthController::class, 'submitUser'])->name('user.submit');
    Route::post('/shop', [AuthController::class, 'submitShop'])->name('shop.submit');
    Route::post('/template', [AuthController::class, 'submitTemplate'])->name('template.submit');
    Route::get('/clear', [AuthController::class, 'clearRegistration'])->name('clear');
});
//preview template
Route::get('/{template:name}/beranda', [TemplateController::class, 'preview'])->name('template.preview');
//     // Rute dashboard utama
//     Route::get('/', function () {
//         return view('dashboard-mitra.dashboardmitra');
//     })->name('dashboard'); // Ini akan menjadi 'mitra.dashboard'

//     // Rute /produk (jika ini untuk daftar produk statis/tanpa controller ProductController)
//     // Jika Anda ingin menggunakan ProductController@index untuk /produk, hapus ini
//     Route::get('/produk', [ProductController::class, 'index'])->name('produk');
//     Route::get('/panel', [HeroSectionController::class, 'index'])->name('panel');


//     Route::resource('hero_sections', HeroSectionController::class);

//     Route::resource('pages', PageController::class); // Ini akan membuat mitra.pages.* routes

//     // Manajemen Seksi Halaman (PageSectionController) - Nested Resource
//     Route::resource('pages.sections', PageSectionController::class)->except(['show']); // Tidak butuh show untuk seksi

//     // Rute untuk mendapatkan partial form dinamis via AJAX
//     Route::get('get-section-form-partial/{sectionType}', function ($sectionType) {
//         $content = request('content', []); // Untuk edit, menerima konten yang ada
//         if (view()->exists('dashboard-mitra.page_sections.partials.' . $sectionType . '_form')) {
//             return view('dashboard-mitra.page_sections.partials.' . $sectionType . '_form', compact('content'));
//         }
//         return response('', 404);
//     })->name('get-section-form-partial');

//     // CRUD route untuk produk menggunakan ProductController
//     // Penting: URI 'products' saja karena sudah ada prefix 'dashboard-mitra'
//     Route::resource('products', ProductController::class)->names([
//         'index' => 'products.index',    // Ini akan menjadi 'mitra.products.index'
//         'create' => 'products.create',   // Ini akan menjadi 'mitra.products.create'
//         'store' => 'products.store',    // Ini akan menjadi 'mitra.products.store'
//         'show' => 'products.show',     // Ini akan menjadi 'mitra.products.show'
//         'edit' => 'products.edit',     // Ini akan menjadi 'mitra.products.edit'
//         'update' => 'products.update',   // Ini akan menjadi 'mitra.products.update'
//         'destroy' => 'products.destroy',  // Ini akan menjadi 'mitra.products.destroy'
//     ]);
// });

//Template1
Route::get('/template1/beranda', [HomeController::class, 'index'])->name('home');

Route::get('/fruit', function () {
    return view('template2.home');
});

// Route untuk halaman tim developer
Route::get('/developer', function () {
    // Pastikan file blade Anda ada di resources/views/layouts/tim-developer.blade.php
    return view('layouts.tim-developer');
})->name('tim.developer');

// == GRUP RUTE UNTUK PENGGUNA YANG SUDAH LOGIN ==
Route::middleware(['auth'])->group(function () {
    /// Rute yang bisa diakses oleh semua user yang login
    Route::get('/payment', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/charge', [PaymentController::class, 'chargeCoreApi'])->name('payment.charge');
    Route::post('/payment/apply-voucher', [PaymentController::class, 'applyVoucher'])->name('payment.applyVoucher');
    Route::post('/payment/remove-voucher', [PaymentController::class, 'removeVoucher'])->name('payment.removeVoucher');
    Route::post('/payment/generate-token', [PaymentController::class, 'generateSnapToken'])->name('payment.generateSnapToken');
    //Route::post('/midtrans-callback', [PaymentCallbackController::class, 'receiveCallback'])->name('midtrans.callback');

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
        Route::get('kelola-mitra', [KelolaMitraController::class, 'index'])->name('mitra.kelola');
        Route::patch('kelola-mitra/{user}/deactivate', [KelolaMitraController::class, 'deactivate'])->name('mitra.deactivate');
        Route::patch('kelola-mitra/{user}/reactivate', [KelolaMitraController::class, 'reactivate'])->name('mitra.reactivate');

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
    Route::middleware(['role:mitra'])->prefix('mitra')->name('mitra.')->group(function () {
        // Rute dashboard utama
        // Route::get('/dashboard', [MitraController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [DashboardMitraController::class, 'index'])->name('dashboard');
        // Rute untuk publikasi toko
        Route::get('/transactions/export/excel', [DashboardMitraController::class, 'exportProductOrdersExcel'])->name('transactions.export.excel');
        Route::get('/pendapatan/export-excel', [DashboardMitraController::class, 'exportSubscriptionPaymentsExcel'])->name('pendapatan.export.excel');

        Route::patch('/editor/publish', [StorePublicationController::class, 'publish'])->name('editor.publish');
        Route::patch('/editor/unpublish', [StorePublicationController::class, 'unpublish'])->name('editor.unpublish');

        Route::get('/produk', [ProductController::class, 'index'])->name('produk');
        Route::get('/hero', [HeroController::class, 'index'])->name('hero');
        Route::get('/banner', [BannerController::class, 'index'])->name('banner');

        Route::get('/testimoni', [TestimoniProductController::class, 'index'])->name('testimoni.index');

        Route::get('/editor', [TemplateEditorController::class, 'edit'])->name('editor.edit');
        Route::post('/editor', [TemplateEditorController::class, 'update'])->name('editor.update');
        Route::post('/editor/hero', [TemplateEditorController::class, 'updateHero'])->name('editor.update.hero');
        Route::post('/editor/settings', [TemplateEditorController::class, 'updateSettings'])->name('editor.update.settings');

        Route::get('vouchers', [VoucherProductController::class, 'index'])->name('vouchers.index');
        Route::get('vouchers/create', [VoucherProductController::class, 'create'])->name('vouchers.create');
        Route::get('vouchers/edit', [VoucherProductController::class, 'edit'])->name('vouchers.edit');
        Route::get('vouchers/destroy', [VoucherProductController::class, 'destroy'])->name('vouchers.destroy');
        Route::resource('vouchers', VoucherProductController::class);


        Route::resource('heroes', HeroController::class);
        Route::resource('banners', BannerController::class);

        Route::get('/contacts', [ContactController::class, 'edit'])->name('contacts');
        Route::put('/contacts', [ContactController::class, 'update'])->name('contacts.update');

        Route::post('/tema/create', [TemaController::class, 'create'])->name('tema.create');
        Route::get('/tema/create', [TemaController::class, 'create'])->name('tema.create');
        Route::post('/tema', [TemaController::class, 'store'])->name('tema.store');
        Route::post('/tema/set', [TemaController::class, 'setTemplate'])->name('tema.set');
        Route::get('/tema', [TemaController::class, 'index'])->name('tema');
        Route::post('/editor/{template}', [TemaController::class, 'editor'])->name('editor');
        Route::get('/tema/store', [TemaController::class, 'store'])->name('tema.store');
        Route::post('/tema/store', [TemaController::class, 'store'])->name('tema.store');
        Route::post('/editor/template/update', [TemaController::class, 'updateTheme'])->name('editor.updatetheme');


        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
        // Rute khusus untuk menangani permintaan refund
        Route::patch('/orders/{order}/refund/approve', [OrderController::class, 'approveRefund'])->name('orders.refund.approve');
        Route::patch('/orders/{order}/refund/reject', [OrderController::class, 'rejectRefund'])->name('orders.refund.reject');

        Route::get('/transactions', [DashboardMitraController::class, 'transactions'])->name('transactions.index');
        // Anda mungkin juga ingin menambahkan link ini di sidebar navigasi dashboard mitra Anda

        Route::resource('products', ProductController::class)->names([
            'index' => 'products.index',    // Ini akan menjadi 'mitra.products.index'
            'create' => 'products.create',   // Ini akan menjadi 'mitra.products.create'
            'store' => 'products.store',    // Ini akan menjadi 'mitra.products.store'
            'show' => 'products.show',     // Ini akan menjadi 'mitra.products.show'
            'edit' => 'products.edit',     // Ini akan menjadi 'mitra.products.edit'
            'update' => 'products.update',   // Ini akan menjadi 'mitra.products.update'
            'destroy' => 'products.destroy',  // Ini akan menjadi 'mitra.products.destroy'
        ]);

        Route::post('products/{product}/varians', [VarianController::class, 'store'])->name('varians.store');
        Route::get('varians/{varian}/edit', [VarianController::class, 'edit'])->name('varians.edit');
        Route::put('varians/{varian}', [VarianController::class, 'update'])->name('varians.update');
        Route::delete('varians/{varian}', [VarianController::class, 'destroy'])->name('varians.destroy');
    });

    //== CUSTOMER ROUTES ==//
    Route::prefix('customer')->name('customer.')->group(function () {
        // default untuk customer, arahkan ke profil
        Route::get('/', function () {
            return redirect()->route('customer.profile');
        })->name('dashboard');
    });
});
