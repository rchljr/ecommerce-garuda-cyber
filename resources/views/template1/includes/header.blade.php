@php
    $isPreview = $isPreview ?? false;
    $currentSubdomain = !$isPreview ? request()->route('subdomain') : null;
    $logoPath = (isset($currentShop) && $currentShop) ? optional($currentShop->customTema)->shop_logo : null;
    $logoUrl = $logoPath ? asset('storage/' . $logoPath) : asset('template1/img/logo.png');
    $cartCount = 0;
    $notificationCount = 0;

    if (Auth::guard('customers')->check()) {
        $user = Auth::guard('customers')->user();

        // Hitung Keranjang Belanja
        $cart = $user->cart;
        if ($cart) {
            $cartCount = $cart->items()->sum('quantity');
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

<header class="header">
    <div class="header__top">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-7">
                    <div class="header__top__left">
                        <div class="header__top__links">
                            <a href="{{ !$isPreview ? route('tenants.index') : '#' }}">Lihat Toko Mitra Lainnya</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-5">
                    <div class="header__top__right">
                        <div class="header__top__links">
                            {{-- Tampilkan link ini jika pengguna adalah tamu (belum login) --}}
                            @guest('customers')
                                <a href="#">FAQs</a>
                                @if(!$isPreview)
                                    <a
                                        href="{{ !$isPreview ? route('tenant.customer.login.form', ['subdomain' => $currentSubdomain]) : '#'}}">Login</a>
                                    <a
                                        href="{{ !$isPreview ? route('tenant.customer.register.form', ['subdomain' => $currentSubdomain]) : '#'}}">Daftar</a>
                                @else
                                    <a href="#" class="disabled-link">Login</a>
                                    <a href="#" class="disabled-link">Daftar</a>
                                @endif
                            @endguest

                            {{-- Tampilkan menu ini jika pengguna sudah login --}}
                            @auth('customers')
                                <a href="#">FAQs</a>
                                <div class="header__top__dropdown">
                                    <a href="#"><i class="fa fa-user"></i> Hi,
                                        {{ strtok(Auth::guard('customers')->user()->name, ' ') }}</a>
                                    <span class="arrow_carrot-down"></span>
                                    <ul>
                                        @if(!$isPreview)
                                            <li><a
                                                    href="{{ !$isPreview ? route('tenant.account.profile', ['subdomain' => $currentSubdomain]) : '#' }}">Akun
                                                    Saya</a></li>
                                            <li><a
                                                    href="{{ !$isPreview ? route('tenant.account.orders', ['subdomain' => $currentSubdomain]) : '#' }}">Pesanan
                                                    Saya</a></li>
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
                    <a href="{{ !$isPreview ? route('tenant.home', ['subdomain' => $currentSubdomain]) : '#' }}">
                        <img src="{{ $logoUrl }}" alt="{{ optional($currentShop)->shop_name ?? 'Logo Toko' }}">
                    </a>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <nav class="header__menu mobile-menu">
                    <ul>
                        {{-- Home --}}
                        <li class="{{ request()->routeIs('tenant.home') ? 'active' : '' }}"><a
                                href="{{ !$isPreview ? route('tenant.home', ['subdomain' => $currentSubdomain]) : '#' }}">Beranda</a>
                        </li>

                        {{-- Shop --}}
                        <li class="{{ request()->routeIs('tenant.shop') ? 'active' : '' }}">
                            <a
                                href="{{ !$isPreview ? route('tenant.shop', ['subdomain' => $currentSubdomain]) : '#' }}">Produk</a>
                        </li>

                        {{-- Pages Dropdown --}}
                        <li
                            class="{{ request()->routeIs('tenant.contact', 'tenant.product.details', 'tenant.cart.index') ? 'active' : '' }}">
                            <a href="#">Halaman</a>
                            <ul class="dropdown">
                                <li><a
                                        href="{{ !$isPreview ? route('tenant.contact', ['subdomain' => $currentSubdomain]) : '#' }}">Tentang
                                        Kami</a>
                                </li>
                                <li><a href="{{ !$isPreview ? route('tenant.cart.index', ['subdomain' => $currentSubdomain]) : '#' }}"
                                        class="{{ request()->routeIs('cart.index') ? 'active' : '' }}">Keranjang
                                        Belanja</a></li>
                            </ul>
                        </li>

                        {{-- Contacts --}}
                        <li class="{{ request()->routeIs('tenant.contact') ? 'active' : '' }}"><a
                                href="{{ !$isPreview ? route('tenant.contact', ['subdomain' => $currentSubdomain]) : '#' }}">Kontak</a>
                        </li>
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

<style>
    .disabled-link {
        color: #b2b2b2 !important;
        cursor: not-allowed;
    }

    .header__nav__option .fa-bell {
        color: #111111;
        font-size: 20px;
    }

    /* Perbaikan untuk ikon notifikasi */
    .notification-icon {
        position: relative;
        display: inline-block;
        /* Memastikan posisi relatif bekerja */
    }

    #notification-count {
        position: absolute;
        top: -6px;
        /* Posisikan lencana di atas ikon */
        right: -9px;
        /* Posisikan lencana di kanan ikon */
        height: 18px;
        width: 18px;
        background: #ca1515;
        /* Warna latar merah untuk notifikasi */
        color: #ffffff;
        border-radius: 50%;
        /* Membuat lencana menjadi bulat */
        font-size: 11px;
        font-weight: 700;
        line-height: 18px;
        /* Menengahkan angka secara vertikal */
        text-align: center;
    }
</style>