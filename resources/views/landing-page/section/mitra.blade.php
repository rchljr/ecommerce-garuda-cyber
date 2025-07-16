<section class="bg-gray-50 py-20">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 tracking-tight">Jelajahi Toko Mitra Kami</h2>
            <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">Temukan berbagai produk unik dan berkualitas dari
                para mitra kreatif yang telah bergabung dengan kami.</p>
        </div>

        @if(isset($partners) && $partners->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @foreach($partners as $partner)
                    @php
                        $shop = $partner->shop;
                        $subdomain = $partner->subdomain;
                    @endphp
                    @if($shop && $subdomain)
                        <a href="{{ route('tenant.home', ['subdomain' => $subdomain->subdomain_name]) }}"
                            class="group block bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 ease-in-out transform hover:-translate-y-2 overflow-hidden">

                            <!-- Banner Toko -->
                            <div class="h-48 w-full overflow-hidden relative">
                                <img src="{{ asset('storage/' . $shop->shop_banner) }}"
                                    onerror="this.onerror=null;this.src='https://placehold.co/600x400/f1f5f9/cbd5e1?text={{ urlencode($shop->shop_name) }}';"
                                    alt="Banner {{ $shop->shop_name }}"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                            </div>

                            <!-- Konten Kartu -->
                            <div class="p-6 relative">
                                <!-- Logo Toko -->
                                <div class="absolute -top-10 left-1/2 -translate-x-1/2">
                                    <img src="{{ asset('storage/' . $shop->shop_logo) }}"
                                        onerror="this.onerror=null;this.src='https://placehold.co/80x80/ef4444/ffffff?text={{ substr($shop->shop_name, 0, 1) }}';"
                                        alt="Logo {{ $shop->shop_name }}"
                                        class="w-20 h-20 rounded-full object-cover border-4 border-white bg-white shadow-md">
                                </div>

                                <!-- Detail Toko -->
                                <div class="pt-12 text-center">
                                    <h3 class="text-xl font-bold text-gray-900 truncate" title="{{ $shop->shop_name }}">
                                        {{ $shop->shop_name }}</h3>
                                    <p class="text-sm text-gray-500 mt-2 h-10 overflow-hidden">
                                        {{ $shop->shop_tagline ?: 'Menyediakan produk terbaik untuk Anda.' }}
                                    </p>
                                </div>

                                <!-- Tombol Aksi -->
                                <div class="mt-6">
                                    <span
                                        class="block w-full bg-red-500 text-white text-center font-semibold py-3 rounded-lg group-hover:bg-red-600 transition-colors duration-300">
                                        Kunjungi Toko
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endif
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