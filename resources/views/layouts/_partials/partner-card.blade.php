@php
    $shop = $partner->shop;
    $subdomain = $partner->subdomain;
    $vouchers = $partner->activeVouchers;
    // PERBAIKAN: Mengambil hero pertama melalui relasi shop yang benar
    // Catatan: Untuk performa terbaik, pastikan relasi 'shop.heroes' di-eager load di controller/service.
    // Contoh: ->with(['shop.heroes', 'subdomain', 'activeVouchers'])
    $firstHero = optional($partner->shop)->heroes()->orderBy('order')->first();
    $bannerUrl = $firstHero ? $firstHero->image_url : asset('storage/' . $shop->shop_banner);
@endphp

@if($shop && $subdomain)
    <div
        class="group bg-white rounded-xl shadow-md transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 flex flex-col">
        <a href="{{ route('tenant.home', ['subdomain' => $subdomain->subdomain_name]) }}" class="flex flex-col h-full">
            <div class="relative">
                {{-- Menggunakan gambar hero sebagai background --}}
                <div class="h-40 bg-gray-200 rounded-t-xl overflow-hidden">
                    <img src="{{ $bannerUrl }}"
                        onerror="this.onerror=null;this.src='https://placehold.co/600x400/f1f5f9/cbd5e1?text={{ urlencode($shop->shop_name) }}';"
                        alt="Banner {{ $shop->shop_name }}"
                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                </div>
                <div class="absolute bottom-0 left-5 transform translate-y-1/2">
                    <img src="{{ asset('storage/' . $shop->shop_logo) }}"
                        onerror="this.onerror=null;this.src='https://placehold.co/80x80/ef4444/ffffff?text={{ substr($shop->shop_name, 0, 1) }}';"
                        alt="Logo {{ $shop->shop_name }}"
                        class="w-20 h-20 rounded-full object-cover border-4 border-white shadow-lg transition-transform duration-300 group-hover:rotate-6">
                </div>
            </div>
            <div class="p-5 pt-12 flex flex-col flex-grow">
                <h3 class="font-bold text-lg text-gray-900 truncate" title="{{ $shop->shop_name }}">{{ $shop->shop_name }}
                </h3>
                <p class="text-sm text-gray-500 mt-1 h-10 overflow-hidden">
                    {{ $shop->shop_tagline ?: 'Menyediakan produk terbaik untuk Anda.' }}
                </p>
                @if($vouchers->isNotEmpty())
                    <div class="mt-4 space-y-2">
                        @foreach($vouchers->take(2) as $voucher)
                            <div class="voucher-strip border-red-400 border-2 rounded-lg p-2 flex items-center gap-2 bg-red-50">
                                <svg class="w-6 h-6 text-red-500 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                                </svg>
                                <div class="text-xs">
                                    <p class="font-semibold text-red-700">Diskon {{ (int) $voucher->discount }}%</p>
                                    <p class="text-red-600">Min. belanja {{ format_rupiah($voucher->min_spending) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="mt-4 h-16"></div>
                @endif
                <div class="mt-auto pt-4 text-center">
                    <span
                        class="kunjungi-badge inline-block bg-red-600 text-white text-sm font-bold px-6 py-2 rounded-full group-hover:bg-red-700 transition-all duration-300 ease-in-out">
                        Kunjungi Toko
                    </span>
                </div>
            </div>
        </a>
    </div>
@endif