
<!-- Header / Navbar -->
<header class="bg-white/80 backdrop-blur-lg sticky top-0 z-50 border-b border-gray-200">
    <div class="container mx-auto px-6 py-4">
        <div class="flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center space-x-2">
                <a href="#">
                    <img src="images/logoGCI.png" alt="Logo" />
                </a>
            </div>

            <!-- Navigasi (Desktop) -->
            <nav class="hidden md:flex items-center space-x-8">
                <a href="#" class="text-gray-600 hover:text-red-600 transition-colors">Beranda</a>
                <a href="#layanan" class="text-gray-600 hover:text-red-600 transition-colors">Layanan</a>
                <a href="#tema" class="text-gray-600 hover:text-red-600 transition-colors">Tema</a>
                <a href="#harga" class="text-gray-600 hover:text-red-600 transition-colors">Biaya</a>
                <a href="#faq" class="text-gray-600 hover:text-red-600 transition-colors">FAQ</a>
                <a href="#testimoni" class="text-gray-600 hover:text-red-600 transition-colors">Testimoni</a>
                <a href="#add-testimonial" class="text-gray-600 hover:text-red-600 transition-colors"></a>
            </nav>

            <!-- Tombol (Desktop) -->
            <div class="hidden md:flex items-center space-x-4">
                <a href="{{ route('register.form') }}"
                    class="px-6 py-2 text-base font-bold text-gray-900 bg-white border-2 border-red-600 rounded-xl hover:bg-red-50 transition-colors shadow-sm">Daftar</a>
                <a href="{{ route('login') }}"
                    class="px-6 py-2 text-base font-bold text-gray-900 bg-white border-2 border-yellow-400 rounded-xl hover:bg-yellow-50 transition-colors shadow-sm">Masuk</a>
            </div>

            <!-- Tombol Menu (Mobile) -->
            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-gray-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <!-- Menu Dropdown (Mobile) -->
    <div id="mobile-menu" class="hidden md:hidden">
        <a href="#" class="block py-2 px-6 text-sm text-gray-600 hover:bg-gray-100">Beranda</a>
        <a href="#layanan" class="block py-2 px-6 text-sm text-gray-600 hover:bg-gray-100">Layanan</a>
        <a href="#tema" class="block py-2 px-6 text-sm text-gray-600 hover:bg-gray-100">Tema</a>
        <a href="#harga" class="block py-2 px-6 text-sm text-gray-600 hover:bg-gray-100">Biaya</a>
        <a href="#faq" class="block py-2 px-6 text-sm text-gray-600 hover:bg-gray-100">FAQ</a>
        <a href="#testimoni" class="block py-2 px-6 text-sm text-gray-600 hover:bg-gray-100">Testimoni</a>
        <a href="#add-testimonial" class="block py-2 px-6 text-sm text-gray-600 hover:bg-gray-100"></a>
        <div class="px-6 py-4 border-t border-gray-200">
            <a href="{{ route('register.form') }}"
                class="block w-full text-center mb-2 px-6 py-2 text-base font-bold text-gray-900 bg-white border-2 border-red-600 rounded-xl hover:bg-red-50 transition-colors shadow-sm">Daftar</a>
            <a href="{{ route('login') }}"
                class="block w-full text-center px-6 py-2 text-base font-bold text-gray-900 bg-white border-2 border-yellow-400 rounded-xl hover:bg-yellow-50 transition-colors shadow-sm">Masuk</a>
        </div>
    </div>
</header>