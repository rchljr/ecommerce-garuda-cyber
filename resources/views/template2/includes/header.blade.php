<!DOCTYPE html>
<html lang="en">
@php
    // Variabel dan logika yang sama dari file asli Anda
    $isPreview = $isPreview ?? false;
    $currentSubdomain = request()->route('subdomain');
    $loggedInUser = Auth::user();
    $currentShop = null;
    $customTema = null;

    if ($loggedInUser) {
        $currentShop = $loggedInUser->shop;
        $customTema = $loggedInUser->customTema;
    }

    $logoPath = optional($customTema)->shop_logo;
    $logoUrl = $logoPath ? asset('storage/' . $logoPath) : asset('template2/img/logo.png'); // Fallback ke logo template 2
    $primaryColor = optional($customTema)->primary_color ?? '#82ae46'; // Default warna template 2
    $secondaryColor = optional($customTema)->secondary_color ?? '#000000'; // Default warna template 2

    // Logika Hitungan Ikon Header
    $cartCount = 0;
    $notificationCount = 0;
    $wishlistCount = 0;

    if (Auth::guard('customers')->check()) {
        $customerUser = Auth::guard('customers')->user();
        $cartService = app(\App\Services\CartService::class);
        $cartCount = $cartService->getCartCount();
        $wishlistCount = $customerUser->wishlist()->count();

        // Logika Notifikasi
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

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="description" content="Male_Fashion Template">
    <meta name="keywords" content="Male_Fashion, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @php
        $shopName = optional($currentShop)->shop_name ?? 'Toko Online';
        $shopLogo = optional($currentShop)->shop_logo ?? null;
    @endphp
    <title>@yield('title', 'Selamat Datang') - {{ $shopName }}</title>

    @if($shopLogo)
        <link rel="icon" href="{{ asset('storage/' . $shopLogo) }}" type="image/png">
    @else
        <link rel="icon" href="{{ asset('images/gci.png') }}" type="image/png">
    @endif

    {{-- Stylesheet dari Template 2 --}}
    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,500,600,700,800&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Lora:400,400i,700,700i&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Amatic+SC:400,700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('template2/css/open-iconic-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template2/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('template2/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template2/css/owl.theme.default.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template2/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('template2/css/aos.css') }}">
    <link rel="stylesheet" href="{{ asset('template2/css/ionicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template2/css/bootstrap-datepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('template2/css/jquery.timepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('template2/css/flaticon.css') }}">
    <link rel="stylesheet" href="{{ asset('template2/css/icomoon.css') }}">
    <link rel="stylesheet" href="{{ asset('template2/css/style.css') }}">
    {{-- Font Awesome untuk ikon tambahan --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">


    {{-- CSS DINAMIS UNTUK WARNA TEMA --}}
    <style>
        :root {
            --primary-color:
                {{ $primaryColor }}
            ;
            --secondary-color:
                {{ $secondaryColor }}
            ;
        }

        /* Style untuk menyamakan elemen dari template 1 */
        .top-bar-link a {
            color: rgba(255, 255, 255, 0.9);
        }

        .top-bar-link a:hover {
            color: #fff;
        }

        .top-bar-link .dropdown-menu {
            font-size: 14px;
        }

        .top-bar-link .dropdown-item {
            padding: .5rem 1rem;
        }

        .icon-with-badge {
            position: relative;
            display: inline-block;
            margin-left: 15px;
        }

        .icon-with-badge .badge {
            position: absolute;
            top: -8px;
            right: -12px;
            padding: 3px 6px;
            border-radius: 50%;
            background: #ca1515;
            color: white;
            font-size: 10px;
            border: 1px solid white;
        }

        .disabled-link {
            color: #ccc !important;
            cursor: not-allowed;
        }

        .navbar-brand img {
            max-height: 40px;
            width: auto;
            object-fit: contain;
        }

        /* [FIX] CSS untuk Tampilan Mobile */
        @media (max-width: 991.98px) {
            .ftco-navbar-light.scrolled {
                background: #fff !important;
            }

            .ftco-navbar-light .navbar-collapse {
                background: #ffffff;
                border-radius: 0 0 8px 8px;
                padding: 1rem;
                box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
                border-top: 1px solid #f0f0f0;
            }

            .ftco-navbar-light .navbar-nav>.nav-item>.nav-link {
                color: #333333 !important;
                /* Warna teks menu menjadi gelap */
                padding: 0.75rem 0.5rem;
            }

            .ftco-navbar-light .navbar-nav>.nav-item.active>a {
                color: var(--primary-color) !important;
                /* Warna link aktif */
            }

            /* Mengatur ikon fungsional di mobile */
            .ftco-navbar-light .navbar-nav:last-child {
                border-top: 1px solid #eeeeee;
                margin-top: 0.5rem;
                padding-top: 0.5rem;
            }

            .ftco-navbar-light .navbar-nav .nav-item .d-flex {
                justify-content: flex-start;
                /* Rata kiri */
                padding: 0.5rem;
            }

            .icon-with-badge i {
                color: #333333 !important;
                /* Warna ikon menjadi gelap */
            }

            .navbar-brand {
                color: #000 !important;
                /* Pastikan warna brand/logo terbaca */
            }
        }
    </style>
    @stack('styles')
</head>

<body class="goto-here">
    <div class="py-1 bg-primary">
        <div class="container">
            <div class="row no-gutters d-flex align-items-center px-md-0">
                <div class="col-lg-6 d-block">
                    <div class="text-left text-white py-1 top-bar-link">
                        <a href="{{ !$isPreview ? route('tenants.index') : '#' }}" class="px-2 py-1"
                            style="font-size: 13px;">
                            <i class="fas fa-store mr-1"></i> Jelajahi Toko Lain
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="d-flex justify-content-end align-items-center top-bar-link">
                        <a href="#" class="px-2">Bantuan</a>
                        @guest('customers')
                            @if(!$isPreview)
                                <a href="{{ route('tenant.customer.login.form', ['subdomain' => $currentSubdomain]) }}"
                                    class="px-2">Login</a>
                                <a href="{{ route('tenant.customer.register.form', ['subdomain' => $currentSubdomain]) }}"
                                    class="px-2">Daftar</a>
                            @else
                                <a href="#" class="px-2 disabled-link">Login</a>
                                <a href="#" class="px-2 disabled-link">Daftar</a>
                            @endif
                        @endguest

                        @auth('customers')
                            <div class="dropdown">
                                <a class="dropdown-toggle px-2" href="#" role="button" id="userDropdown"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-user"></i> Hi,
                                    {{ strtok(Auth::guard('customers')->user()->name, ' ') }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                                    @if(!$isPreview)
                                        <a class="dropdown-item"
                                            href="{{ route('tenant.account.profile', ['subdomain' => $currentSubdomain]) }}">Akun
                                            Saya</a>
                                        <a class="dropdown-item"
                                            href="{{ route('tenant.account.orders', ['subdomain' => $currentSubdomain]) }}">Pesanan
                                            Saya</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="#"
                                            onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();">Keluar</a>
                                        <form id="logout-form-header"
                                            action="{{ route('tenant.customer.logout', ['subdomain' => $currentSubdomain]) }}"
                                            method="POST" style="display: none;">@csrf</form>
                                    @endif
                                </div>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bagian Navigasi Utama --}}
    <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
        <div class="container">
            <a class="navbar-brand"
                href="{{ !$isPreview ? route('tenant.home', ['subdomain' => $currentSubdomain]) : '#' }}">
                <img src="{{ $logoUrl }}" alt="Logo {{ $shopName }}">
            </a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav"
                aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="oi oi-menu"></span> Menu
            </button>

            <div class="collapse navbar-collapse" id="ftco-nav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item {{ request()->routeIs('tenant.home') ? 'active' : '' }}">
                        <a href="{{ !$isPreview ? route('tenant.home', ['subdomain' => $currentSubdomain]) : '#' }}"
                            class="nav-link">Beranda</a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('tenant.shop') ? 'active' : '' }}">
                        <a href="{{ !$isPreview ? route('tenant.shop', ['subdomain' => $currentSubdomain]) : '#' }}"
                            class="nav-link">Toko</a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('tenant.contact') ? 'active' : '' }}">
                        <a href="{{ !$isPreview ? route('tenant.contact', ['subdomain' => $currentSubdomain]) : '#' }}"
                            class="nav-link">Kontak</a>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <li class="nav-item">
                        <div class="d-flex align-items-center h-100">
                            {{-- Wishlist Link --}}
                            @auth('customers')
                                <a href="{{ !$isPreview ? route('tenant.wishlist', ['subdomain' => $currentSubdomain]) : '#' }}"
                                    class="icon-with-badge">
                            @else
                                    <a href="{{ !$isPreview ? route('tenant.customer.login.form', ['subdomain' => $currentSubdomain]) : '#' }}"
                                        class="icon-with-badge">
                                @endauth
                                    <i class="fas fa-heart" style="font-size: 18px; color: #000;"></i>
                                    @if ($wishlistCount > 0)
                                        <span class="badge">{{ $wishlistCount }}</span>
                                    @endif
                                </a>

                                {{-- Notifikasi Link --}}
                                @auth('customers')
                                    <a href="{{ !$isPreview ? route('tenant.account.notifications', ['subdomain' => $currentSubdomain]) : '#' }}"
                                        class="icon-with-badge">
                                @else
                                        <a href="{{ !$isPreview ? route('tenant.customer.login.form', ['subdomain' => $currentSubdomain]) : '#' }}"
                                            class="icon-with-badge">
                                    @endauth
                                        <i class="fas fa-bell" style="font-size: 18px; color: #000;"></i>
                                        @if ($notificationCount > 0)
                                            <span class="badge">{{ $notificationCount }}</span>
                                        @endif
                                    </a>

                                    {{-- Keranjang --}}
                                    <a href="{{ !$isPreview ? route('tenant.cart.index', ['subdomain' => $currentSubdomain]) : '#' }}"
                                        class="icon-with-badge">
                                        <i class="fas fa-shopping-cart" style="font-size: 18px; color: #000;"></i>
                                        @if ($cartCount > 0)
                                            <span class="badge" id="cart-count">{{ $cartCount }}</span>
                                        @endif
                                    </a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</body>

</html>