{{-- resources/views/homepage.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di Toko Online Kami</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .carousel-item {
            display: none;
            transition: opacity 0.5s ease-in-out;
            opacity: 0;
            width: 100%;
            height: 100%;
            /* Pastikan gambar memenuhi ruang container, bukan item itu sendiri */
            /* object-fit: cover; */
        }
        .carousel-item.active {
            display: block;
            opacity: 1;
        }
        .carousel-item-container {
            position: relative;
            width: 100%;
            padding-top: 40%; /* Rasio aspek 16:9 (9/16 * 100%) untuk responsive, atau fixed height */
            overflow: hidden;
            background-color: #f0f0f0; /* Default background jika gambar belum load */
        }
        .carousel-item-container img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(70%); /* Sedikit meredupkan gambar agar teks lebih menonjol */
        }
        .slide-content {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 100%;
            padding: 0 1rem;
            color: white; /* Default text color, akan di-override oleh slide->text_color */
            z-index: 10; /* Pastikan konten di atas gambar */
        }
        .slide-content.left { text-align: left; left: 5%; transform: translateY(-50%); width: auto; max-width: 50%;} /* Sesuaikan lebar agar tidak terlalu panjang */
        .slide-content.center { text-align: center; left: 50%; transform: translate(-50%, -50%); }
        .slide-content.right { text-align: right; right: 5%; transform: translateY(-50%); width: auto; max-width: 50%;} /* Sesuaikan lebar agar tidak terlalu panjang */

        /* Tambahan: Pastikan teks di dalam slide-content terlihat jelas */
        .slide-content h2,
        .slide-content p {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7); /* Memberi bayangan agar teks menonjol */
        }
    </style>
</head>
<body class="font-sans bg-gray-100 text-gray-800">

    <nav class="bg-blue-600 p-4 text-white shadow-md">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <a href="{{ route('homepage') }}" class="text-2xl font-bold">Toko Online Anda</a>
            <div>
                <a href="#" class="mr-4 hover:text-blue-200">Produk</a>
                <a href="#" class="mr-4 hover:text-blue-200">Tentang Kami</a>
                <a href="#" class="hover:text-blue-200">Kontak</a>
                @auth
                    <a href="{{ route('dashboard-mitra.index') }}" class="ml-4 bg-white text-blue-600 px-3 py-1 rounded hover:bg-gray-100">Dashboard</a>
                @else
                    <a href="/login" class="ml-4 bg-white text-blue-600 px-3 py-1 rounded hover:bg-gray-100">Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <section class="relative w-full max-w-full mx-auto shadow-lg mt-0">
        @if($slides->isEmpty())
            <div class="bg-gray-200 h-64 flex items-center justify-center">
                <p class="text-gray-600 text-xl">Tidak ada slide yang aktif.</p>
            </div>
        @else
            <div id="slide-carousel" class="carousel-item-container">
                @foreach ($slides as $index => $slide)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <img src="{{ Storage::url($slide->image_path) }}" alt="{{ $slide->title }}">
                        <div class="slide-content {{ $slide->text_position }}">
                           <h2 class="text-5xl font-bold mb-2" style="color: {{ $slide->text_color ?? 'white' }} !important;">{{ $slide->title }}</h2>
                           <p class="text-xl mb-4" style="color: {{ $slide->text_color ?? 'white' }} !important;">{{ $slide->content }}</p>
                            @if($slide->button_text && $slide->button_url)
                                <a href="{{ $slide->button_url }}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-full text-lg shadow-lg transition duration-300">
                                    {{ $slide->button_text }}
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Navigasi Carousel --}}
            <button class="absolute top-1/2 left-4 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-3 rounded-full hover:bg-opacity-75 focus:outline-none z-20" onclick="changeSlide(-1)">❮</button>
            <button class="absolute top-1/2 right-4 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-3 rounded-full hover:bg-opacity-75 focus:outline-none z-20" onclick="changeSlide(1)">❯</button>
        @endif
    </section>

    <main class="max-w-7xl mx-auto p-8 mt-8 bg-white rounded-lg shadow">
        <h1 class="text-4xl font-bold mb-6 text-center">Produk Unggulan</h1>
        <p class="text-center text-lg text-gray-700">Temukan penawaran terbaik kami hari ini!</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
            <div class="bg-gray-50 p-6 rounded-lg shadow-sm text-center">
                <h3 class="text-xl font-semibold mb-2">Produk A</h3>
                <p class="text-gray-600">Deskripsi singkat produk A.</p>
                <button class="mt-4 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Lihat Detail</button>
            </div>
            <div class="bg-gray-50 p-6 rounded-lg shadow-sm text-center">
                <h3 class="text-xl font-semibold mb-2">Produk B</h3>
                <p class="text-gray-600">Deskripsi singkat produk B.</p>
                <button class="mt-4 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Lihat Detail</button>
            </div>
            <div class="bg-gray-50 p-6 rounded-lg shadow-sm text-center">
                <h3 class="text-xl font-semibold mb-2">Produk C</h3>
                <p class="text-gray-600">Deskripsi singkat produk C.</p>
                <button class="mt-4 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Lihat Detail</button>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white p-6 text-center text-sm mt-12">
        <p>&copy; 2025 Toko Online Anda. Semua hak dilindungi.</p>
    </footer>

    <script>
        let currentSlide = 0;
        const slides = document.querySelectorAll('.carousel-item');
        const totalSlides = slides.length;

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.remove('active');
                if (i === index) {
                    slide.classList.add('active');
                }
            });
        }

        function changeSlide(direction) {
            currentSlide += direction;
            if (currentSlide >= totalSlides) {
                currentSlide = 0;
            } else if (currentSlide < 0) {
                currentSlide = totalSlides - 1;
            }
            showSlide(currentSlide);
        }

        // Auto-play (opsional)
        let slideInterval = setInterval(() => changeSlide(1), 5000); // Ganti slide setiap 5 detik

        // Berhenti auto-play saat hover, lanjutkan saat tidak hover
        const carousel = document.getElementById('slide-carousel');
        if (carousel) {
            carousel.addEventListener('mouseenter', () => clearInterval(slideInterval));
            carousel.addEventListener('mouseleave', () => slideInterval = setInterval(() => changeSlide(1), 5000));
        }

        // Inisialisasi slide pertama saat DOM dimuat
        document.addEventListener('DOMContentLoaded', () => {
            if (totalSlides > 0) {
                showSlide(currentSlide);
            }
        });
    </script>
</body>
</html>