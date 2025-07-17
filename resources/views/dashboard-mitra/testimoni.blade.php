{{-- resources/views/mitra/dashboard/testimoni.blade.php --}}
@extends('layouts.mitra')

@section('title', 'Ulasan & Testimoni Pelanggan')

@section('content')
    {{-- Container utama, menggantikan .card --}}
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        
        {{-- Header, menggantikan .card-header --}}
        <div class="px-6 py-4 border-b border-gray-200">
            <h4 class="text-lg font-semibold text-gray-800">Ulasan & Testimoni untuk Produk Anda</h4>
        </div>

        {{-- Body, menggantikan .card-body --}}
        <div class="p-6">
            {{-- Wrapper untuk membuat tabel responsif --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left text-gray-700">
                    <thead class="bg-gray-50 text-xs text-gray-700 uppercase">
                        <tr>
                            <th scope="col" class="px-6 py-3">Produk</th>
                            <th scope="col" class="px-6 py-3">Nama Pelanggan</th>
                            <th scope="col" class="px-6 py-3">Rating</th>
                            <th scope="col" class="px-6 py-3">Ulasan</th>
                            <th scope="col" class="px-6 py-3">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($testimonials as $testimonial)
                            {{-- Efek 'striped' dengan odd/even background --}}
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $testimonial->product->name ?? 'Produk Dihapus' }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $testimonial->name }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        @for ($i = 0; $i < $testimonial->rating; $i++)
                                            {{-- Mengganti text-warning dengan text-yellow-400 --}}
                                            <i class="fas fa-star text-yellow-400"></i>
                                        @endfor
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    {{ $testimonial->content }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $testimonial->created_at->format('d M Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr class="bg-white border-b">
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    Belum ada testimoni untuk produk Anda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Link Pagination dengan margin atas --}}
            <div class="mt-6">
                {{ $testimonials->links() }}
            </div>
        </div>
    </div>
@endsection