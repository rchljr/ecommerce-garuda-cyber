<aside id="sidebar" class="sidebar w-64 bg-red-700 text-white flex flex-col" style="background-color: #B20000;">
    @auth
        @php
            // Mengambil data toko dari user yang sedang login.
            // Menggunakan optional() untuk mencegah error jika relasi 'shop' tidak ada.
            $shop = optional(Auth::user()->shop);
            $shopLogo = $shop->shop_logo ?? null;
            $shopName = $shop->shop_name ?? 'Mitra Dashboard';
        @endphp
        <div class="p-6 flex items-center gap-4">
            <img src="{{ $shopLogo ? asset('storage/' . $shopLogo) : asset('images/GCI.png') }}" alt="Logo"
                class="w-10 h-10 rounded-md object-cover sidebar-logo">
            <span class="font-bold text-xl sidebar-text">{{ $shopName }}</span>
        </div>
    @else
        {{-- Tampilan default jika tidak ada user yang login --}}
        <div class="p-6 flex items-center gap-4">
            <img src="{{ asset('images/GCI.png') }}" alt="Logo" class="w-10 h-10 sidebar-logo">
            <span class="font-bold text-xl sidebar-text">E-COMMERCE GCI</span>
        </div>
    @endauth

    <nav class="flex-1 px-4 space-y-2">
        <a href="{{ route('mitra.dashboard') }}"
            class="sidebar-item flex items-center p-3 rounded-lg hover:bg-red-800 transition-colors">
            <span class="sidebar-icon">@include('dashboard-admin.icons.dashboard')</span>
            <span class="sidebar-text ml-4">Dashboard</span>
        </a>

        <a href="{{ route('mitra.produk') }}"
            class="sidebar-item flex items-center p-3 rounded-lg hover:bg-red-800 transition-colors">
            <span class="sidebar-icon">@include('dashboard-admin.icons.paket')</span>
            <span class="sidebar-text ml-4">Kelola Produk</span>
        </a>

        <div class="sidebar-dropdown">
            <button
                class="sidebar-item dropdown-toggle w-full flex items-center justify-between p-3 rounded-lg hover:bg-red-800 transition-colors">
                <div class="flex items-center">
                    <span class="sidebar-icon">@include('dashboard-admin.icons.kategori')</span>
                    <span class="sidebar-text ml-4">Panel</span>
                </div>
                <svg class="w-4 h-4 text-white/70 dropdown-arrow sidebar-text" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div class="sidebar-submenu hidden mt-1 pl-8 space-y-1">
                <a href="{{ route('mitra.hero') }}"
                    class="sidebar-subitem block p-2 rounded-lg hover:bg-red-800 text-sm">Kelola Hero</a>
                <a href="{{ route('mitra.banner') }}"
                    class="sidebar-subitem block p-2 rounded-lg hover:bg-red-800 text-sm">Kelola Banner</a>
                <a href="{{ route('mitra.tema') }}"
                    class="sidebar-subitem block p-2 rounded-lg hover:bg-red-800 text-sm">Kelola Tema</a>
            </div>
        </div>

        <a href="{{ route('mitra.contacts') }}"
            class="sidebar-item flex items-center p-3 rounded-lg hover:bg-red-800 transition-colors">
            <span class="sidebar-icon">@include('dashboard-admin.icons.testimoni')</span>
            <span class="sidebar-text ml-4">Kelola Kontak</span>
        </a>

        <a href="{{ route('mitra.orders.show') }}"
            class="sidebar-item flex items-center p-3 rounded-lg hover:bg-red-800 transition-colors">
            <span class="sidebar-icon">@include('dashboard-admin.icons.landing')</span>
            <span class="sidebar-text ml-4">Kelola Order</span>
        </a>
        {{-- <a href="{{ route('admin.kategori.index') }}"
            class="sidebar-item flex items-center p-3 rounded-lg hover:bg-red-800 transition-colors">
            <span class="sidebar-icon">@include('dashboard-admin.icons.kategori')</span>
            <span class="sidebar-text ml-4">Kelola Kategori</span>
        </a>
        <a href="{{ route('admin.testimoni.index') }}"
            class="sidebar-item flex items-center p-3 rounded-lg hover:bg-red-800 transition-colors">
            <span class="sidebar-icon">@include('dashboard-admin.icons.testimoni')</span>
            <span class="sidebar-text ml-4">Kelola Testimoni</span>
        </a>
        <a href="{{ route('admin.pendapatan.index') }}"
            class="sidebar-item flex items-center p-3 rounded-lg hover:bg-red-800 transition-colors">
            <span class="sidebar-icon">@include('dashboard-admin.icons.pendapatan')</span>
            <span class="sidebar-text ml-4">Kelola Pendapatan</span>
        </a> --}}
        {{-- <a href="{{ route('admin.landing-page.statistics') }}"
            class="sidebar-item flex items-center p-3 rounded-lg hover:bg-red-800 transition-colors">
            <span class="sidebar-icon">@include('dashboard-admin.icons.landing')</span>
            <span class="sidebar-text ml-4">Landing Page</span>
        </a> --}}
    </nav>

    <div class="p-4 border-t border-red-800">
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
        <a href="#"
            class="logout-confirm sidebar-item flex items-center p-3 rounded-lg hover:bg-red-800 transition-colors"
            data-form-id="logout-form">
            <span class="sidebar-icon">@include('dashboard-admin.icons.logout')</span>
            <span class="sidebar-text ml-4">Logout</span>
        </a>
    </div>
</aside>
