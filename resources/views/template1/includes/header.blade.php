@php
    // --- BLOK LOGIKA BARU ---
    // Variabel $customTema sekarang tersedia berkat View Composer.

    // Cek mode preview
    $isPreview = $isPreview ?? false;

    // Ambil subdomain jika bukan mode preview
    $currentSubdomain = !$isPreview ? request()->route('subdomain') : null;

    // Ambil path logo dari data tema, gunakan default jika tidak ada
    $logoPath = optional($customTema)->shop_logo;
    $logoUrl = $logoPath ? asset('storage/' . $logoPath) : asset('template1/img/logo.png');

    // Ambil nama toko dari data tema, gunakan fallback ke data shop, lalu default
    $shopName = optional($customTema)->shop_name ?? optional($shop)->shop_name ?? 'Nama Toko';

    // Ambil warna dari data tema, gunakan default jika tidak ada
    $primaryColor = optional($customTema)->primary_color ?? '#111111'; // Warna default template1
    $secondaryColor = optional($customTema)->secondary_color ?? '#e53637'; // Warna default template1

    // Logika hitungan keranjang (tetap sama)
    $cartCount = 0;
    if (!$isPreview && Auth::guard('customers')->check()) {
        $cart = Auth::guard('customers')->user()->cart;
        $cartCount = $cart ? $cart->items->sum('quantity') : 0;
    } elseif (!$isPreview && session()->has('cart')) {
        $cartCount = array_sum(array_column(session('cart'), 'quantity'));
    }
@endphp

{{-- MENAMBAHKAN STYLE DINAMIS DI HEAD --}}
<style>
    /* Menggunakan CSS Variables untuk kemudahan */
    :root {
        --primary-color: {{ $primaryColor }};
        --secondary-color: {{ $secondaryColor }};
    }

    /* Mengganti warna elemen-elemen kunci */
    .header__top {
        background: var(--primary-color);
    }
    .header__menu ul li.active>a,
    .header__menu ul li:hover>a {
        color: var(--secondary-color);
    }
    .header__nav__option a span {
        background: var(--secondary-color);
    }
    .site-btn, .primary-btn { /* Asumsi nama class untuk tombol utama */
        background-color: var(--primary-color);
    }
    .product__item__text .add-cart {
        border-color: var(--primary-color);
        color: var(--primary-color);
    }
    .product__item__text .add-cart:hover {
        background: var(--primary-color);
        color: #ffffff;
    }
</style>

<header class="header">
    <div class="header__top">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-7">
                    <div class="header__top__left">
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
                                        href="{{ route('tenant.customer.login.form', ['subdomain' => $currentSubdomain]) }}">Login</a>
                                    <a
                                        href="{{ route('tenant.customer.register.form', ['subdomain' => $currentSubdomain]) }}">Daftar</a>
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
                                                    href="{{ route('tenant.account.profile', ['subdomain' => $currentSubdomain]) }}">Akun
                                                    Saya</a></li>
                                            <li><a
                                                    href="{{ route('tenant.account.orders', ['subdomain' => $currentSubdomain]) }}">Pesanan
                                                    Saya</a></li>
                                            <li>
                                                <a href="{{ route('tenant.customer.logout', ['subdomain' => $currentSubdomain]) }}"
                                                    onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();">
                                                    Log Out
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
                    {{-- PERBAIKAN: Logo dan Nama Toko Dinamis --}}
                    <a href="{{ !$isPreview ? route('tenant.home', ['subdomain' => $currentSubdomain]) : '#' }}">
                        <img src="{{ $logoUrl }}" alt="{{ $shopName }}" style="max-height: 40px;">
                        {{-- Opsional: tampilkan nama toko di sebelah logo --}}
                        <span style="font-size: 20px; font-weight: 700; color: #111; vertical-align: middle; margin-left: 10px;">{{ $shopName }}</span>
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
                            <a href="#">Pages</a>
                            <ul class="dropdown">
                                <li><a href="contact"
                                        class="{{ !$isPreview ? route('tenant.contact', ['subdomain' => $currentSubdomain]) : '#' }}">Tentang
                                        Kami</a>
                                </li>
                                <li><a href="{{ !$isPreview ? route('tenant.cart.index', ['subdomain' => $currentSubdomain]) : '#' }}"
                                        class="{{ request()->routeIs('cart.index') ? 'active' : '' }}">Keranjang
                                        Belanja</a></li>

                                {{-- <li><a href="./checkout.html"
                                        class="{{ request()->routeIs('checkout.*') ? 'active' : '' }}">Check Out</a>
                                </li>
                                <li><a href="./blog-details.html"
                                        class="{{ request()->routeIs('blog.details.*') ? 'active' : '' }}">Blog
                                        Details</a></li> --}}
                            </ul>
                        </li>

                        {{-- Blog --}}
                        {{-- <li><a href="./blog.html">Blog</a></li> --}}

                        {{-- Contacts --}}
                        <li class="{{ request()->routeIs('tenant.contact') ? 'active' : '' }}"><a
                                href="{{ !$isPreview ? route('tenant.contact', ['subdomain' => $currentSubdomain]) : '#' }}">Tentang
                                Kami</a></li>
                    </ul>
                </nav>
            </div>
            <div class="col-lg-3 col-md-3">
                <div class="header__nav__option">
                    <a href="#" class="search-switch"><img src="{{ asset('template1/img/icon/search.png') }}"
                            alt=""></a>
                    <a href="{{ !$isPreview ? route('tenant.wishlist', ['subdomain' => $currentSubdomain]) : '#' }}"><img
                            src="{{ asset('template1/img/icon/heart.png') }}" alt=""></a>
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
    .disabled-link { color: #b2b2b2 !important; cursor: not-allowed; }
</style>