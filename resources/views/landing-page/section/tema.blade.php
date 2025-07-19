<section id="tema" class="py-16 md:py-24">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h3 class="text-3xl md:text-5xl font-bold text-gray-900">Pilihan Tampilan Profesional Untuk Bisnis Anda</h3>
            <p class="mt-4 text-lg text-gray-600">Ini adalah koleksi tema yang tersedia. Pilih salah satu untuk memulai kustomisasi dan meluncurkan toko online Anda.</p>
        </div>
        <div class="border-2 border-red-300 rounded-3xl p-4 md:p-8">
            <div class="relative">
                <div id="tema-carousel-container" class="overflow-hidden">
                    <div id="tema-carousel" class="flex space-x-6 pb-4 overflow-x-auto no-scrollbar scroll-smooth">

                        @forelse($templates as $template)
                            <div class="tema-card flex-shrink-0 w-4/5 sm:w-1/2 md:w-[calc(33.333%-1rem)] bg-white rounded-2xl shadow-md p-4 text-center flex flex-col relative overflow-hidden">
                                
                                <!-- Image and Link -->
                                <a href="{{ $template->status === 'active' ? route('template.preview', $template) : '#' }}" 
                                target="_blank" 
                                   class="block {{ $template->status !== 'active' ? 'pointer-events-none' : '' }}">
                                    <img src="{{ asset('storage/' . $template->image_preview) }}"
                                         onerror="this.onerror=null;this.src='https://placehold.co/400x300/f1f5f9/cbd5e1?text=No+Image';"
                                         alt="{{ $template->name }}"
                                         class="w-full h-40 object-cover rounded-lg mb-4 {{ $template->status === 'active' ? 'hover:opacity-80' : 'filter grayscale brightness-75' }} transition-all">
                                </a>
                                
                                <!-- Content -->
                                <div class="flex flex-col flex-grow text-left">
                                    <h4 class="font-semibold text-gray-800">{{ $template->name }}</h4>
                                    <p class="text-gray-600 mt-2 text-sm flex-grow">
                                        {{ Str::limit($template->description, 85) }}</p>
                                </div>

                                <!-- Coming Soon Overlay -->
                                @if($template->status !== 'active')
                                    <div class="absolute inset-0 bg-black/40 flex flex-col items-center justify-center text-white p-4 rounded-2xl">
                                        <svg class="w-10 h-10 mb-2 opacity-80" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <h4 class="text-lg font-bold tracking-wide">SEGERA HADIR</h4>
                                        <p class="text-xs text-gray-200 mt-1">Desain baru sedang kami siapkan!</p>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-center w-full text-gray-500">Tema akan segera tersedia.</p>
                        @endforelse

                    </div>
                </div>
            </div>
            <div id="tema-nav-buttons" class="flex justify-center mt-8 space-x-4">
                <button id="tema-prev-btn" class="w-10 h-10 flex items-center justify-center border border-red-300 rounded-full hover:bg-red-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <button id="tema-next-btn" class="w-10 h-10 flex items-center justify-center border border-yellow-400 bg-white rounded-full hover:bg-yellow-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</section>

{{-- Pastikan script ini ada di dalam @push('scripts') di view utama Anda --}}
<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
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
                prevBtn.disabled = carousel.scrollLeft < 10;
                const maxScrollLeft = carousel.scrollWidth - carousel.clientWidth;
                nextBtn.disabled = carousel.scrollLeft >= (maxScrollLeft - 10);
            } else {
                navButtons.style.display = 'none';
                carousel.style.justifyContent = 'center';
            }
        }

        nextBtn.addEventListener('click', () => {
            const scrollAmount = container.clientWidth * 0.8;
            carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        });

        prevBtn.addEventListener('click', () => {
            const scrollAmount = container.clientWidth * 0.8;
            carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        });

        carousel.addEventListener('scroll', () => {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(updateNavButtons, 150);
        });

        window.addEventListener('resize', updateNavButtons);
        updateNavButtons();
    });
</script>
