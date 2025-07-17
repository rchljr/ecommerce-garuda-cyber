@php
    // Cek apakah ini mode preview.
    $isPreview = $isPreview ?? false;

    // Ambil subdomain saat ini jika bukan mode preview.
    $currentSubdomain = !$isPreview ? request()->route('subdomain') : null;

    // Ambil data tenant yang sedang aktif dari request (disediakan oleh middleware).
    $tenant = $tenant ?? (!$isPreview ? request()->get('tenant') : null);

    // Inisialisasi variabel
    $cartCount = 0;
    $cartTotal = 0;

    $cartCount = 0;
    if (Auth::guard('customers')->check()) {
        $cart = Auth::guard('customers')->user()->cart;
        if ($cart) {
            $cartCount = $cart->items()->sum('quantity');
        }
    } elseif (session()->has('cart')) {
        $cartCount = collect(session('cart'))->sum('quantity');
    }
@endphp

<div class="offcanvas-menu-overlay"></div>
<div class="offcanvas-menu-wrapper">
    <div class="offcanvas__option">
        <div class="offcanvas__links">
            @guest('customers')
                @if(!$isPreview)
                    <a href="{{ route('tenant.customer.login.form', ['subdomain' => $currentSubdomain]) }}">Login</a>
                    <a href="{{ route('tenant.customer.register.form', ['subdomain' => $currentSubdomain]) }}">Daftar</a>
                @else
                    <a href="#" class="disabled-link">Login</a>
                    <a href="#" class="disabled-link">Daftar</a>
                @endif
            @endguest

            @auth('customers')
                <div class="header__top__dropdown">
                    <a href="#"><i class="fa fa-user"></i> Hi, {{ strtok(Auth::guard('customers')->user()->name, ' ') }}</a>
                    <span class="arrow_carrot-down"></span>
                    <ul>
                        @if(!$isPreview)
                            <li><a href="{{ route('tenant.account.profile', ['subdomain' => $currentSubdomain]) }}">Akun
                                    Saya</a></li>
                            <li><a href="{{ route('tenant.account.orders', ['subdomain' => $currentSubdomain]) }}">Pesanan
                                    Saya</a></li>
                            <li>
                                <a href="{{ route('tenant.customer.logout', ['subdomain' => $currentSubdomain]) }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form-offcanvas').submit();">
                                    Log Out
                                </a>
                                <form id="logout-form-offcanvas"
                                    action="{{ route('tenant.customer.logout', ['subdomain' => $currentSubdomain]) }}"
                                    method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </li>
                        @endif
                    </ul>
                </div>
            @endauth
            {{-- <a href="#">FAQs</a> --}}
        </div>
    </div>
    <div class="offcanvas__nav__option">
        <a href="#" class="search-switch"><img src="{{ asset('template1/img/icon/search.png') }}" alt=""></a>
        <a href="{{ !$isPreview ? route('tenant.wishlist', ['subdomain' => $currentSubdomain]) : '#' }}"><img
                src="{{ asset('template1/img/icon/heart.png') }}" alt=""></a>
        <a href="{{ !$isPreview ? route('tenant.cart.index', ['subdomain' => $currentSubdomain]) : '#' }}"><img
                src="{{ asset('template1/img/icon/cart.png') }}" alt="">
            {{-- PERBAIKAN: Gunakan variabel cartCount --}}
            <span>{{ $cartCount }}</span>
        </a>
        {{-- PERBAIKAN: Gunakan variabel cartTotal --}}
        {{-- <div class="price">{{ format_rupiah($cartTotal) }}</div> --}}
    </div>
    <div id="mobile-menu-wrap"></div>
</div>