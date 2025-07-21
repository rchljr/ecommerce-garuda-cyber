<section class="bg-gray-50 py-20">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 tracking-tight">Jelajahi Toko Mitra Kami</h2>
            <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">Temukan berbagai produk unik dan berkualitas dari
                para mitra kreatif yang telah bergabung dengan kami.</p>
        </div>

        @if(isset($partners) && $partners->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($partners as $partner)
                    @include('layouts._partials.partner-card', ['partner' => $partner])
                @endforeach
            </div>
        @else
            <div class="text-center py-10 border-2 border-dashed rounded-lg">
                <p class="text-gray-500">Belum ada mitra yang bergabung. Jadilah yang pertama!</p>
            </div>
        @endif

        @if(isset($partners) && $partners->isNotEmpty())
            <div class="text-center mt-16">
                <a href="{{ route('tenants.index') }}"
                    class="inline-block bg-gray-800 text-white font-semibold py-3 px-8 rounded-lg hover:bg-black transition-colors duration-300">
                    Lihat Semua Mitra
                </a>
            </div>
        @endif
    </div>
</section>