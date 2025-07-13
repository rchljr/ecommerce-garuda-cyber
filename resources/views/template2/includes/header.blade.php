<!DOCTYPE html>
<html lang="en">
@php
    // Logika ini disiapkan di bagian atas agar rapi.

    // Cek apakah ini mode preview dari TemplateController.
    $isPreview = $isPreview ?? false;

    // Ambil data tenant yang sudah disiapkan oleh middleware.
    // Variabel $tenant diasumsikan sudah ada dan dikirim dari controller.
    $tenant = $tenant ?? null;

    // Ambil subdomain saat ini jika bukan mode preview.
    $currentSubdomain = !$isPreview && $tenant ? $tenant->subdomain->subdomain_name : null;

    // Ambil data kontak dan toko dari tenant.
    $contact = $tenant ? $tenant->contact : null;
    $shop = $tenant ? $tenant->shop : null;

    // Logika untuk hitungan keranjang belanja.
    $cartCount = 0;
    if (!$isPreview && Auth::guard('customers')->check()) {
        // Jika pelanggan login, hitung dari database.
        $cart = Auth::guard('customers')->user()->cart;
        $cartCount = $cart ? $cart->items->sum('quantity') : 0;
    } elseif (!$isPreview && session()->has('cart')) {
        // Jika tamu, hitung dari session.
        $cartCount = array_sum(array_column(session('cart'), 'quantity'));
    }
@endphp

<head>
    {{-- Menggunakan nama toko dari tenant secara dinamis sebagai judul halaman --}}
    <title>{{ $shop->shop_name ?? 'Nama Toko' }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,500,600,700,800&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Lora:400,400i,700,700i&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Amatic+SC:400,700&display=swap" rel="stylesheet">

    {{-- Path ke aset CSS di folder public menggunakan helper asset() --}}
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
                {{ $shop->shop_name ?? 'Nama Toko' }}
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
                    <li class="nav-item cta cta-colored">
                        <a href="{{ !$isPreview ? route('tenant.cart.index', ['subdomain' => $currentSubdomain]) : '#' }}"
                            class="nav-link">
                            <span class="icon-shopping_cart"></span>[<span
                                id="cart-count">{{ $cartCount ?? 0 }}</span>]
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- AKHIR BAGIAN NAVIGASI --}}
