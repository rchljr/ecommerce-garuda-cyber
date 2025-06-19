<!DOCTYPE html>
<html lang="id">

<head>
    @include('layouts._partials.head')
</head>

<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Kolom Kiri (Sidebar Merah) -->
        <div class="hidden lg:flex w-1/3 text-white flex-col justify-between relative"
            style="background-color: #B20000;">
            <!-- Gambar Latar Belakang -->
            <img src="{{ asset('images/bg-auth.png') }}" alt="Background Auth"
                class="absolute bottom-20 left-0 w-auto h-auto object-cover z-0">

            <!-- Konten Sidebar -->
            <div class="relative z-10 p-12 flex flex-col justify-between h-full">
                <div>
                    <div class="flex items-center gap-3 mb-12">
                        <img src="{{ asset('images/GCI.png') }}" alt="Logo E-Commerce GCI" class="w-12 h-12">
                        <h1 class="font-bold text-2xl">E-COMMERCE GCI</h1>
                    </div>

                    <h2 class="font-bold text-4xl leading-tight mb-4">Selamat Datang !</h2>
                    <p class="text-white/100">
                        Masuk ke akun Anda untuk melanjutkan pengelolaan bisnis dengan solusi digital yang memudahkan
                        setiap proses operasional dan monitoring secara efisien.
                    </p>
                </div>

                <div class="p-6 rounded-[19px] backdrop-blur-sm mb-10" style="background-color: #8C0B0B;">
                    <h3 class="font-bold mt-2 mb-6">Solusi UMKM Yang Praktis Dan Ekonomis!</h3>
                    <p class="text-sm text-white/80 mb-4">
                        Dengan Smart Bisnis, saya bisa membuat website toko online tanpa harus memiliki pengetahuan
                        teknis. Biayanya juga sangat terjangkau untuk UMKM. Dukungan pelanggannya cepat dan ramah, saya
                        sangat puas dengan layanan ini.
                    </p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div
                                class="w-10 h-10 rounded-full flex items-center justify-center border-2 border-white/50 mr-3">
                                <svg class="w-6 h-6 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <p class="font-semibold">Amanda Lauren</p>
                        </div>
                        <div class="flex text-yellow-400">★★★★☆</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan (Konten Utama) -->
        <div class="w-full lg:w-2/3 bg-white flex flex-col">
            <main class="flex-grow overflow-y-auto p-8 md:p-10 lg:p-12">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>

</html>