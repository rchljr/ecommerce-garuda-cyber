@extends('layouts.mitra')

@section('title', 'Detail Produk: ' . $product->name)

@section('content')
<div class="bg-white shadow-lg rounded-lg p-6 mb-8 max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">{{ $product->name }}</h1>
        <div class="space-x-2">
            <a href="{{ route('mitra.products.edit', $product->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                Edit Produk
            </a>
            <form action="{{ route('mitra.products.destroy', $product->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini dan semua variannya?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                    Hapus Produk
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
        <div>
            <h2 class="text-xl font-semibold text-gray-700 mb-2">Informasi Produk:</h2>
            @if ($product->thumbnail_url)
                <img src="{{ $product->thumbnail_url }}" alt="Thumbnail Produk {{ $product->name }}" class="w-full max-h-60 object-contain rounded-lg mb-4 border border-gray-200 shadow-sm">
            @else
                <div class="w-full max-h-60 bg-gray-200 rounded-lg mb-4 flex items-center justify-center text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            @endif
            <p class="text-gray-600"><strong>Kategori:</strong> {{ $product->category->name ?? '-' }}</p>
            <p class="text-gray-600"><strong>Dibuat Oleh:</strong> {{ $product->user->name ?? '-' }}</p>
            <p class="text-gray-600"><strong>Harga:</strong> Rp {{ number_format($product->price, 0, ',', '.') }}</p>
            <p class="text-gray-600"><strong>Stok:</strong> {{ $product->stock }}</p>
            <p class="text-gray-600"><strong>Diskon:</strong> {{ $product->product_discount ?? 0 }}%</p>
            <p class="text-gray-600"><strong>SKU:</strong> {{ $product->sku ?? '-' }}</p>
            <p class="text-gray-600"><strong>Status:</strong> <span class="px-3 py-1 rounded-full text-xs font-semibold
                {{ $product->status == 'published' ? 'bg-green-200 text-green-800' : 'bg-gray-200 text-gray-800' }}">
                {{ ucfirst($product->status) }}
            </span></p>
            <p class="text-gray-600"><strong>Dibuat:</strong> {{ $product->created_at->format('d M Y H:i') }}</p>
            <p class="text-gray-600"><strong>Diperbarui:</strong> {{ $product->updated_at->format('d M Y H:i') }}</p>
        </div>
        <div>
            <h2 class="text-xl font-semibold text-gray-700 mb-2">Deskripsi Produk:</h2>
            <div class="text-gray-600 trix-content"> {{-- Tambahkan kelas trix-content untuk styling Trix output --}}
                {!! $product->description ?? 'Tidak ada deskripsi.' !!} {{-- Menggunakan {!! !!} untuk render HTML dari Trix --}}
            </div>

            <h2 class="text-xl font-semibold text-gray-700 mt-6 mb-2">Opsi Varian:</h2>
            @if ($product->productOptions->isNotEmpty())
                <ul class="list-disc list-inside text-gray-600 pl-4">
                    @foreach ($product->productOptions as $option)
                        <li>
                            <strong>{{ $option->name }}:</strong>
                            @if ($option->optionValues->isNotEmpty())
                                {{ $option->optionValues->pluck('value')->implode(', ') }}
                            @else
                                -
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-600">Tidak ada opsi varian yang ditentukan.</p>
            @endif
        </div>
    </div>
</div>
@endsection
