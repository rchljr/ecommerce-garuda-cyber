@php
    // Logika ini disamakan dengan header utama untuk konsistensi data
    $isPreview = $isPreview ?? false;
    $currentSubdomain = request()->route('subdomain');
    $cartCount = 0;
    $notificationCount = 0;

    if (Auth::guard('customers')->check()) {
        $user = Auth::guard('customers')->user();
        $cartService = app(\App\Services\CartService::class);
        $cartCount = $cartService->getCartCount();

        // Logika Hitung Notifikasi dari controller notifikasi Anda
        $successfulPaymentsCount = \App\Models\Payment::where('user_id', $user->id)
            ->whereIn('midtrans_transaction_status', ['settlement', 'capture'])
            ->count();
        $ordersCount = \App\Models\Order::where('user_id', $user->id)
            ->whereIn('status', ['failed', 'cancelled', 'expired', 'pending'])
            ->count();
        $notificationCount = $successfulPaymentsCount + $ordersCount;

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
                                    Logout
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
        </div>
    </div>
    <div class="offcanvas__nav__option">
        <a href="{{ !$isPreview ? route('tenant.wishlist', ['subdomain' => $currentSubdomain]) : '#' }}"><i
                class="fa fa-heart-o"></i></a>
        <a class="notification-icon"
            href="{{ !$isPreview ? route('tenant.account.notifications', ['subdomain' => $currentSubdomain]) : '#' }}">
            <i class="fa fa-bell-o"></i>
            @if ($notificationCount > 0)
                <span id="notification-count-mobile">{{ $notificationCount }}</span>
            @endif
        </a>
        <a href="{{ !$isPreview ? route('tenant.cart.index', ['subdomain' => $currentSubdomain]) : '#' }}">
            <i class="fa fa-shopping-basket"></i>
            @if ($cartCount > 0)
                <span id="cart-count-mobile">{{ $cartCount }}</span>
            @endif
        </a>
    </div>
    <div id="mobile-menu-wrap"></div>
    <div class="offcanvas__text">
        <p>Platform E-commerce Terbaik untuk Bisnis Anda.</p>
    </div>
</div>

{{-- Menambahkan style untuk badge dan perbaikan warna ikon di menu mobile --}}
<style>
    .offcanvas__nav__option a {
        position: relative;
    }

    .offcanvas__nav__option a i {
        color: #111111;
    }

    #notification-count-mobile,
    #cart-count-mobile {
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