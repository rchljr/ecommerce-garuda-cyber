@php
    // --- Data untuk Mode Preview dan Subdomain ---
    // $isPreview: Diterima dari controller TemaController (saat di mode editor)
    // Jika tidak di-pass, asumsikan bukan mode preview.
    $isPreview = $isPreview ?? false;

    // $currentSubdomain: Ambil dari route parameter.
    $currentSubdomain = request()->route('subdomain');

    // --- Ambil Data Toko (Shop) dan Tema Kustom (CustomTema) ---
    // Ini adalah bagian paling KRUSIAL.
    // Di lingkungan multi-tenant, $currentShop biasanya disediakan oleh middleware
    // atau di-resolve dari subdomain. Kita akan mencoba mengambilnya di sini.

    $currentShop = $currentShop ?? null; // Inisialisasi untuk jaga-jaga

    // Jika $currentShop belum tersedia (misal: ini adalah partial/component yang berdiri sendiri),
    // coba ambil dari subdomain yang aktif.
    if (!$currentShop && $currentSubdomain) {
        // Asumsi model Shop memiliki kolom 'subdomain'
        // dan relasi 'user' yang kemudian memiliki relasi 'customTema'
        $currentShop = \App\Models\Shop::where('subdomain', $currentSubdomain)
                                        ->with(['user.customTema']) // Eager load relasi yang dibutuhkan
                                        ->first();
    }

    // Ambil data tenant yang sedang aktif dari shop (jika ada)
    $tenant = $tenant ?? (isset($currentShop) ? optional($currentShop->user)->tenant : null);

    // Ambil data customTema dari user pemilik toko
    $customTema = isset($currentShop) ? optional($currentShop->user)->customTema : null;
   
    // --- Definisi Variabel Tema Kustom ---
    // Gunakan nilai dari $customTema jika ada, jika tidak, gunakan default.
    $logoPath = optional($customTema)->shop_logo;
    // Jika logo kustom ada, gunakan path storage. Jika tidak, gunakan logo default template.
    $logoUrl = $logoPath ? asset('storage/' . $logoPath) : asset('template1/img/logo.png');

    $shopName = optional($customTema)->shop_name ?? optional($currentShop)->shop_name ?? 'Nama Toko Anda';
    $shopDescription = optional($customTema)->shop_description ?? 'Selamat datang di toko kami!';

    $primaryColor = optional($customTema)->primary_color ?? '#007bff'; // Default Bootstrap blue
    $secondaryColor = optional($customTema)->secondary_color ?? '#6c757d'; // Default Bootstrap gray
    // dd($currentShop, $customTema, $logoUrl, $shopName, $primaryColor);
     
    // --- Logika Keranjang Belanja ---
    // (Kode ini sudah cukup baik, tidak ada perubahan signifikan di sini)
    $cartCount = 0;
    $notificationCount = 0;

        if (Auth::guard('customers')->check()) {
            $cart = Auth::guard('customers')->user()->cart;
            if ($cart) {
                $cartCount = $cart->items()
                    ->whereHas('product', function ($query) use ($shopOwnerId) {
                        $query->where('user_id', $shopOwnerId);
                    })
                    ->sum('quantity');
            }
        } elseif (session()->has('cart')) {
            $sessionCart = session('cart');
            $productIdsInCart = array_column($sessionCart, 'product_id');

            if (!empty($productIdsInCart)) {
                $tenantProductIds = \App\Models\Product::whereIn('id', $productIdsInCart)
                    ->where('user_id', $shopOwnerId)
                    ->pluck('id')->all();

                $cartCount = collect($sessionCart)->filter(function ($item) use ($tenantProductIds) {
                    return isset($item['product_id']) && in_array($item['product_id'], $tenantProductIds);
                })->sum('quantity');
            }
        }

        // Hitung Notifikasi
        // Notifikasi dari pembayaran yang berhasil
        $successfulPaymentsCount = \App\Models\Payment::where('user_id', $user->id)
            ->whereIn('midtrans_transaction_status', ['settlement', 'capture'])
            ->count();

        // Notifikasi dari pesanan dengan status tertentu
        $ordersCount = \App\Models\Order::where('user_id', $user->id)
            ->whereIn('status', ['failed', 'cancelled', 'expired', 'pending'])
            ->count();

        $notificationCount = $successfulPaymentsCount + $ordersCount;

    } elseif (session()->has('cart')) {
        $cartCount = collect(session('cart'))->sum('quantity');
    }
@endphp

{{-- --- Inject Custom Colors into CSS Variables --- --}}
{{-- PENTING: Pastikan CSS di template Anda menggunakan variabel ini --}}
<style>
    :root {
        --primary-color: {{ $primaryColor }};
        --secondary-color: {{ $secondaryColor }};
        /* Contoh lain: */
        /* --header-bg-color: var(--primary-color); */
        /* --button-text-color: var(--secondary-color); */
    }

    /* CONTOH PENERAPAN VAR CSS KE ELEMEN TEMPLATE */
    /* Anda perlu menyesuaikan ini dengan kelas CSS template Anda */
    .header__top__links a {
        /* color: var(--secondary-color); */
    }
    .header__top__links a:hover {
        color: var(--primary-color) !important;
    }
    .header__menu ul li.active > a,
    .header__menu ul li:hover > a {
        color: var(--primary-color);
    }
    .header__nav__option a span { /* Untuk badge keranjang */
        background: var(--primary-color);
    }
    .header__nav__option a:hover {
        color: var(--primary-color);
    }
    /* Tambahkan lebih banyak aturan CSS di sini sesuai kebutuhan template Anda */

    .disabled-link {
        color: #b2b2b2 !important;
        cursor: not-allowed;
    }
</style>

<header class="header">
    <div class="header__top">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-7">
                    <div class="header__top__left">
                        {{-- Contoh: Tampilkan deskripsi toko di sini jika diinginkan --}}
                        {{-- <p>{{ $shopDescription }}</p> --}}
                    </div>
                </div>
                <div class="col-lg-6 col-md-5">
                    <div class="header__top__right">
                        <div class="header__top__links">
                            @guest('customers')
                                <a href="#">FAQs</a>
                                @if(!$isPreview)
                                    <a href="{{ route('tenant.customer.login.form', ['subdomain' => $currentSubdomain]) }}">Login</a>
                                    <a href="{{ route('tenant.customer.register.form', ['subdomain' => $currentSubdomain]) }}">Daftar</a>
                                @else
                                    <a href="#" class="disabled-link">Login</a>
                                    <a href="#" class="disabled-link">Daftar</a>
                                @endif
                            @endguest

                            @auth('customers')
                                <a href="#">FAQs</a>
                                <div class="header__top__dropdown">
                                    <a href="#"><i class="fa fa-user"></i> Hi,
                                        {{ strtok(Auth::guard('customers')->user()->name, ' ') }}</a>
                                    <span class="arrow_carrot-down"></span>
                                    <ul>
                                        @if(!$isPreview)
                                            <li><a href="{{ route('tenant.account.profile', ['subdomain' => $currentSubdomain]) }}">Akun Saya</a></li>
                                            <li><a href="{{ route('tenant.account.orders', ['subdomain' => $currentSubdomain]) }}">Pesanan Saya</a></li>
                                            <li>
                                                <a href="{{ !$isPreview ? route('tenant.customer.logout', ['subdomain' => $currentSubdomain]) : '#' }}"
                                                    onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();">
                                                    Log Out
                                                </a>
                                                <form id="logout-form-header"
                                                    action="{{ !$isPreview ? route('tenant.customer.logout', ['subdomain' => $currentSubdomain]) : '#' }}"
                                                    method="POST" style="display: none;">
                                                    @csrf
                                                </form>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-3">
                <div class="header__logo">
                    {{-- LOGO TOKO DISESUAIKAN DI SINI --}}
                    <a href="{{ !$isPreview ? route('tenant.home', ['subdomain' => $currentSubdomain]) : '#' }}">
                        <img src="{{ $logoUrl }}" alt="{{ $shopName }}">
                    </a>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <nav class="header__menu mobile-menu">
                    <ul>
                        <li class="{{ request()->routeIs('tenant.home') ? 'active' : '' }}"><a
                                href="{{ !$isPreview ? route('tenant.home', ['subdomain' => $currentSubdomain]) : '#' }}">Beranda</a>
                        </li>
                        <li class="{{ request()->routeIs('tenant.shop') ? 'active' : '' }}">
                            <a href="{{ !$isPreview ? route('tenant.shop', ['subdomain' => $currentSubdomain]) : '#' }}">Produk</a>
                        </li>
                        <li class="{{ request()->routeIs('tenant.contact', 'tenant.product.details', 'tenant.cart.index') ? 'active' : '' }}">
                            <a href="#">Pages</a>
                            <ul class="dropdown">
                                <li><a href="{{ !$isPreview ? route('tenant.contact', ['subdomain' => $currentSubdomain]) : '#' }}">Tentang Kami</a></li>
                                <li><a href="{{ !$isPreview ? route('tenant.cart.index', ['subdomain' => $currentSubdomain]) : '#' }}"
                                        class="{{ request()->routeIs('cart.index') ? 'active' : '' }}">Keranjang Belanja</a></li>
                            </ul>
                        </li>
                        <li class="{{ request()->routeIs('tenant.contact') ? 'active' : '' }}"><a
                                href="{{ !$isPreview ? route('tenant.contact', ['subdomain' => $currentSubdomain]) : '#' }}">Kontak</a></li>
                    </ul>
                </nav>
            </div>
            <div class="col-lg-3 col-md-3">
                <div class="header__nav__option">
                    <a href="#" class="search-switch"><img src="{{ asset('template1/img/icon/search.png') }}"
                            alt=""></a>
                    <a href="{{ !$isPreview ? route('tenant.wishlist', ['subdomain' => $currentSubdomain]) : '#' }}"><img
                            src="{{ asset('template1/img/icon/heart.png') }}" alt=""></a>
                    <a class="notification-icon"
                        href="{{ !$isPreview ? route('tenant.account.notifications', ['subdomain' => $currentSubdomain]) : '#' }}">
                        <i class="fa fa-bell"></i>
                        @if ($notificationCount > 0)
                            <span id="notification-count">{{ $notificationCount }}</span>
                        @endif
                    </a>
                    <a href="{{ !$isPreview ? route('tenant.cart.index', ['subdomain' => $currentSubdomain]) : '#' }}"><img
                            src="{{ asset('template1/img/icon/cart.png') }}" alt="">
                        <span id="cart-count">{{ $cartCount }}</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="canvas__open"><i class="fa fa-bars"></i></div>
    </div>
</header>
