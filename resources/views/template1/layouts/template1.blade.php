<!DOCTYPE html>
<html lang="id">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta charset="UTF-8">
    <meta name="description" content="Male_Fashion Template">
    <meta name="keywords" content="Male_Fashion, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
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

    <!-- Css Styles -->
    <link rel="stylesheet" href="{{ asset('template1/css/bootstrap.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('template1/css/font-awesome.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('template1/css/elegant-icons.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('template1/css/magnific-popup.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('template1/css/nice-select.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('template1/css/owl.carousel.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('template1/css/slicknav.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('template1/css/style.css') }}" type="text/css">
    @stack('styles') {{-- Untuk CSS tambahan dari child views --}}

    <style>
        /* CSS untuk transisi halaman yang mulus */
        body {
            opacity: 0;
            /* Sembunyikan body secara default */
            transition: opacity 0.5s ease-in-out;
            /* Animasi fade */
        }

        body.page-loaded {
            opacity: 1;
            /* Tampilkan body setelah dimuat */
        }

        body.page-leaving {
            opacity: 0;
            /* Sembunyikan body saat meninggalkan halaman */
        }

        #preloder {
            z-index: 99999;
            /* Pastikan preloader di atas semua */
        }
    </style>
</head>

<body>
    <!-- Page Preloder -->
    <div id="preloder">
        <div class="loader"></div>
    </div>

    {{-- Offcanvas Menu --}}
    @include('template1.includes.offcanvas')

    {{-- Header Section --}}
    @include('template1.includes.header')

    {{-- Konten utama halaman --}}
    <main id="main-content"> {{-- Tambahkan ID untuk konten utama --}}
        @yield('content')
    </main>

    {{-- Footer Section --}}
    @include('template1.includes.footer')

    {{-- Search Modal --}}
    @include('template1.includes.search_modal')

    {{-- Js Plugins --}}
    @include('template1.includes.scripts')
    @stack('scripts') {{-- Untuk JS tambahan dari child views --}}

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Hilangkan preloader saat DOM selesai dimuat
            if (document.getElementById('preloder')) {
                document.getElementById('preloder').style.display = 'none';
            }

            // Tambahkan kelas untuk menampilkan halaman dengan fade-in
            document.body.classList.add('page-loaded');

            // Ambil semua link internal yang mengarah ke halaman yang sama (tanpa hash atau dengan hash)
            // Atau semua link yang tidak diawali dengan '#' atau 'javascript:' dan tidak punya target='_blank'
            document.querySelectorAll('a[href^="{{ url('/') }}"]:not([href^="#"]):not([target="_blank"]), a[href^="./"]:not([href^="#"]):not([target="_blank"]), a[href^="/"]:not([href^="#"]):not([target="_blank"])').forEach(link => {
                link.addEventListener('click', function (e) {
                    // Cek apakah link adalah download atau action yang tidak perlu transisi
                    if (this.hasAttribute('download') || this.getAttribute('href').startsWith('javascript:')) {
                        return;
                    }

                    e.preventDefault(); // Mencegah navigasi default

                    const targetUrl = this.href; // Dapatkan URL tujuan

                    // Tambahkan kelas fade-out ke body
                    document.body.classList.remove('page-loaded');
                    document.body.classList.add('page-leaving');

                    // Tunggu animasi fade-out selesai sebelum navigasi
                    setTimeout(() => {
                        window.location.href = targetUrl;
                    }, 500); // Sesuaikan durasi timeout dengan durasi transisi CSS (0.5s = 500ms)
                });
            });
        });
    </script>
</body>

</html>