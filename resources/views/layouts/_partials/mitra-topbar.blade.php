<header class="flex items-center justify-between p-4 border-b bg-white">
    <div class="flex items-center">
        <button id="sidebarToggle" class="text-gray-600 hover:text-gray-900 mr-4" aria-label="Toggle Sidebar">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
            </svg>
        </button>
    </div>
    <div class="flex items-center space-x-4">
        <span class="font-semibold text-gray-700">Hi,
            {{ optional(Auth::user()->shop)->shop_name ?? Auth::user()->name }}</span>
        {{-- Menggunakan inisial dari nama user sebagai fallback jika foto tidak ada --}}
        <img src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : 'https://placehold.co/40x40/EBF4FF/76A9FA?text=' . strtoupper(substr(Auth::user()->shop->shop_name, 0, 1)) }}"
            alt="Profile" class="w-10 h-10 rounded-full border-2 border-gray-300 object-cover">
    </div>
</header>