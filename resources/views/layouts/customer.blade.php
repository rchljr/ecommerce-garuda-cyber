<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth h-full">

<head>
    {{-- Menggunakan partial head yang sama dengan layout tenants untuk konsistensi --}}
    @include('layouts._partials.head')

    @push('styles')
        <style>
            body {
                font-family: 'Inter', sans-serif;
            }

            /* Sembunyikan dropdown secara default untuk dikontrol oleh JS */
            .dropdown-menu {
                display: none;
                transition: opacity 0.3s ease, transform 0.3s ease;
                transform: translateY(-10px);
                opacity: 0;
            }

            .dropdown-menu.active {
                display: block;
                transform: translateY(0);
                opacity: 1;
            }

            /* Style untuk badge notifikasi di keranjang */
            .cart-badge {
                position: absolute;
                top: -5px;
                right: -10px;
                height: 20px;
                width: 20px;
                background-color: #ef4444;
                /* red-500 */
                color: white;
                border-radius: 9999px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.75rem;
                font-weight: 600;
                border: 2px solid white;
            }
        </style>
    @endpush
    @stack('styles')
</head>

<body class="bg-gray-100 text-gray-800 h-full">

    <div id="app" class="min-h-full flex flex-col">
        <!-- Header Baru yang Lebih Modern -->
        <header class="bg-white shadow-sm sticky top-0 z-30">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-3">
                    <!-- Logo GCI -->
                    <div class="flex-shrink-0">
                        <a href="{{ route('tenants.index') }}" class="flex items-center" title="Jelajahi Semua Toko">
                            <img class="h-8 w-auto" src="{{ asset('images/LogoGCI.png') }}"
                                onerror="this.onerror=null;this.src='https://placehold.co/150x40/1f2937/ffffff?text=GCI';"
                                alt="Logo Garuda Cyber Indonesia">
                        </a>
                    </div>

                    <!-- Search Bar (Tengah) -->
                    <div class="flex-grow max-w-xl mx-4 hidden md:block">
                        <form action="{{ route('tenants.index') }}" method="GET">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="search" name="search"
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-full focus:ring-red-500 focus:border-red-500 transition"
                                    placeholder="Cari produk atau toko...">
                            </div>
                        </form>
                    </div>

                    <!-- Navigasi & Menu Customer (Kanan) -->
                    <div class="flex items-center gap-4 md:gap-6">
                        @php
                            $currentSubdomain = request()->route('subdomain');
                        @endphp

                        <a href="{{ route('tenant.home', ['subdomain' => $currentSubdomain]) }}"
                            class="text-sm font-medium text-gray-600 hover:text-red-600 transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                            </svg>
                            <span class="hidden lg:inline">Kembali ke Toko</span>
                        </a>

                        <div class="h-6 w-px bg-gray-200 hidden md:block"></div>

                        @auth('customers')
                            <!-- Ikon Keranjang -->
                            @inject('cartService', 'App\Services\CartService')
                            <a href="{{ route('tenant.cart.index', ['subdomain' => $currentSubdomain]) }}"
                                class="relative text-gray-600 hover:text-red-600 transition-colors">
                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.658-.463 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                                @if($cartService->getCartCount() > 0)
                                    <span id="cart-count" class="cart-badge">{{ $cartService->getCartCount() }}</span>
                                @endif
                            </a>

                            <!-- Dropdown Menu Customer -->
                            <div class="relative dropdown">
                                @php
                                    $customer = Auth::guard('customers')->user();
                                    // Membuat URL foto profil. Jika tidak ada foto, gunakan placeholder dengan inisial nama.
                                    $photoUrl = $customer->photo
                                        ? Storage::url($customer->photo)
                                        : 'https://placehold.co/40x40/e2e8f0/64748b?text=' . strtoupper(substr($customer->name, 0, 1));
                                @endphp
                                <button id="customer-menu-button"
                                    class="flex items-center gap-2 text-sm font-medium text-gray-700 rounded-full hover:bg-gray-100 p-1 transition">
                                    {{-- PERBAIKAN: Menampilkan foto profil --}}
                                    <img src="{{ $photoUrl }}" alt="Foto Profil"
                                        class="w-8 h-8 rounded-full object-cover border-2 border-gray-200">
                                    <span class="hidden lg:inline">Hi, {{ Str::words($customer->name, 1, '') }}</span>
                                    <svg class="w-4 h-4 text-gray-500 hidden lg:block" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                                <div id="customer-menu"
                                    class="dropdown-menu absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg py-1 z-40">
                                    {{-- PERBAIKAN: Header dropdown baru --}}
                                    <div class="px-4 py-3 border-b">
                                        <p class="text-sm font-semibold text-gray-900">{{ $customer->name }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ $customer->email }}</p>
                                    </div>
                                    <div class="py-1">
                                        <a href="{{ route('tenant.account.profile', ['subdomain' => $currentSubdomain]) }}"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil Saya</a>
                                        <a href="{{ route('tenant.account.orders', ['subdomain' => $currentSubdomain]) }}"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Pesanan Saya</a>
                                        <a href="{{ route('tenant.account.notifications', ['subdomain' => $currentSubdomain]) }}"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Notifikasi</a>
                                    </div>
                                    <div class="border-t"></div>
                                    <form action="{{ route('tenant.customer.logout', ['subdomain' => $currentSubdomain]) }}"
                                        method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('tenant.customer.login.form', ['subdomain' => $currentSubdomain]) }}"
                                class="text-sm font-medium text-gray-600 hover:text-red-600 transition-colors">
                                Login
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <!-- Konten Utama yang akan diisi oleh halaman spesifik (misal: pesanan, profil) -->
        <main class="flex-grow">
            @yield('content')
        </main>

        {{-- Memanggil file footer parsial yang konsisten --}}
        @include('layouts._partials.customer-footer')

    </div>

    @push('scripts')
        {{-- JavaScript untuk fungsionalitas dropdown --}}
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const menuButton = document.getElementById('customer-menu-button');
                const menu = document.getElementById('customer-menu');

                if (menuButton && menu) {
                    menuButton.addEventListener('click', function (event) {
                        event.stopPropagation();
                        menu.classList.toggle('active');
                    });

                    window.addEventListener('click', function (event) {
                        if (menu.classList.contains('active') && !menu.contains(event.target) && !menuButton.contains(event.target)) {
                            menu.classList.remove('active');
                        }
                    });
                }
            });
        </script>
    @endpush
    @stack('scripts')
</body>

</html>