@extends('layouts.mitra')

@section('title', 'Kelola Voucher')

@push('styles')
{{-- Menambahkan Font Awesome untuk ikon --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
@endpush

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Notifikasi --}}
    {{-- @if (session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 text-green-700 p-4 mb-6 rounded-md shadow-sm" role="alert">
            <div class="flex">
                <div class="py-1"><i class="fas fa-check-circle fa-lg mr-3 text-green-500"></i></div>
                <div>
                    <p class="font-bold">Sukses</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif --}}

    @if (session('error'))
        <div class="bg-red-50 border-l-4 border-red-400 text-red-700 p-4 mb-6 rounded-md shadow-sm" role="alert">
            <div class="flex">
                <div class="py-1"><i class="fas fa-exclamation-circle fa-lg mr-3 text-red-500"></i></div>
                <div>
                    <p class="font-bold">Terjadi Kesalahan</p>
                    <p class="text-sm">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Card Utama --}}
    <div class="bg-white shadow-lg rounded-xl border border-gray-200">
        {{-- Header Card --}}
        <div class="px-6 py-5 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Daftar Voucher</h2>
                <p class="text-sm text-gray-500 mt-1">Kelola semua voucher promosi untuk toko Anda di sini.</p>
            </div>
            <a href="{{ route('mitra.vouchers.create') }}"
               class="inline-flex items-center justify-center gap-2 w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition-all duration-300 ease-in-out">
                <i class="fas fa-plus"></i>
                <span>Buat Voucher Baru</span>
            </a>
        </div>

        {{-- Body Card (Table) --}}
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left text-gray-700">
                <thead class="bg-gray-50 text-xs text-gray-600 uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-semibold">Kode & Deskripsi</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Tipe & Diskon</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Status</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Periode Aktif</th>
                        <th scope="col" class="px-6 py-3 font-semibold text-center">Penggunaan</th>
                        <th scope="col" class="px-6 py-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($vouchers as $voucher)
                        <tr class="bg-white hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-mono text-indigo-700 font-bold">{{ $voucher->voucher_code }}</div>
                                <div class="text-xs text-gray-500 mt-1 max-w-xs truncate" title="{{ $voucher->description }}">
                                    {{ $voucher->description ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-semibold text-gray-800">
                                    @if ($voucher->type == 'percentage')
                                        {{ $voucher->discount }}%
                                    @elseif ($voucher->type == 'fixed_amount')
                                        {{ format_rupiah($voucher->discount) }}
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500 mt-1">Min. Belanja: {{ format_rupiah($voucher->min_spending) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $status = $voucher->status;
                                    $statusClass = '';
                                    if ($status === 'active') {
                                        $statusClass = 'bg-green-100 text-green-800';
                                    } elseif ($status === 'expired') {
                                        $statusClass = 'bg-red-100 text-red-800';
                                    } else {
                                        $statusClass = 'bg-yellow-100 text-yellow-800';
                                    }
                                @endphp
                                <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-gray-800">{{ \Carbon\Carbon::parse($voucher->start_date)->format('d M Y') }}</div>
                                <div class="text-xs text-gray-500">hingga {{ \Carbon\Carbon::parse($voucher->expired_date)->format('d M Y') }}</div>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <div class="text-gray-800">{{ $voucher->max_uses_per_customer == 1 ? '1x' : 'Berkali-kali' }} / Pelanggan</div>
                                <div class="text-xs text-gray-500">{{ $voucher->is_for_new_customer ? 'Hanya Pengguna Baru' : 'Semua Pengguna' }}</div>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('mitra.vouchers.edit', $voucher->id) }}" class="text-indigo-600 hover:text-indigo-800 font-medium mr-4">Edit</a>
                                <form action="{{ route('mitra.vouchers.destroy', $voucher->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus voucher ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-16 text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-ticket-alt fa-3x text-gray-300"></i>
                                    <p class="mt-4 font-semibold">Anda belum membuat voucher.</p>
                                    <p class="text-sm mt-1">Klik tombol "Buat Voucher Baru" untuk memulai.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer Card (Pagination) --}}
        @if ($vouchers->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $vouchers->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
