<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Akun Saya') - {{ config('app.name', 'Toko Anda') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50 text-gray-800">
    <div x-data="{ mobileMenuOpen: false }" class="min-h-screen flex flex-col">
        <header class="bg-white shadow-sm sticky top-0 z-40">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <a href="/" class="text-2xl font-bold text-gray-900">NamaToko</a>

                    <nav class="hidden lg:flex space-x-8">
                        <a href="#" class="text-gray-600 hover:text-gray-900 transition-colors">Home</a>
                        <a href="#" class="text-gray-600 hover:text-gray-900 transition-colors">Produk</a>
                        <a href="#" class="text-gray-600 hover:text-gray-900 transition-colors">Tentang Kami</a>
                    </nav>

                    <div class="hidden lg:flex items-center space-x-5">
                        <a href="#" class="text-gray-500 hover:text-gray-900 relative" title="Keranjang Belanja">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </a>
                        <div class="w-px h-6 bg-gray-200"></div>

                        <div x-data="{ dropdownOpen: false }" class="relative">
                            <button @click="dropdownOpen = !dropdownOpen"
                                class="flex items-center space-x-2 focus:outline-none">
                                <img src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : 'https://placehold.co/32x32/EBF4FF/76A9FA?text=' . strtoupper(substr(Auth::user()->name, 0, 1)) }}"
                                    class="w-8 h-8 rounded-full object-cover" alt="Avatar">
                                <span class="text-sm font-medium">Hi, {{ strtok(Auth::user()->name, ' ') }}</span>
                                <svg class="w-4 h-4 text-gray-500 transition-transform duration-200"
                                    :class="{'transform rotate-180': dropdownOpen}" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-transition x-cloak
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 ring-1 ring-black ring-opacity-5">
                                <a href="{{ route('customer.profile') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Akun Saya</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Pesanan
                                    Saya</a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <form id="logout-form-dropdown" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">@csrf</form>
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form-dropdown').submit();"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Log
                                    Out</a>
                            </div>
                        </div>
                    </div>

                    <div class="lg:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen"
                            class="text-gray-600 hover:text-gray-900 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path :class="{'hidden': mobileMenuOpen, 'inline-flex': !mobileMenuOpen }"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"></path>
                                <path :class="{'hidden': !mobileMenuOpen, 'inline-flex': mobileMenuOpen }"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        {{-- PERUBAHAN: Panel Menu Mobile Slide-in dari Kanan --}}
        <div x-show="mobileMenuOpen" x-cloak class="fixed inset-0 flex justify-end z-50 lg:hidden">
            <!-- Overlay -->
            <div x-show="mobileMenuOpen" @click="mobileMenuOpen = false"
                x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black bg-opacity-50"></div>

            <!-- Panel Menu -->
            <div x-show="mobileMenuOpen" x-transition:enter="transition ease-in-out duration-300 transform"
                x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in-out duration-300 transform"
                x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                class="relative max-w-xs w-full bg-white flex flex-col h-full">

                <div class="flex items-center justify-between px-4 py-4 border-b">
                    <span class="text-lg font-bold">Menu</span>
                    <button @click="mobileMenuOpen = false" class="text-gray-600 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-4 flex-grow overflow-y-auto">
                    <nav class="flex flex-col space-y-2">
                        <a href="#"
                            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Home</a>
                        <a href="#"
                            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Produk</a>
                        <a href="#"
                            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Tentang
                            Kami</a>
                    </nav>

                    <div class="border-t my-4"></div>

                    <div class="flex items-center px-2 mb-4">
                        <img src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : 'https://placehold.co/40x40/EBF4FF/76A9FA?text=' . strtoupper(substr(Auth::user()->name, 0, 1)) }}"
                            class="w-10 h-10 rounded-full object-cover" alt="Avatar">
                        <div class="ml-3">
                            <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                            <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                    <nav class="flex flex-col space-y-2">
                        <a href="{{ route('customer.profile') }}"
                            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Akun
                            Saya</a>
                        <a href="#"
                            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Pesanan
                            Saya</a>
                        <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST"
                            style="display: none;">@csrf</form>
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();"
                            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Log
                            Out</a>
                    </nav>
                </div>
            </div>
        </div>

        <main class="flex-grow">
            @yield('content')
        </main>

        @include('layouts._partials.customer-footer')

    </div>
    @stack('scripts')
</body>

</html>