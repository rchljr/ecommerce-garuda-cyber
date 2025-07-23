@extends('layouts.mitra')

@section('title', 'Detail Produk: ' . $product->name)

@section('content')
<div class="bg-white shadow-xl rounded-xl p-6 sm:p-8 md:p-10 max-w-screen-xl w-full mx-auto">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900">Detail Produk</h1>
        <a href="{{ route('mitra.products.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Produk
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-6">
            <strong class="font-bold">Sukses!</strong> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6">
            <strong class="font-bold">Error!</strong> {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        {{-- Kolom Kiri: Gambar Produk --}}
        <div class="md:col-span-1">
            <div class="bg-gray-100 rounded-lg overflow-hidden border border-gray-200">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-auto object-cover">
            </div>
            @if($product->gallery_image_paths && count($product->gallery_image_paths) > 0)
                <div class="mt-4 grid grid-cols-3 gap-2">
                    @foreach($product->gallery_image_paths as $path)
                        <div class="bg-gray-100 rounded-lg overflow-hidden border border-gray-200 aspect-w-1 aspect-h-1">
                            <img src="{{ asset('storage/' . $path) }}" alt="Galeri {{ $product->name }}" class="w-full h-full object-cover">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Kolom Tengah: Informasi Produk --}}
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $product->name }}</h2>
                <div class="text-gray-700 space-y-2">
                    <p><strong>Sub Kategori:</strong> {{ $product->subCategory->name ?? 'N/A' }}</p>
                    <p><strong>SKU:</strong> {{ $product->sku ?? 'N/A' }}</p>
                    <p><strong>Status:</strong> <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ ucfirst($product->status) }}</span></p>
                    <p><strong>Deskripsi Singkat:</strong> {{ $product->short_description ?? 'Tidak ada deskripsi singkat.' }}</p>
                    <div>
                        <strong>Deskripsi Lengkap:</strong>
                        <div class="text-gray-600 trix-content">
                            {!! $product->description ?? 'Tidak ada deskripsi lengkap.' !!}
                        </div>
                    </div>
                    @if($product->tags->isNotEmpty())
                        <p><strong>Tags:</strong>
                            @foreach($product->tags as $tag)
                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1">{{ $tag->name }}</span>
                            @endforeach
                        </p>
                    @endif
                    <p><strong>Tanggal Dibuat:</strong> {{ $product->created_at->format('d M Y H:i') }}</p>
                    <p><strong>Terakhir Diperbarui:</strong> {{ $product->updated_at->format('d M Y H:i') }}</p>
                </div>
            </div>

            {{-- Informasi Varian --}}
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Daftar Varian</h2>
                {{-- KUNCI PERBAIKAN DI SINI: Gunakan $product->varians --}}
                @if ($product->varians->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Opsi Varian</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Modal</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profit (%)</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Jual</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gambar</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($product->varians as $varian)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $varian->name }}
                                            @if($varian->options_data)
                                                <br><span class="text-xs text-gray-500">({{ collect($varian->options_data)->pluck('name')->implode(': ') }} - {{ collect($varian->options_data)->pluck('value')->implode(' / ') }})</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ format_rupiah($varian->modal_price) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $varian->profit_percentage }}%</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">{{ format_rupiah($varian->selling_price) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $varian->stock }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($varian->image_path)
                                                <img src="{{ Storage::url($varian->image_path) }}" alt="Varian Gambar" class="w-16 h-16 object-cover rounded-md">
                                            @else
                                                Tidak Ada
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-600">Produk ini tidak memiliki varian.</p>
                @endif
            </div>

            {{-- Aksi --}}
            <div class="mt-6 bg-white p-6 rounded-lg shadow flex justify-end gap-3">
                <a href="{{ route('mitra.products.edit', $product->id) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md inline-flex items-center">
                    <i class="fas fa-edit mr-2"></i> Edit Produk
                </a>
                <form action="{{ route('mitra.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini? Semua varian dan data terkait akan dihapus.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-md inline-flex items-center">
                        <i class="fas fa-trash-alt mr-2"></i> Hapus Produk
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection