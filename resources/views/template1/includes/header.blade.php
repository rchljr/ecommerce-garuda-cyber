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
                            <a href="login">Sign in</a>
                            <a href="#">FAQs</a>
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
                    <a href="{{ route('home') }}"><img src="{{ asset('template1/img/logo.png') }}" alt=""></a>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <nav class="header__menu mobile-menu">
                    <ul>
                        {{-- Home --}}
                        <li class="{{ request()->routeIs('home') ? 'active' : '' }}"><a href="{{ route('home') }}">Beranda</a></li>
                        
                        {{-- Shop --}}
                        <li class="{{ request()->routeIs('shop') ? 'active' : '' }}"><a href="{{ route('shop') }}">Produk</a></li>
                        
                        {{-- Pages Dropdown --}}
                        <li class="{{ request()->routeIs('cart.*') || request()->routeIs('checkout.*') || request()->routeIs('about.*') || request()->routeIs('blog.details.*') || request()->routeIs('shop.details') ? 'active' : '' }}"> {{-- Pastikan 'shop.details' juga di sini --}}
                            <a href="#">Pages</a>
                            <ul class="dropdown">
                                <li><a href="contact" class="{{ request()->routeIs('contact') ? 'active' : '' }}">Tentang Kami</a></li>
                                <li><a href="{{ route('shop.details', ['product' => 'some-product-slug']) }}" class="{{ request()->routeIs('shop.details') ? 'active' : '' }}">Detail Produk</a></li> {{-- Perhatikan placeholder slug --}}
                                <li><a href="{{ route('cart.index') }}" class="{{ request()->routeIs('cart.index') ? 'active' : '' }}">Keranjang Belanja</a></li>
                                {{-- <li><a href="./checkout.html" class="{{ request()->routeIs('checkout.*') ? 'active' : '' }}">Check Out</a></li>
                                <li><a href="./blog-details.html" class="{{ request()->routeIs('blog.details.*') ? 'active' : '' }}">Blog Details</a></li> --}}
                            </ul>
                        </li>
                        
                        {{-- Blog --}}
                        {{-- <li><a href="./blog.html">Blog</a></li> --}}
                        
                        {{-- Contacts --}}
                        <li class="{{ request()->routeIs('contact') ? 'active' : '' }}"><a href="{{ route('contact') }}">Tentang Kami</a></li>
                    </ul>
                </nav>
            </div>
            <div class="col-lg-3 col-md-3">
                <div class="header__nav__option">
                    <a href="#" class="search-switch"><img src="{{ asset('template1/img/icon/search.png') }}" alt=""></a>
                    <a href="{{ route('wishlist.index') }}"><img src="{{ asset('template1/img/icon/heart.png') }}" alt=""></a>
                    {{-- Diperbarui untuk menampilkan hitungan dari session --}}
                    <a href="{{ route('cart.index') }}"><img src="{{ asset('template1/img/icon/cart.png') }}" alt=""> <span id="cart-count">{{ Session::has('cart') ? array_sum(array_column(Session::get('cart'), 'quantity')) : 0 }}</span></a>
                </div>
            </div>
        </div>
        <div class="canvas__open"><i class="fa fa-bars"></i></div>
    </div>
</header>
