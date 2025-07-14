<!DOCTYPE html>
<html lang="en">
@php
    // --- BLOK LOGIKA THEME & KERANJANG ---
    // Variabel $customTema, $tenant, dan $shop tersedia berkat middleware dan View Composer.

    // 1. Pengaturan Dasar
    $isPreview = $isPreview ?? false;
    $currentSubdomain = !$isPreview && $tenant ? $tenant->subdomain->subdomain_name : null;

    // 2. Pengaturan Tema Dinamis
    // Ambil data dari $customTema, berikan nilai default jika tidak ada.
    $logoPath = optional($customTema)->shop_logo;
    $shopName = optional($customTema)->shop_name ?? (optional($shop)->shop_name ?? 'Nama Toko');
    $primaryColor = optional($customTema)->primary_color ?? '#82ae46'; // Default: Hijau
    $secondaryColor = optional($customTema)->secondary_color ?? '#F96D00'; // Default: Oranye

    // Siapkan URL logo dengan fallback ke logo default template.
    $logoUrl = $logoPath ? asset('storage/' . $logoPath) : null; // Defaultnya tidak ada logo, hanya nama

    // 3. Logika Hitungan Keranjang
    $cartCount = 0;
    if (!$isPreview && Auth::guard('customers')->check()) {
        $cart = Auth::guard('customers')->user()->cart;
        $cartCount = $cart ? $cart->items->sum('quantity') : 0;
    } elseif (!$isPreview && session()->has('cart')) {
        $cartCount = array_sum(array_column(session('cart'), 'quantity'));
    }
@endphp

<head>
    <title>{{ $shopName }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">


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

    {{-- CSS DINAMIS UNTUK WARNA TEMA --}}
    <style>
        :root {
            --primary-color: {{ $primaryColor }};
            --secondary-color: {{ $secondaryColor }};
        }

        /* Menerapkan warna tema ke elemen-elemen kunci */
        .bg-primary,
        .ftco-navbar-light .navbar-nav>.nav-item.cta>a {
            background: var(--primary-color) !important;
        }

        .text-primary,
        .ftco-navbar-light .navbar-nav>.nav-item.active>a {
            color: var(--primary-color) !important;
        }

        a,
        .product-category a.active {
            color: var(--primary-color);
        }

        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            opacity: 0.9;
        }

        .price,
        .price-sale {
            color: var(--secondary-color) !important;
        }
    </style>
    @stack('styles')
</head>

<body class="goto-here">
    <div class="py-1 bg-primary">
        <div class="container">
            <div class="row no-gutters d-flex align-items-start align-items-center px-md-0">
                <div class="col-lg-12 d-block">
                    <div class="row d-flex">
                        <div class="col-md pr-4 d-flex topper align-items-center">
                            <div class="icon mr-2 d-flex justify-content-center align-items-center"><span
                                    class="icon-phone2"></span></div>
                            {{-- Menampilkan nomor telepon dari data kontak tenant --}}
                            <span class="text">{{ $contact->phone ?? '+62 123 456 789' }}</span>
                        </div>
                        <div class="col-md pr-4 d-flex topper align-items-center">
                            <div class="icon mr-2 d-flex justify-content-center align-items-center"><span
                                    class="icon-paper-plane"></span></div>
                            {{-- Menampilkan email dari data kontak tenant --}}
                            <span class="text">{{ $contact->email ?? 'kontak@tokoanda.com' }}</span>
                        </div>
                        <div class="col-md-5 pr-4 d-flex topper align-items-center text-lg-right">
                            <span class="text">Pengiriman Cepat &amp; Terpercaya</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- BAGIAN NAVIGASI YANG SUDAH DIPERBAIKI TOTAL --}}
    <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
        <div class="container">
            {{-- Menampilkan nama toko sebagai brand --}}
            <a class="navbar-brand"
                href="{{ !$isPreview ? route('tenant.home', ['subdomain' => $currentSubdomain]) : '#' }}">
                {{-- LOGO & NAMA TOKO DINAMIS --}}
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo {{ $shopName }}"
                        style="max-height: 40px; margin-right: 10px;">
                @endif
                {{ $shopName }}
            </a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav"
                aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="oi oi-menu"></span> Menu
            </button>

            <div class="collapse navbar-collapse" id="ftco-nav">
                <ul class="navbar-nav ml-auto">
                    {{-- Link Home dinamis dengan pengecekan halaman aktif --}}
                    <li class="nav-item {{ request()->routeIs('tenant.home') ? 'active' : '' }}">
                        <a href="{{ !$isPreview ? route('tenant.home', ['subdomain' => $currentSubdomain]) : '#' }}"
                            class="nav-link">Home</a>
                    </li>

                    {{-- Dropdown Shop dengan struktur HTML yang benar --}}
                    <li class="nav-item dropdown {{ request()->routeIs('tenant.shop*') ? 'active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#" id="dropdown04" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">Toko</a>
                        <div class="dropdown-menu" aria-labelledby="dropdown04">
                            <a class="dropdown-item {{ request()->routeIs('tenant.shop') ? 'active' : '' }}"
                                href="{{ !$isPreview ? route('tenant.shop', ['subdomain' => $currentSubdomain]) : '#' }}">
                                Semua Produk
                            </a>
                            {{-- PERBAIKAN: Link dibuat tenant-aware & preview-aware --}}
                            <a class="dropdown-item"
                                href="{{ !$isPreview ? route('tenant.wishlist', ['subdomain' => $currentSubdomain]) : '#' }}">Wishlist</a>
                            {{-- PERBAIKAN: Link dibuat tenant-aware & preview-aware --}}
                            <a class="dropdown-item"
                                href="{{ !$isPreview ? route('tenant.cart.index', ['subdomain' => $currentSubdomain]) : '#' }}">Keranjang</a>
                            {{-- PERBAIKAN: Link dibuat tenant-aware & preview-aware --}}
                            <a class="dropdown-item"
                                href="{{ !$isPreview ? route('tenant.checkout.index', ['subdomain' => $currentSubdomain]) : '#' }}">Checkout</a>
                        </div>
                    </li>

                    {{-- PERBAIKAN: Link dibuat tenant-aware & preview-aware --}}
                    {{-- <li class="nav-item"><a
                            href="{{ !$isPreview ? route('tenant.about', ['subdomain' => $currentSubdomain]) : '#' }}"
                            class="nav-link">Tentang Kami</a></li> --}}
                    {{-- PERBAIKAN: Link dibuat tenant-aware & preview-aware --}}
                    <li class="nav-item"><a
                            href="{{ !$isPreview ? route('tenant.contact', ['subdomain' => $currentSubdomain]) : '#' }}"
                            class="nav-link">Kontak</a></li>

                    {{-- Link ke halaman keranjang dengan hitungan dinamis --}}
                    {{-- Cari bagian navigasi keranjang --}}
                    <li class="nav-item cta cta-colored">
                        <a href="{{ !$isPreview ? route('tenant.cart.index', ['subdomain' => $currentSubdomain]) : '#' }}"
                            class="nav-link">
                            <span class="icon-shopping_cart"></span>[<span id="cart-count">{{ $cartCount }}</span>]
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- AKHIR BAGIAN NAVIGASI --}}
