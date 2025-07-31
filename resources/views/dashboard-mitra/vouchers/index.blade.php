@extends('layouts.mitra')

@section('title', 'Kelola Voucher')

@section('content')
    {{-- Notifikasi Sukses --}}
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    {{-- Notifikasi Error dari Middleware (jika ada) --}}
    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h4 class="text-lg font-semibold text-gray-800">Daftar Voucher Anda</h4>
            <a href="{{ route('mitra.vouchers.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-plus mr-2"></i>Buat Voucher Baru
            </a>
        </div>

        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left text-gray-700">
                    <thead class="bg-gray-50 text-xs text-gray-700 uppercase">
                        <tr>
                            {{-- UBAH: Kolom 'Nama Voucher' sekarang menampilkan 'description' --}}
                            <th scope="col" class="px-6 py-3">Deskripsi Voucher</th>
                            {{-- UBAH: Kolom 'Kode' sekarang menampilkan 'voucher_code' --}}
                            <th scope="col" class="px-6 py-3">Kode</th>
                            {{-- UBAH: Kolom 'Diskon' sekarang menampilkan 'discount' dengan format yang benar --}}
                            <th scope="col" class="px-6 py-3">Diskon</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            {{-- UBAH: Kolom 'Berlaku Sampai' sekarang menampilkan 'expired_date' --}}
                            <th scope="col" class="px-6 py-3">Berakhir Pada</th>
                            <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($vouchers as $voucher)
                            <tr class="bg-white border-b hover:bg-gray-50">
                                {{-- UBAH: Tampilkan deskripsi, jika kosong tampilkan kode voucher --}}
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $voucher->description ?? 'Tidak ada deskripsi' }}</td>
                                
                                {{-- UBAH: Tampilkan kode voucher --}}
                                <td class="px-6 py-4 font-mono text-indigo-600">{{ $voucher->voucher_code }}</td>
                                
                                {{-- UBAH: Tampilkan diskon berdasarkan tipe voucher --}}
                                <td class="px-6 py-4">
                                    @if ($voucher->type == 'percentage')
                                        {{ $voucher->discount }}%
                                    @elseif ($voucher->type == 'fixed_amount')
                                        Rp {{ number_format($voucher->discount, 0, ',', '.') }}
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4">
                                    {{-- Asumsi model Voucher punya accessor 'status' (active, expired, scheduled) --}}
                                    @php
                                        $status = $voucher->status; // 'active', 'expired', 'scheduled'
                                        $statusClass = '';
                                        if ($status === 'active') {
                                            $statusClass = 'bg-green-100 text-green-800';
                                        } elseif ($status === 'expired') {
                                            $statusClass = 'bg-red-100 text-red-800';
                                        } else {
                                            $statusClass = 'bg-yellow-100 text-yellow-800';
                                        }
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>

                                {{-- UBAH: Tampilkan tanggal berakhir --}}
                                <td class="px-6 py-4">{{ \Carbon\Carbon::parse($voucher->expired_date)->format('d M Y') }}</td>
                                
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('mitra.vouchers.edit', $voucher->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-4 font-medium">Edit</a>
                                    <form action="{{ route('mitra.vouchers.destroy', $voucher->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus voucher ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-gray-500">Anda belum membuat voucher.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4">
                <h2 class="text-xl font-semibold text-gray-800">Daftar Voucher Anda</h2>
                <a href="{{ route('mitra.vouchers.create') }}"
                    class="w-full sm:w-auto text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-300 flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Buat Voucher Baru</span>
                </a>
            </div>

            <div class="p-4 sm:p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left text-gray-700">
                        <thead class="bg-gray-50 text-xs text-gray-600 uppercase tracking-wider">
                            <tr>
                                <th scope="col" class="px-6 py-3">Kode Voucher</th>
                                <th scope="col" class="px-6 py-3">Deskripsi</th>
                                <th scope="col" class="px-6 py-3">Min. Belanja</th>
                                <th scope="col" class="px-6 py-3">Periode Aktif</th>
                                <th scope="col" class="px-6 py-3 text-center">Diskon</th>
                                <th scope="col" class="px-6 py-3 text-center">Pelanggan Baru?</th>
                                <th scope="col" class="px-6 py-3 text-center">Satu Kali/Pelanggan?</th>
                                <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($vouchers as $voucher)
                                <tr class="bg-white hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 font-mono text-indigo-600 font-semibold whitespace-nowrap">
                                        {{ $voucher->voucher_code }}</td>
                                    <td class="px-6 py-4 text-gray-600 max-w-xs truncate" title="{{ $voucher->description }}">
                                        {{ Str::limit($voucher->description, 50) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ format_rupiah($voucher->min_spending) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span>Dari:
                                                {{ \Carbon\Carbon::parse($voucher->start_date)->format('d M Y') }}</span>
                                            <span class="text-xs text-gray-500">Sampai:
                                                {{ \Carbon\Carbon::parse($voucher->expired_date)->format('d M Y') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-green-600">{{ $voucher->discount }}%</td>
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $voucher->is_for_new_customer ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700' }}">
                                            {{ $voucher->is_for_new_customer ? 'Ya' : 'Tidak' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $voucher->max_uses_per_customer == 1 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700' }}">
                                            {{ $voucher->max_uses_per_customer == 1 ? 'Ya' : 'Tidak' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <a href="{{ route('mitra.vouchers.edit', $voucher->id) }}"
                                            class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</a>
                                        <span class="text-gray-300 mx-1">|</span>
                                        <form action="{{ route('mitra.vouchers.destroy', $voucher->id) }}" method="POST"
                                            class="inline-block"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus voucher ini? Aksi ini tidak dapat dibatalkan.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-900 font-medium">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-10 text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                            </svg>
                                            <p class="mt-2 font-semibold">Anda belum membuat voucher.</p>
                                            <p class="text-sm">Klik tombol "Buat Voucher Baru" untuk memulai.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">
                    {{ $vouchers->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection