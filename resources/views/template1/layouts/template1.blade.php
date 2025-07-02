<!DOCTYPE html>
<html lang="zxx">

<head>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta charset="UTF-8">
    <meta name="description" content="Male_Fashion Template">
    <meta name="keywords" content="Male_Fashion, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Male-Fashion | Template')</title>

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
    <script>
        ! function(f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function() {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', 'YOUR_PIXEL_ID'); // <-- Ganti dengan ID Pixel Anda
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id=YOUR_PIXEL_ID&ev=PageView&noscript=1" /></noscript>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Hilangkan preloader saat DOM selesai dimuat
            if (document.getElementById('preloder')) {
                document.getElementById('preloder').style.display = 'none';
            }

            // Tambahkan kelas untuk menampilkan halaman dengan fade-in
            document.body.classList.add('page-loaded');

            // Ambil semua link internal yang mengarah ke halaman yang sama (tanpa hash atau dengan hash)
            // Atau semua link yang tidak diawali dengan '#' atau 'javascript:' dan tidak punya target='_blank'
            document.querySelectorAll(
                'a[href^="{{ url('/') }}"]:not([href^="#"]):not([target="_blank"]), a[href^="./"]:not([href^="#"]):not([target="_blank"]), a[href^="/"]:not([href^="#"]):not([target="_blank"])'
                ).forEach(link => {
                link.addEventListener('click', function(e) {
                    // Cek apakah link adalah download atau action yang tidak perlu transisi
                    if (this.hasAttribute('download') || this.getAttribute('href').startsWith(
                            'javascript:')) {
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
