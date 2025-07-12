<aside id="sidebar" class="sidebar w-64 bg-red-700 text-white flex flex-col" style="background-color: #B20000;">
    <div class="p-6 flex items-center gap-4 flex-shrink-0">
        <img src="{{ asset('images/gci.png') }}" alt="Logo" class="w-10 h-10 sidebar-logo">
        <span class="font-bold text-xl sidebar-text">E-COMMERCE GCI</span>
    </div>

    <nav class="flex-1 px-4 space-y-2 overflow-y-auto">
        <a href="{{ route('admin.dashboard') }}"
            class="sidebar-item flex items-center p-3 rounded-lg hover:bg-red-800 transition-colors">
            <span class="sidebar-icon">@include('dashboard-admin.icons.dashboard')</span>
            <span class="sidebar-text ml-4">Dashboard</span>
        </a>

        <div class="sidebar-dropdown">
            <button
                class="sidebar-item dropdown-toggle w-full flex items-center justify-between p-3 rounded-lg hover:bg-red-800 transition-colors">
                <div class="flex items-center">
                    <span class="sidebar-icon">@include('dashboard-admin.icons.mitra')</span>
                    <span class="sidebar-text ml-4">Mitra</span>
                </div>
                <svg class="w-4 h-4 text-white/70 dropdown-arrow sidebar-text" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div class="sidebar-submenu hidden mt-1 pl-8 space-y-1">
                <a href="{{ route('admin.mitra.verifikasi') }}"
                    class="sidebar-subitem block p-2 rounded-lg hover:bg-red-800 text-sm">Verifikasi Mitra</a>
                <a href="{{ route('admin.mitra.kelola') }}"
                    class="sidebar-subitem block p-2 rounded-lg hover:bg-red-800 text-sm">Kelola Mitra</a>
            </div>
        </div>

        <a href="{{ route('admin.paket.index') }}"
            class="sidebar-item flex items-center p-3 rounded-lg hover:bg-red-800 transition-colors">
            <span class="sidebar-icon">@include('dashboard-admin.icons.paket')</span>
            <span class="sidebar-text ml-4">Kelola Paket</span>
        </a>
        <a href="{{ route('admin.voucher.index') }}"
            class="sidebar-item flex items-center p-3 rounded-lg hover:bg-red-800 transition-colors">
            <span class="sidebar-icon">@include('dashboard-admin.icons.voucher')</span>
            <span class="sidebar-text ml-4">Kelola Voucher</span>
        </a>
        <a href="{{ route('admin.kategori.index') }}"
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
        </a>
        <a href="{{ route('admin.landing-page.statistics') }}"
            class="sidebar-item flex items-center p-3 rounded-lg hover:bg-red-800 transition-colors">
            <span class="sidebar-icon">@include('dashboard-admin.icons.landing')</span>
            <span class="sidebar-text ml-4">Landing Page</span>
        </a>
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