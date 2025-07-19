<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tim Pengembang - E-commerce Garuda</title>
    <meta name="keywords" content="ecommerce, garuda cyber, toko online, produk, jasa">
    <meta name="description" content="E-Commerce Garuda Cyber: Temukan berbagai produk dan layanan terbaik.">
    <meta name="author" content="Garuda Cyber">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/gci.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .gradient-bg {
            background-color: #f8fafc;
            background-image:
                radial-gradient(at 0% 0%, hsla(212, 45%, 96%, 1) 0px, transparent 50%),
                radial-gradient(at 98% 1%, hsla(212, 45%, 96%, 1) 0px, transparent 50%);
        }

        .card {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #e2e8f0;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.15);
        }
    </style>
</head>

<body class="gradient-bg text-gray-800">

    <!-- Container Utama -->
    <div class="container mx-auto px-4 py-16 sm:py-24">

        <!-- Header Halaman -->
        <header class="text-center mb-16">
            <h1 class="text-4xl sm:text-5xl font-bold tracking-tight text-gray-900">Mengenal Tim Kami</h1>
            <p class="mt-4 max-w-2xl mx-auto text-lg text-gray-600">Kami adalah duo developer di balik platform
                E-commerce Garuda, berkolaborasi untuk menciptakan pengalaman terbaik bagi mitra dan pelanggan.</p>
        </header>

        <!-- Grid Profil Tim -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 lg:gap-16 max-w-5xl mx-auto">

            <!-- Profil 1: Rachel Jeflisa -->
            <div class="card flex flex-col items-center text-center p-8 lg:p-12">
                <img class="w-32 h-32 rounded-full object-cover mb-6 border-4 border-white shadow-lg"
                    src="{{ asset('images/rachel.jpg') }}"
                    onerror="this.onerror=null; this.src='https://placehold.co/200x200/E2E8F0/4A5568?text=RJ'"
                    alt="Foto profil Rachel Jeflisa">

                <h2 class="text-2xl font-bold text-gray-900">Rachel Jeflisa Rahmawati</h2>
                <p class="text-indigo-600 font-medium mt-1">Full-Stack Developer</p>

                <div class="w-full border-t my-8 border-gray-200"></div>

                <h3 class="text-lg font-semibold text-gray-800 self-start">Fokus Pengembangan:</h3>
                <ul class="mt-4 space-y-3 text-left text-gray-600">
                    <li class="flex items-start">
                        <i class="fas fa-shield-alt w-5 h-5 text-indigo-500 mr-3 mt-1"></i>
                        <span>Manajemen Admin PT Garuda Cyber Indonesia & Sistem Utama</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-pager w-5 h-5 text-indigo-500 mr-3 mt-1"></i>
                        <span>Pengembangan Landing Page & Antarmuka Publik</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-shopping-cart w-5 h-5 text-indigo-500 mr-3 mt-1"></i>
                        <span>Alur Proses & Pengalaman Sisi Pelanggan</span>
                    </li>
                </ul>

                <div class="mt-auto pt-8 flex space-x-5 text-xl text-gray-400">
                    <a href="https://www.linkedin.com/in/racheljeflisa" target="_blank"
                        class="hover:text-indigo-600 transition-colors"><i class="fab fa-linkedin"></i></a>
                    <a href="https://github.com/rchljr" target="_blank"
                        class="hover:text-indigo-600 transition-colors"><i class="fab fa-github"></i></a>
                    <a href="https://www.instagram.com/rchljr" target="_blank"
                        class="hover:text-indigo-600 transition-colors"><i class="fab fa-instagram"></i></a>
                </div>
            </div>

            <!-- Profil 2: Hilmi Ramadhan -->
            <div class="card flex flex-col items-center text-center p-8 lg:p-12">
                <img class="w-32 h-32 rounded-full object-cover mb-6 border-4 border-white shadow-lg"
                    src="{{ asset('images/hilmi.jpg') }}"
                    onerror="this.onerror=null; this.src='https://placehold.co/200x200/E2E8F0/4A5568?text=HR'"
                    alt="Foto profil Hilmi Ramadhan">

                <h2 class="text-2xl font-bold text-gray-900">Hilmi Ramadhan</h2>
                <p class="text-teal-600 font-medium mt-1">Full-Stack Developer</p>

                <div class="w-full border-t my-8 border-gray-200"></div>

                <h3 class="text-lg font-semibold text-gray-800 self-start">Fokus Pengembangan:</h3>
                <ul class="mt-4 space-y-3 text-left text-gray-600">
                    <li class="flex items-start">
                        <i class="fas fa-tachometer-alt w-5 h-5 text-teal-500 mr-3 mt-1"></i>
                        <span>Sistem & Fungsionalitas Dashboard Mitra UMKM</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-store w-5 h-5 text-teal-500 mr-3 mt-1"></i>
                        <span>Manajemen Tema & Kustomisasi Toko Mitra UMKM</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-palette w-5 h-5 text-teal-500 mr-3 mt-1"></i>
                        <span>Desain & Interaktivitas Seluruh Tema Toko</span>
                    </li>
                </ul>

                <div class="mt-auto pt-8 flex space-x-5 text-xl text-gray-400">
                    <a href="#" class="hover:text-teal-600 transition-colors"><i class="fab fa-linkedin"></i></a>
                    <a href="#" class="hover:text-teal-600 transition-colors"><i class="fab fa-github"></i></a>
                    <a href="#" class="hover:text-teal-600 transition-colors"><i class="fab fa-instagram"></i></a>
                </div>
            </div>

        </div>

        <!-- Footer Halaman -->
        <footer class="text-center mt-20 border-t border-gray-200 pt-8">
            <p class="text-gray-500">
                Copyright &copy;
                <script>document.write(new Date().getFullYear())</script> All rights reserved | Ditenagai oleh
                <a href="{{ route('tim.developer') }}" target="_blank"
                    class="text-indigo-600 hover:underline font-medium">Tim E-Commerce
                    Garuda</a>
                by <a href="https://garudacyber.co.id" target="_blank"
                    class="text-indigo-600 hover:underline font-medium">PT. Garuda Cyber Indonesia</a>.
            </p>
        </footer>

    </div>

</body>

</html>