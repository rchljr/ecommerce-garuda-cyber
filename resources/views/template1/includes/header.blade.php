@php
    $isPreview = $isPreview ?? false;
    $currentSubdomain = request()->route('subdomain');

    // Ambil user yang sedang login
    $loggedInUser = Auth::user();

    // Inisialisasi currentShop dan customTema
    $currentShop = null;
    $customTema = null;

    // Jika user login (mitra), coba ambil shop dan customTema
    if ($loggedInUser) {
        // Ambil objek shop dari user (tanpa eager loading customTema di sini)
        $currentShop = $loggedInUser->shop;

        // Ambil customTema langsung dari user
        // Pastikan relasi 'customTema' ada di model App\Models\User
        $customTema = $loggedInUser->customTema;
    }

    // Ambil logo dan warna dari customTema, dengan fallback ke default
    $logoPath = optional($customTema)->shop_logo;
    $logoUrl = $logoPath ? asset('storage/' . $logoPath) : asset('template1/img/logo.png');

    $primaryColor = optional($customTema)->primary_color ?? '#4F46E5'; // Default dari form Anda
    $secondaryColor = optional($customTema)->secondary_color ?? '#D946EF'; // Default dari form Anda

    // Logika Keranjang Belanja dan Notifikasi
    $cartCount = 0;
    $notificationCount = 0;

    // Logika ini untuk customer, bukan mitra
    if (Auth::guard('customers')->check()) {
        $customerUser = Auth::guard('customers')->user();

        // 1. Logika Hitung Keranjang (menggunakan service yang sudah ada)
        $cartService = app(\App\Services\CartService::class);
        $cartCount = $cartService->getCartCount();

        // 2. Logika Hitung Notifikasi (disesuaikan dengan controller Anda)
        $successfulPaymentsCount = \App\Models\Payment::where('user_id', $customerUser->id)
            ->whereIn('midtrans_transaction_status', ['settlement', 'capture'])
            ->count();
        $ordersCount = \App\Models\Order::where('user_id', $customerUser->id)
            ->whereIn('status', ['failed', 'cancelled', 'expired', 'pending'])
            ->count();
        $notificationCount = $successfulPaymentsCount + $ordersCount;

    } elseif (session()->has('cart')) {
        $cartCount = collect(session('cart'))->sum('quantity');
    }
@endphp

{{-- --- Inject Custom Colors into CSS Variables --- --}}
<style>
    :root {
        --primary-color: {{ $primaryColor }};
        --secondary-color: {{ $secondaryColor }};
    }

    .header__top__links a:hover {
        color: var(--primary-color) !important;
    }
    .header__menu ul li.active > a,
    .header__menu ul li:hover > a {
        color: var(--primary-color);
    }
    .header__nav__option a span {
        background: var(--primary-color);
    }
    .header__nav__option a:hover {
        color: var(--primary-color);
    }

    .disabled-link {
        color: #b2b2b2 !important;
        cursor: not-allowed;
    }

    .header__nav__option a i {
        font-size: 20px;
        color: #111111;
    }

    .header__nav__option a {
        position: relative;
    }

    .notification-icon {
        position: relative;
        display: inline-block;
    }

    #notification-count,
    #cart-count {
        position: absolute;
        top: -6px;
        right: -9px;
        height: 18px;
        width: 18px;
        background: #ca1515;
        color: #ffffff;
        border-radius: 50%;
        font-size: 11px;
        font-weight: 700;
        line-height: 18px;
        text-align: center;
    }

    /* --- CSS BARU UNTUK LOGO --- */
    .header__logo img {
        max-height: 40px; /* Atur tinggi maksimum logo */
        width: auto; /* Biarkan lebar menyesuaikan proporsi */
        object-fit: contain; /* Pastikan gambar tidak terpotong */
        display: block; /* Menghilangkan spasi ekstra di bawah gambar */
    }

    /* Sesuaikan jika navbar Anda memiliki tinggi tetap, misalnya */
    .header {
        /* min-height: 80px; */ /* Contoh: jika header memiliki tinggi minimum */
        display: flex;
        flex-direction: column; /* BARIS PENTING: Menata item secara vertikal */
        /* align-items: center; */ /* Hapus atau sesuaikan ini jika tidak diperlukan lagi */
    }

    .header__logo {
        display: flex;
        align-items: center; /* Pusatkan logo secara vertikal di dalam div-nya */
        height: 100%; /* Agar logo mengambil tinggi penuh container */
    }
    /* --- AKHIR CSS BARU --- */
</style>

<header class="header">
    <div class="header__top">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-7">
                    <div class="header__top__left">
                        <div class="header__top__links">
                            <a href="{{ !$isPreview ? route('tenants.index') : '#' }}"
                                class="font-semibold text-white bg-red-600 px-3 py-1 rounded-full text-xs hover:bg-red-700 transition-colors">
                                <i class="fa fa-store mr-1"></i> Jelajahi Toko Lain
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-5">
                    <div class="header__top__right">
                        <div class="header__top__links">
                            @guest('customers')
                                <a href="#">Bantuan</a>
                                @if(!$isPreview)
                                    <a
                                        href="{{ route('tenant.customer.login.form', ['subdomain' => $currentSubdomain]) }}">Login</a>
                                    <a
                                        href="{{ route('tenant.customer.register.form', ['subdomain' => $currentSubdomain]) }}">Daftar</a>
                                @else
                                    <a href="#" class="disabled-link">Login</a>
                                    <a href="#" class="disabled-link">Daftar</a>
                                @endif
                            @endguest

                            @auth('customers')
                                <a href="#">Bantuan</a>
                                <div class="header__top__dropdown">
                                    @php $customer = Auth::guard('customers')->user(); @endphp
                                    {{-- PERBAIKAN: Mengganti foto profil dengan ikon --}}
                                    <a href="#">
                                        <i class="fa fa-user"></i> Hi, {{ strtok($customer->name, ' ') }}
                                    </a>
                                    <span class="arrow_carrot-down"></span>
                                    <ul>
                                        @if(!$isPreview)
                                            <li><a
                                                    href="{{ route('tenant.account.profile', ['subdomain' => $currentSubdomain]) }}">Akun
                                                    Saya</a></li>
                                            <li><a
                                                    href="{{ route('tenant.account.orders', ['subdomain' => $currentSubdomain]) }}">Pesanan
                                                    Saya</a></li>
                                            <li>
                                                <a href="{{ route('tenant.customer.logout', ['subdomain' => $currentSubdomain]) }}"
                                                    onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();">
                                                    Keluar
                                                </a>
                                                <form id="logout-form-header"
                                                    action="{{ route('tenant.customer.logout', ['subdomain' => $currentSubdomain]) }}"
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
                    <a href="{{ !$isPreview ? route('tenant.home', ['subdomain' => $currentSubdomain]) : '#' }}">
                        <img src="{{ $logoUrl }}" alt="{{ optional($currentShop)->shop_name ?? 'Logo Toko' }}">
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
                            <a
                                href="{{ !$isPreview ? route('tenant.shop', ['subdomain' => $currentSubdomain]) : '#' }}">Toko</a>
                        </li>
                        <li class="{{ request()->routeIs('tenant.contact') ? 'active' : '' }}"><a
                                href="{{ !$isPreview ? route('tenant.contact', ['subdomain' => $currentSubdomain]) : '#' }}">Kontak Kami</a>
                        </li>
                    </ul>
                </nav>
            </div>
            <div class="col-lg-3 col-md-3">
                <div class="header__nav__option">
                    <a href="{{ !$isPreview ? route('tenant.wishlist', ['subdomain' => $currentSubdomain]) : '#' }}"><i
                            class="fa fa-heart-o"></i></a>
                    <a class="notification-icon"
                        href="{{ !$isPreview ? route('tenant.account.notifications', ['subdomain' => $currentSubdomain]) : '#' }}">
                        <i class="fa fa-bell-o"></i>
                        @if ($notificationCount > 0)
                            <span id="notification-count">{{ $notificationCount }}</span>
                        @endif
                    </a>
                    <a href="{{ !$isPreview ? route('tenant.cart.index', ['subdomain' => $currentSubdomain]) : '#' }}">
                        <i class="fa fa-shopping-basket"></i>
                        @if ($cartCount > 0)
                            <span id="cart-count">{{ $cartCount }}</span>
                        @endif
                    </a>
                </div>
            </div>
        </div>
        <div class="canvas__open"><i class="fa fa-bars"></i></div>
    </div>
</header>

<style>
    .disabled-link {
        color: #b2b2b2 !important;
        cursor: not-allowed;
    }

    .header__nav__option a i {
        font-size: 20px;
        color: #111111;
    }

    .header__nav__option a {
        position: relative;
    }

    .notification-icon {
        position: relative;
        display: inline-block;
    }

    #notification-count,
    #cart-count {
        position: absolute;
        top: -6px;
        right: -9px;
        height: 18px;
        width: 18px;
        background: #ca1515;
        color: #ffffff;
        border-radius: 50%;
        font-size: 11px;
        font-weight: 700;
        line-height: 18px;
        text-align: center;
    }
</style>
