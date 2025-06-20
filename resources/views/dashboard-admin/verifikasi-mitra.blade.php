
@extends('layouts.admin')
@section('title', 'Verifikasi Mitra')
@section('content')
    <div class="flex flex-col h-full">
        <div class="flex-shrink-0 flex justify-between items-center mb-6">
            <div>
                <h1 class="text-4xl font-bold text-gray-800">Verifikasi Mitra</h1>
                <p class="text-lg text-gray-500 mt-6">Daftar Pengajuan Mitra</p>
            </div>
            {{-- Tombol Cari bisa diimplementasikan di sini jika perlu --}}
        </div>
        <div class="flex-grow overflow-auto bg-white rounded-lg shadow border border-gray-200">
            <table class="w-full whitespace-no-wrap min-w-[1200px]">
                <thead class="bg-gray-200">
                    <tr class="text-center font-semibold text-sm uppercase text-gray-700 tracking-wider">
                        <th class="px-6 py-3 border-b-2 border-gray-300">Nama Toko</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300">Pemilik</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300">Kategori</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300">Alamat</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300">Tahun Berdiri</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300">Dokumen</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300">Status</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($pendingPartners as $partner)
                        <tr class="text-gray-700 text-center">
                            <td class="px-6 py-4">
                                <div class="font-bold text-black">{{ optional($partner->shop)->shop_name }}</div>
                                <a href="http://{{ optional($partner->subdomain)->subdomain_name }}.garuda.id" target="_blank" class="text-sm text-blue-600 underline">{{ optional($partner->subdomain)->subdomain_name }}.garuda.id</a>
                            </td>
                            <td class="px-6 py-4 text-left">
                                <div class="font-semibold">{{ $partner->name }}</div>
                                <div class="text-sm text-gray-500">{{ $partner->email }}</div>
                                <div class="text-sm text-gray-500">{{ $partner->phone }}</div>
                            </td>
                            <td class="px-6 py-4">{{ optional($partner->shop)->product_categories }}</td>
                            <td class="px-6 py-4 max-w-xs whitespace-normal">{{ optional($partner->shop)->shop_address }}</td>
                            <td class="px-6 py-4">{{ format_tanggal(optional($partner->shop)->year_founded, 'Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1 text-left">
                                    @if(optional($partner->shop)->shop_photo) <a href="{{ Storage::url($partner->shop->shop_photo) }}" target="_blank" class="text-sm text-blue-600 underline hover:text-blue-800">Foto Usaha</a> @endif
                                    @if(optional($partner->shop)->ktp) <a href="{{ Storage::url($partner->shop->ktp) }}" target="_blank" class="text-sm text-blue-600 underline hover:text-blue-800">KTP</a> @endif
                                    @if(optional($partner->shop)->sku) <a href="{{ Storage::url($partner->shop->sku) }}" target="_blank" class="text-sm text-blue-600 underline hover:text-blue-800">SKU</a> @endif
                                    
                                    @if(optional($partner->shop)->npwp) <a href="{{ Storage::url($partner->shop->npwp) }}" target="_blank" class="text-sm text-blue-600 underline hover:text-blue-800">NPWP</a> @endif
                                    @if(optional($partner->shop)->nib) <a href="{{ Storage::url($partner->shop->nib) }}" target="_blank" class="text-sm text-blue-600 underline hover:text-blue-800">NIB</a> @endif
                                    @if(optional($partner->shop)->iumk) <a href="{{ Storage::url($partner->shop->iumk) }}" target="_blank" class="text-sm text-blue-600 underline hover:text-blue-800">IUMK</a> @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-700">Menunggu Verifikasi</span>
                            </td>
                            <td class="px-6 py-4 flex justify-center gap-2">
                                <form action="{{ route('admin.mitra.approve', $partner) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button class="p-2 hover:bg-green-100 rounded-full" title="Setujui" type="submit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                </form>
                                <form action="{{ route('admin.mitra.reject', $partner) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button class="delete-confirm p-2 hover:bg-red-100 rounded-full" title="Tolak" type="submit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-gray-500 py-10">Tidak ada pengajuan mitra baru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
