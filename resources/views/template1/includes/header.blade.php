@php
    // Ambil subdomain saat ini sekali saja dari parameter rute agar lebih efisien.
    // Ini tersedia karena middleware 'tenant.exists' sudah memprosesnya.
    $currentSubdomain = request()->route('subdomain');
@endphp

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
                            @guest
                                <a href="#">FAQs</a>
                                <a href="{{ route('tenant.customer.login.form', ['subdomain' => $currentSubdomain]) }}">Login</a>
                                <a href="{{ route('tenant.customer.register.form', ['subdomain' => $currentSubdomain]) }}">Daftar</a>
                            @endguest

                            {{-- Tampilkan menu ini jika pengguna sudah login --}}
                            @auth
                                <a href="#">FAQs</a>
                                <div class="header__top__dropdown">
                                    <a href="#"><i class="fa fa-user"></i> Hi, {{ strtok(Auth::user()->name, ' ') }}</a>
                                    <span class="arrow_carrot-down"></span>
                                    <ul>
                                        <li><a href="{{ route('tenant.customer.profile', ['subdomain' => $currentSubdomain]) }}">Akun Saya</a></li>
                                        <li><a href="{{ route('tenant.customer.orders', ['subdomain' => $currentSubdomain]) }}">Pesanan Saya</a></li>
                                        <li>
                                            <a href="{{ route('tenant.customer.logout', ['subdomain' => $currentSubdomain]) }}"
                                                onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();">
                                                Log Out
                                            </a>
                                            <form id="logout-form-header" action="{{ route('tenant.customer.logout', ['subdomain' => $currentSubdomain]) }}"
                                                method="POST" style="display: none;">
                                                @csrf
                                            </form>
                                        </li>
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
                    <a href="{{ route('tenant.home', ['subdomain' => $currentSubdomain]) }}"><img
                            src="{{ asset('template1/img/logo.png') }}" alt=""></a>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <nav class="header__menu mobile-menu">
                    <ul>
                        {{-- Home --}}
                        <li class="{{ request()->routeIs('tenant.home') ? 'active' : '' }}"><a
                                href="{{ route('tenant.home', ['subdomain' => $currentSubdomain]) }}">Beranda</a></li>

                        {{-- Shop --}}
                        <li class="{{ request()->routeIs('tenant.shop') ? 'active' : '' }}">
                            <a href="{{ route('tenant.shop', ['subdomain' => $currentSubdomain]) }}">Produk</a>
                        </li>

                        {{-- Pages Dropdown --}}
                        <li
                            class="{{-- {{ request()->routeIs('cart.*') || request()->routeIs('checkout.*') || request()->routeIs('about.*') || request()->routeIs('blog.details.*') || request()->routeIs('shop.details') ? 'active' : '' }} --}}">
                            {{-- Pastikan 'shop.details' juga di sini --}}
                            <a href="#">Pages</a>
                            <ul class="dropdown">
                                <li><a href="contact"
                                        class="{{ route('tenant.contact', ['subdomain' => $currentSubdomain]) }}">Tentang
                                        Kami</a>
                                </li>
                                {{-- <li><a href="{{ route('shop.details', ['product' => 'some-product-slug']) }}"
                                        class="{{ request()->routeIs('shop.details') ? 'active' : '' }}">Detail
                                        Produk</a></li> --}}
                                {{-- <li><a href="{{ route('cart.index') }}"
                                        class="{{ request()->routeIs('cart.index') ? 'active' : '' }}">Keranjang
                                        Belanja</a></li> --}}
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
                                href="{{ route('tenant.contact', ['subdomain' => $currentSubdomain]) }}">Tentang
                                Kami</a></li>
                    </ul>
                </nav>
            </div>
            <div class="col-lg-3 col-md-3">
                <div class="header__nav__option">
                    <a href="#" class="search-switch"><img src="{{ asset('template1/img/icon/search.png') }}"
                            alt=""></a>
                    <a href="{{ route('tenant.wishlist', ['subdomain' => $currentSubdomain]) }}"><img
                            src="{{ asset('template1/img/icon/heart.png') }}" alt=""></a>
                    <a href="{{ route('tenant.cart.index', ['subdomain' => $currentSubdomain]) }}"><img src="{{ asset('template1/img/icon/cart.png') }}" alt="">
                        <span id="cart-count">{{-- ... --}}</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="canvas__open"><i class="fa fa-bars"></i></div>
    </div>
</header>