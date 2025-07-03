<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        // Variabel $currentShop ini tersedia secara global di semua view yang menggunakan grup middleware 'web'
        $shopName = optional($currentShop)->shop_name ?? 'Toko Online';
        $shopLogo = optional($currentShop)->shop_logo ?? null;
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
    <link rel="stylesheet" href="{{ asset('template1/css/bootstrap.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('template1/css/font-awesome.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('template1/css/elegant-icons.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('template1/css/magnific-popup.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('template1/css/nice-select.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('template1/css/owl.carousel.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('template1/css/slicknav.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('template1/css/style.css') }}" type="text/css">

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
    @include('template1.includes.offcanvas')

    {{-- Header Section dari Template 1 --}}
    @include('template1.includes.header')

    {{-- Konten utama (login/register) akan disisipkan di sini --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer Section dari Template 1 --}}
    @include('template1.includes.footer')

    {{-- Search Modal dari Template 1 --}}
    @include('template1.includes.search_modal')

    {{-- Js Plugins dari Template 1 --}}
    @include('template1.includes.scripts')
    @stack('scripts')
</body>

</html>