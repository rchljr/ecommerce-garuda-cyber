<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\TestimoniController;
use App\Http\Controllers\Mitra\TemaController;
use App\Http\Controllers\Mitra\HeroController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\Mitra\MitraController;
use App\Http\Controllers\Mitra\BannerController;
use App\Http\Controllers\Mitra\VarianController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\Mitra\ContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PendapatanController;
use App\Http\Controllers\Admin\KelolaMitraController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\SubscriptionPackageController;
use App\Http\Controllers\Mitra\DashboardMitraController;
use App\Http\Controllers\Customer\CustomerOrderController;
use App\Http\Controllers\Customer\CustomerPointController;
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

//== RUTE PUBLIK & AUTENTIKASI ==//
Route::get('/', [LandingPageController::class, 'home'])->name('landing');
Route::post('/testimonials', [TestimoniController::class, 'submitFromLandingPage'])->name('testimonials.store');

// Auth (Proses Registrasi dan Login)
// Rute untuk menampilkan form login & register admin dan mitra
Route::get('/login', fn() => view('landing-page.auth.login'))->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute untuk menampilkan form login & register customer
Route::prefix('customer')->name('customer.')->group(function () {
    // Rute untuk menampilkan form login & register customer
    Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('login.form');
    Route::get('/register', [CustomerAuthController::class, 'showRegisterForm'])->name('register.form');

    // Rute untuk memproses data dari form
    Route::post('/login', [CustomerAuthController::class, 'login'])->name('login.submit');
    Route::post('/register', [CustomerAuthController::class, 'register'])->name('register.submit');

    // Rute untuk logout customer
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');
});

// Proses Registrasi Multi-Langkah (Publik)
Route::prefix('register')->name('register.')->group(function () {
    Route::get('/', [AuthController::class, 'showRegisterForm'])->name('form');
    Route::post('/step0', [AuthController::class, 'registerStep0'])->name('step0');
    Route::post('/step1', [AuthController::class, 'registerStep1'])->name('step1');
    Route::post('/step1a', [TenantController::class, 'store'])->name('step1a');
    Route::post('/step2', [AuthController::class, 'registerStep2'])->name('step2');
    Route::post('/step3', [AuthController::class, 'registerStep3'])->name('step3');
});

Route::get('/tenant/create', [TenantController::class, 'create'])->name('tenant.create');



//Keranjang Belanja
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index')->middleware('auth');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::patch('/update/{productCartId}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{productCartId}', [CartController::class, 'remove'])->name('remove');
});

//Mitra Sementara
// Route::prefix('dashboard-mitra')->name('mitra.')->group(function () {
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
Route::get('/preview/{slug}', function ($slug) {
    $template = \App\Models\Template::where('slug', $slug)->firstOrFail();

    // Render tampilan berdasarkan folder template (contoh: resources/views/template1/index.blade.php)
    return view($template->path . '.beranda');
});
// routes/web.php
Route::get('/beranda', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::get('/shop/{product:slug}', [ShopController::class, 'show'])->name('shop.details');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index'); // Melihat isi keranjang
Route::put('/cart/update/{product_id}', [CartController::class, 'update'])->name('cart.update'); // Memperbarui kuantitas
Route::delete('/cart/remove/{product_id}', [CartController::class, 'remove'])->name('cart.remove'); // Menghapus item
Route::get('/contact', [ContactController::class, 'showPublic'])->name('contact');
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');

// Route untuk menambah/menghapus item dari wishlist (untuk AJAX)
Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
// Route::get('/{slug}', [HomeController::class, 'showPage'])->name('page.show');

//== MIDTRANS WEBHOOK (TIDAK MEMERLUKAN AUTH/CSRF) ==//
//Route::post('/midtrans/webhook', [PaymentController::class, 'handleWebhook'])->name('midtrans.webhook');

// == GRUP RUTE UNTUK PENGGUNA YANG SUDAH LOGIN ==
Route::middleware(['auth'])->group(function () {
    /// Rute yang bisa diakses oleh semua user yang login
    Route::post('/voucher/apply', [VoucherController::class, 'apply'])->name('voucher.apply');
    Route::get('/payment/token', [PaymentController::class, 'generateSnapToken'])->name('payment.token');
    //Checkout Customer
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/', [CheckoutController::class, 'process'])->name('process');
        Route::post('/shipping-cost', [CheckoutController::class, 'getShippingCost'])->name('shipping');
    });
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
        Route::patch('kelola-mitra/{user}/status', [KelolaMitraController::class, 'updateStatus'])->name('mitra.updateStatus');

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

        Route::get('/produk', [ProductController::class, 'index'])->name('produk');
        Route::get('/hero', [HeroController::class, 'index'])->name('hero');
        Route::get('/banner', [BannerController::class, 'index'])->name('banner');

        Route::resource('heroes', HeroController::class);
        Route::resource('banners', BannerController::class);

        Route::get('/contacts', [ContactController::class, 'edit'])->name('contacts');
        Route::put('/contacts', [ContactController::class, 'update'])->name('contacts.update');

        Route::get('/tema', [TemaController::class, 'create'])->name('tema');
        Route::post('/tema', [TemaController::class, 'store'])->name('tema.store');

        // Route::resource('pages', PageController::class); // Ini akan membuat mitra.pages.* routes

        // Manajemen Seksi Halaman (PageSectionController) - Nested Resource
        // Route::resource('pages.sections', PageSectionController::class)->except(['show']); // Tidak butuh show untuk seksi

        // Rute untuk mendapatkan partial form dinamis via AJAX
        // Route::get('get-section-form-partial/{sectionType}', function ($sectionType) {
        //     $content = request('content', []); // Untuk edit, menerima konten yang ada
        //     if (view()->exists('dashboard-mitra.page_sections.partials.' . $sectionType . '_form')) {
        //         return view('dashboard-mitra.page_sections.partials.' . $sectionType . '_form', compact('content'));
        //     }
        //     return response('', 404);
        // })->name('get-section-form-partial');

        // CRUD route untuk produk menggunakan ProductController
        // Penting: URI 'products' saja karena sudah ada prefix 'dashboard-mitra'
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

        // untuk menampilkan dan mengupdate profil
        Route::get('/profile', [CustomerProfileController::class, 'show'])->name('profile');
        Route::post('/profile', [CustomerProfileController::class, 'update'])->name('profile.update');

        //  Pesanan Saya
        Route::get('/orders', [CustomerOrderController::class, 'index'])->name('orders');

        // Notifikasi Saya
        Route::get('/notifications', [CustomerNotificationController::class, 'index'])->name('notifications');

        // Voucher Saya
        Route::get('/vouchers', [CustomerVoucherController::class, 'index'])->name('vouchers');
        Route::post('/vouchers-claim', [CustomerVoucherController::class, 'claimVoucher'])->name('vouchers.claim');

        // Poin Saya
        Route::get('/points', [CustomerPointController::class, 'index'])->name('points');
        Route::post('/points-redeem', [CustomerPointController::class, 'redeem'])->name('points.redeem');
    });

    // Route::get('/home', [HomeController::class, 'index']); // Mengarahkan ke metode index di HomeController
    // Route::get('/{slug}', [HomeController::class, 'showPage'])->name('page.show');

});
