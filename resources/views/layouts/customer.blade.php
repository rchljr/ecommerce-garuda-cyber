<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        // Variabel ini sekarang tersedia secara global berkat middleware
        $shopName = optional($currentShop)->shop_name ?? 'Toko Online';
        $shopLogo = optional($currentShop)->shop_logo ?? null;

        // PERBAIKAN: Dapatkan path template secara dinamis dari $currentTenant
        // Memberi fallback ke 'template1' jika karena suatu hal template tidak terdefinisi
        $templatePath = optional($currentTenant->template)->path ?? 'template1';
    @endphp
    <title>@yield('title', 'Selamat Datang') - {{ $shopName }}</title>

    @if($shopLogo)
        <link rel="icon" href="{{ asset('storage/' . $shopLogo) }}" type="image/png">
    @else
        <link rel="icon" href="{{ asset('images/gci.png') }}" type="image/png">
    @endif

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- PERTAMA: Muat CSS dari Template -->
    <link rel="stylesheet" href="{{ asset($templatePath . '/css/bootstrap.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset($templatePath . '/css/font-awesome.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset($templatePath . '/css/elegant-icons.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset($templatePath . '/css/magnific-popup.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset($templatePath . '/css/nice-select.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset($templatePath . '/css/owl.carousel.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset($templatePath . '/css/slicknav.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset($templatePath . '/css/style.css') }}" type="text/css">

    <!-- KEDUA: Muat Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    @stack('styles')
</head>

<body>
    <!-- Page Preloder -->
    <div id="preloder">
        <div class="loader"></div>
    </div>

    {{-- Offcanvas Menu --}}
    @include($templatePath . '.includes.offcanvas')

    {{-- Header Section dari Template --}}
    @include($templatePath . '.includes.header')

    {{-- Konten utama (login/register) akan disisipkan di sini --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer Section dari Template --}}
    @include($templatePath . '.includes.footer')

    {{-- Search Modal dari Template --}}
    @include($templatePath . '.includes.search_modal')

    {{-- Js Plugins dari Template--}}
    @include($templatePath . '.includes.scripts')

    {{-- Script khusus untuk halaman ini --}}
    @stack('scripts')
</body>

</html>