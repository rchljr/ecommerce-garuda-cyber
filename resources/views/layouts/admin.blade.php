<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - E-Commerce Garuda Cyber</title>
    <meta name="keywords" content="ecommerce, garuda cyber, toko online, produk, jasa">
    <meta name="description" content="E-Commerce Garuda Cyber: Temukan berbagai produk dan layanan terbaik.">
    <meta name="author" content="Garuda Cyber">
    <link rel="icon" href="{{ asset('images/gci.png') }}" type="image/png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

            {{-- Wrapper ini sekarang menjadi area scroll utama --}}
            <div class="flex-1 overflow-y-auto relative">

                {{-- Pembungkus ini memastikan footer terdorong ke bawah dan berada di atas gambar --}}
                <div class="min-h-full flex flex-col relative z-10">

                    {{-- Konten Utama yang akan tumbuh mengisi ruang --}}
                    <main class="flex-grow p-8">
                        @yield('content')
                    </main>

                    {{-- Footer yang akan menempel di bawah --}}
                    <footer class="text-center p-4 text-gray-500 text-sm border-t bg-white z-10 flex-shrink-0">
                        <p>
                            Copyright &copy;
                            <script>document.write(new Date().getFullYear())</script> All rights reserved | Ditenagai
                            oleh
                            <a href="{{ route('tim.developer') }}" target="_blank"
                                class="text-red-600 hover:underline font-medium">Tim E-Commerce Garuda</a>
                            by <a href="https://garudacyber.co.id" target="_blank"
                                class="text-red-600 hover:underline font-medium">PT. Garuda Cyber Indonesia</a>.
                        </p>
                    </footer>
                </div>

                {{-- Gambar Dekoratif diposisikan absolut di lapisan bawah --}}
                <div class="absolute bottom-0 right-0 pointer-events-none z-0" aria-hidden="true">
                    <img src="{{ asset('images/bg-nav.png') }}" alt="Dekoratif"
                        class="w-64 h-64 lg:w-96 lg:h-96 opacity-75">
                </div>

            </div>

        </div>
    </div>

    @include('layouts._partials.scripts')

    {!! showAlert() !!}
    {!! deactivateConfirmScript() !!}
    {!! reactivateConfirmScript() !!}
    @stack('scripts')
</body>

</html>