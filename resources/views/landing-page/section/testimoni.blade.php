<!-- Testimoni Section -->
<section id="testimoni" class="py-16 md:py-24 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12 max-w-2xl mx-auto">
            <h2 class="text-5xl font-bold text-gray-900">Testimoni</h2>
            <p class="mt-4 text-gray-600">Lihat pengalaman nyata pengguna layanan kami!</p>
        </div>

        <div class="relative">
            {{-- Pastikan ada testimoni sebelum menampilkan carousel --}}
            @if($testimonials->isNotEmpty())
                <div id="testimoni-carousel" class="flex overflow-x-auto space-x-8 pb-8 no-scrollbar scroll-smooth">
                    
                    @foreach ($testimonials as $testimonial)
                        {{-- PERUBAIKAN: Menambahkan min-h-[360px] untuk tinggi yang seragam --}}
                        <div class="flex-shrink-0 w-80 min-h-[360px] border-2 border-yellow-400 rounded-2xl p-6 bg-white shadow-lg flex flex-col">
                            <p class="text-gray-600 mb-6 flex-grow">"{{ $testimonial->content }}"</p>
                            
                            <div class="flex justify-center mb-4 text-yellow-400">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $testimonial->rating)
                                        <span>★</span>
                                    @else
                                        <span class="text-gray-300">★</span>
                                    @endif
                                @endfor
                            </div>
                            
                            <img src="https://placehold.co/48x48/EBF4FF/76A9FA?text={{ strtoupper(substr($testimonial->name, 0, 1)) }}" alt="Foto {{ $testimonial->name }}"
                                class="w-12 h-12 rounded-full mx-auto mb-2 object-cover">
                            <h4 class="font-bold text-center text-gray-800">{{ $testimonial->name }}</h4>
                        </div>
                    @endforeach

                </div>

                @if($testimonials->count() > 3)
                <div class="flex justify-center mt-8 space-x-4">
                    <button id="testimoni-prev-btn"
                        class="w-12 h-12 flex items-center justify-center border-2 border-red-300 rounded-lg hover:bg-red-50 transition-colors">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <button id="testimoni-next-btn"
                        class="w-12 h-12 flex items-center justify-center border-2 border-yellow-400 rounded-lg hover:bg-yellow-50 transition-colors">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
                @endif
                
            @else
                {{-- Tampilan jika tidak ada testimoni --}}
                <div class="text-center text-gray-500 py-8">
                    <p>Belum ada testimoni untuk ditampilkan saat ini.</p>
                </div>
            @endif
        </div>
    </div>
</section>

{{-- Pastikan ini berada di dalam layout yang sama atau di-include --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const carousel = document.getElementById('testimoni-carousel');
        const prevBtn = document.getElementById('testimoni-prev-btn');
        const nextBtn = document.getElementById('testimoni-next-btn');

        if (carousel && prevBtn && nextBtn) {
            const scrollAmount = 320 + 32;

            nextBtn.addEventListener('click', () => {
                carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            });

            prevBtn.addEventListener('click', () => {
                carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            });
        }
    });
</script>
@endpush
