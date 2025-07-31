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
            <div class="mt-6">
                {{ $vouchers->links() }}
            </div>
        </div>
    </div>
@endsection
