{{-- resources/views/landing-page/partials/_themes.blade.php --}}

<section id="tema" class="py-16 md:py-24">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h3 class="text-3xl md:text-5xl font-bold text-gray-900">Tema Toko Online Anda</h3>
            <p class="mt-4 text-lg text-gray-600">Pilih beragam desain profesional yang siap pakai untuk bisnis
                Anda.</p>
        </div>
        <div class="border-2 border-red-300 rounded-3xl p-4 md:p-8">
            <div class="relative">
                <div id="tema-carousel-container" class="overflow-hidden">
                    <div id="tema-carousel" class="flex space-x-6 pb-4 overflow-x-auto no-scrollbar scroll-smooth">

                        @forelse($templates as $template)
                            <div
                                class="tema-card flex-shrink-0 w-4/5 sm:w-1/2 md:w-[calc(33.333%-1rem)] bg-white rounded-2xl shadow-md p-4 text-center flex flex-col">
                                <a href="{{ route('template.preview', $template) }}" target="_blank" class="block">
                                    <img src="{{ asset('storage/' . $template->image_preview) }}"
                                        onerror="this.onerror=null;this.src='https://placehold.co/400x300/f1f5f9/cbd5e1?text=No+Image';"
                                        alt="{{ $template->name }}"
                                        class="w-full h-40 object-cover rounded-lg mb-4 hover:opacity-80 transition-opacity">
                                </a>
                                <div class="flex flex-col flex-grow text-left">
                                    <h4 class="font-semibold text-gray-800">{{ $template->name }}</h4>
                                    <p class="text-gray-600 mt-2 text-sm flex-grow">
                                        {{ Str::limit($template->description, 85) }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center w-full text-gray-500">Tema akan segera tersedia.</p>
                        @endforelse

                    </div>
                </div>
            </div>
            <div id="tema-nav-buttons" class="flex justify-center mt-8 space-x-4">
                <button id="tema-prev-btn"
                    class="w-10 h-10 flex items-center justify-center border border-red-300 rounded-full hover:bg-red-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                </button>
                <button id="tema-next-btn"
                    class="w-10 h-10 flex items-center justify-center border border-yellow-400 bg-white rounded-full hover:bg-yellow-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</section>

{{-- Pastikan script ini ada di dalam @push('scripts') di view utama Anda --}}
<style>
    /* Menyembunyikan scrollbar di berbagai browser */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .no-scrollbar {
        -ms-overflow-style: none;
        /* IE and Edge */
        scrollbar-width: none;
        /* Firefox */
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('tema-carousel-container');
        const carousel = document.getElementById('tema-carousel');
        const prevBtn = document.getElementById('tema-prev-btn');
        const nextBtn = document.getElementById('tema-next-btn');
        const navButtons = document.getElementById('tema-nav-buttons');

        if (!carousel || !prevBtn || !nextBtn || !container) return;

        let scrollTimeout;

        function updateNavButtons() {
            const isScrollable = carousel.scrollWidth > carousel.clientWidth;

            if (isScrollable) {
                navButtons.style.display = 'flex';
                // Nonaktifkan tombol 'prev' jika sudah di paling kiri
                prevBtn.disabled = carousel.scrollLeft < 10;
                // Nonaktifkan tombol 'next' jika sudah di paling kanan
                const maxScrollLeft = carousel.scrollWidth - carousel.clientWidth;
                nextBtn.disabled = carousel.scrollLeft >= (maxScrollLeft - 10);
            } else {
                // Jika semua item sudah terlihat, sembunyikan tombol navigasi
                navButtons.style.display = 'none';
                carousel.style.justifyContent = 'center'; // Pusatkan item jika tidak bisa di-scroll
            }
        }

        nextBtn.addEventListener('click', () => {
            // Gulir sejauh 80% dari lebar kontainer yang terlihat
            const scrollAmount = container.clientWidth * 0.8;
            carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        });

        prevBtn.addEventListener('click', () => {
            const scrollAmount = container.clientWidth * 0.8;
            carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        });

        // Update status tombol setelah scroll selesai
        carousel.addEventListener('scroll', () => {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(updateNavButtons, 150);
        });

        // Panggil saat pertama kali dimuat dan saat ukuran window berubah
        window.addEventListener('resize', updateNavButtons);
        updateNavButtons();
    });
</script>