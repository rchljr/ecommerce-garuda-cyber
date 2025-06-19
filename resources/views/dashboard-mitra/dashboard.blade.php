<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mitra Dashboard') - E-Commerce Garuda Cyber</title>
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

    <h2>HAI MITRA DASHBOARD</h2>
    @include('layouts._partials.scripts')
    @stack('scripts')
</body>

</html>