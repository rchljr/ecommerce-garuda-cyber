<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth h-full">

<head>
    {{--
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Jelajahi Mitra Kami') - {{ config('app.name', 'Garuda Cyber Indonesia') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    --}}
    @include('layouts._partials.head')
    @push('styles')
        <style>
            body {
                font-family: 'Inter', sans-serif;
            }
        </style>
    @endpush
    @stack('styles')
</head>
<body class="bg-gray-100 text-gray-800 h-full">

    <div id="app" class="min-h-full flex flex-col">
        <!-- Header Sederhana -->
        <header class="bg-white shadow-sm sticky top-0 z-20">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <!-- Logo GCI -->
                    <a href="{{ route('landing') }}" class="flex items-center" title="Kembali ke Beranda">
                        <img class="h-8 w-auto" src="{{ asset('images/LogoGCI.png') }}"
                            onerror="this.onerror=null;this.src='https://placehold.co/150x40/1f2937/ffffff?text=GCI';"
                            alt="Logo Garuda Cyber Indonesia">
                    </a>

                    <!-- Tombol Kembali ke Halaman Utama -->
                    <a href="{{ route('landing') }}"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 -ml-1" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path
                                d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                        Buat Toko Anda Sendiri!
                    </a>
                </div>
            </div>
        </header>

        <!-- Konten Utama yang akan mengisi ruang kosong -->
        <main class="flex-grow">
            @yield('content')
        </main>

        <!-- Footer Sederhana -->
        <footer class="bg-gray-800 text-white">
            <div class="container mx-auto py-6 px-4 sm:px-6 lg:px-8 text-center">
                <p class="text-sm text-gray-400">&copy; {{ date('Y') }}
                    {{ config('app.name', 'Garuda Cyber Indonesia') }}. All rights reserved.
                </p>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>

</html>