<header class="flex items-center justify-between p-4 border-b bg-white">
    <div class="flex items-center">
        <button id="sidebarToggle" class="text-gray-600 hover:text-gray-900 mr-4" aria-label="Toggle Sidebar">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
            </svg>
        </button>
    </div>
    <div class="flex items-center space-x-4">
        <span class="font-semibold text-gray-700">Hi, Admin!</span>
        <img src="{{ asset('images/user.svg') }}" alt="Profile" class="w-10 h-10 rounded-full border-2 border-grey-800">
    </div>
</header>