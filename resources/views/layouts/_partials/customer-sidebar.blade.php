<div class="flex items-center space-x-4 mb-6">
    <img src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : 'https://placehold.co/64x64/EBF4FF/76A9FA?text=' . strtoupper(substr(Auth::user()->name, 0, 1)) }}"
        alt="Foto Profil" class="w-16 h-16 rounded-full object-cover flex-shrink-0">
    <div class="min-w-0">
        <p class="font-bold text-lg truncate">{{ Auth::user()->name }}</p>
        <p class="text-sm text-gray-500 truncate">{{ Auth::user()->phone ?? 'No. Telepon belum diisi' }}</p>
    </div>
</div>
<nav class="space-y-2">
    <a href="{{ route('customer.profile') }}"
        class="flex items-center px-4 py-2 rounded-lg font-semibold {{ request()->routeIs('customer.profile') ? 'bg-gray-800 text-white' : 'text-gray-600 hover:bg-gray-700 hover:text-white' }} transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
        Profil Saya
    </a>
    <a href="{{ route('customer.orders') }}"
        class="flex items-center px-4 py-2 rounded-lg font-semibold {{ request()->routeIs('customer.orders') ? 'bg-gray-800 text-white' : 'text-gray-600 hover:bg-gray-700 hover:text-white' }} transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
            </path>
        </svg>
        Pesanan Saya
    </a>
    <a href="{{ route('customer.notifications') }}"
        class="flex items-center px-4 py-2 rounded-lg font-semibold {{ request()->routeIs('customer.notifications') ? 'bg-gray-800 text-white' : 'text-gray-600 hover:bg-gray-700 hover:text-white' }} transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
            </path>
        </svg>
        Notifikasi Saya
    </a>
    <a href="{{ route('customer.vouchers') }}"
        class="flex items-center px-4 py-2 rounded-lg font-semibold {{ request()->routeIs('customer.vouchers') ? 'bg-gray-800 text-white' : 'text-gray-600 hover:bg-gray-700 hover:text-white' }} transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
            </path>
        </svg>
        Voucher Saya
    </a>
    {{-- <a href="{{ route('customer.points') }}"
        class="flex items-center px-4 py-2 rounded-lg font-semibold {{ request()->routeIs('customer.points') ? 'bg-gray-800 text-white' : 'text-gray-600 hover:bg-gray-700 hover:text-white' }} transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v.01M12 6v-1m0-1V4m0 2.01v.01M12 14v1m0 1v1m0-1v-.01M12 16v-1m0-1v-.01m0-2.01v.01M4.599 15.401A2.002 2.002 0 013 13.5V11c0-1.657.895-3 2-3s2 1.343 2 3v2.5c0 .65.268 1.25.701 1.683A2.003 2.003 0 0110.5 17c-1.11 0-2.08-.402-2.599-1M4.599 15.401L4.5 15.5m0-2.01v.01M4.5 13.5v-1m0-1V11m0 2.01v.01M19.401 9.599A2.002 2.002 0 0121 11.5V14c0 1.657-.895 3-2 3s-2-1.343-2-3v-2.5c0-.65-.268-1.25-.701-1.683A2.003 2.003 0 0113.5 7c1.11 0 2.08.402 2.599 1M19.401 9.599L19.5 9.5m0 2.01v.01M19.5 11.5v1m0 1V14m0-2.01v.01">
            </path>
        </svg>
        Poin Saya
    </a> --}}
    <div class="border-t border-gray-200 mt-4 pt-4">
        <form id="logout-form-sidebar" action="{{ route('customer.logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();"
            class="flex items-center px-4 py-2 rounded-lg font-semibold text-gray-600 hover:bg-gray-700 hover:text-white transition-colors">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                </path>
            </svg>
            Logout
        </a>
    </div>
</nav>