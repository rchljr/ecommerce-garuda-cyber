<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - E-Commerce Garuda Cyber</title>
    <meta name="keywords" content="ecommerce, garuda cyber, toko online, produk, jasa">
    <meta name="description" content="E-Commerce Garuda Cyber: Temukan berbagai produk dan layanan terbaik.">
    <meta name="author" content="Garuda Cyber">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/gci.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .sidebar {
            transition: width 0.3s ease;
        }

        .sidebar.collapsed {
            width: 5rem;
        }

        .sidebar.collapsed .sidebar-text,
        .sidebar.collapsed .dropdown-arrow {
            display: none;
        }

        .sidebar.collapsed .sidebar-item,
        .sidebar.collapsed .sidebar-logout {
            justify-content: center;
        }
    </style>
    @stack('styles')
</head>

<body class="bg-gray-100">

    <div class="flex h-screen bg-white">
        @include('layouts._partials.admin-sidebar')

        <div class="flex-1 flex flex-col overflow-hidden">
            @include('layouts._partials.admin-topbar')

            <main class="flex-1 bg-white p-8 relative">
                <div class="absolute inset-0 z-0 pointer-events-none" aria-hidden="true">
                    <div class="absolute bottom-0 -right-20 w-96 h-96">
                        <img src="{{ asset('images/bg-nav.png') }}" alt="Dekoratif"
                            class="w-full h-full object-cover opacity-75">
                    </div>
                </div>
                <div class="relative z-10 h-auto flex flex-col">
                    @yield('content')
                </div>
            </main>

            <footer class="text-center p-4 text-gray-500 text-sm border-t bg-white z-10 flex-shrink-0">
                Copyright 2025 PT Garuda Cyber Indonesia
            </footer>
        </div>
    </div>

    @include('layouts._partials.scripts')
    @stack('scripts')
</body>

</html>