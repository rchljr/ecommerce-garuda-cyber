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

    // Logika untuk menghitung item keranjang dan total harga berdasarkan tenant yang aktif.
    if (!$isPreview && $tenant) {
        $shopOwnerId = $tenant->user_id;

        if (Auth::guard('customers')->check()) {
            // Jika pelanggan login, hitung dari database dengan filter.
            $cart = Auth::guard('customers')->user()->cart;
            if ($cart) {
                $tenantItems = $cart->items()
                    ->whereHas('product', function ($query) use ($shopOwnerId) {
                        $query->where('user_id', $shopOwnerId);
                    })
                    ->with('product:id,price') // Hanya ambil kolom yg diperlukan
                    ->get();

                $cartCount = $tenantItems->sum('quantity');
                $cartTotal = $tenantItems->sum(function ($item) {
                    return $item->quantity * $item->unit_price;
                });
            }
        } elseif (session()->has('cart')) {
            // Jika tamu, hitung dari session dengan filter.
            $sessionCart = session('cart');
            $productIdsInCart = array_column($sessionCart, 'product_id');

            if (!empty($productIdsInCart)) {
                $tenantProductIds = \App\Models\Product::whereIn('id', $productIdsInCart)
                    ->where('user_id', $shopOwnerId)
                    ->pluck('id')->all();

                $filteredItems = collect($sessionCart)->filter(function ($item) use ($tenantProductIds) {
                    return isset($item['product_id']) && in_array($item['product_id'], $tenantProductIds);
                });

                $cartCount = $filteredItems->sum('quantity');
                $cartTotal = $filteredItems->sum(function ($item) {
                    return $item['quantity'] * $item['price'];
                });
            }
        }
    }
@endphp

<div class="offcanvas-menu-overlay"></div>
<div class="offcanvas-menu-wrapper">
    <div class="offcanvas__option">
        <div class="offcanvas__links">
            <a href="#">FAQs</a>
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
        <div class="price">{{ format_rupiah($cartTotal) }}</div>
    </div>
    <div id="mobile-menu-wrap"></div>
    <div class="offcanvas__text">
        <p>Free shipping, 30-day return or refund guarantee.</p>
    </div>
</div>