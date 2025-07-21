@php
    // Relasi yang dibutuhkan sudah di-eager load dari controller
    $shop = optional($product->shopOwner)->shop;
    $subdomain = optional($product->shopOwner)->subdomain;
@endphp

@if($shop && $subdomain)
    <div class="bg-white rounded-lg shadow-md overflow-hidden group transition-all duration-300 hover:shadow-xl">
        {{-- Link sekarang mengarah ke halaman /shop di toko mitra --}}
        <a href="{{ route('tenant.shop', ['subdomain' => $subdomain->subdomain_name]) }}" class="block">
            <div class="h-48 bg-gray-200 overflow-hidden">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                    onerror="this.onerror=null;this.src='https://placehold.co/600x400/f1f5f9/cbd5e1?text={{ urlencode($product->name) }}';"
                    alt="Banner {{ $product->name }}"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
            </div>
            <div class="p-4">
                <p class="text-xs text-gray-500">{{ $shop->shop_name }}</p>
                <h4 class="font-semibold text-gray-800 truncate mt-1" title="{{ $product->name }}">{{ $product->name }}</h4>
                <p class="font-bold text-red-600 mt-2">{{ format_rupiah($product->price) }}</p>
            </div>
        </a>
    </div>
@endif