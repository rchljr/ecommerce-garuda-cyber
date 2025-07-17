@extends('layouts.mitra')

@section('title', 'Kelola Voucher')

@section('content')
    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h4 class="text-lg font-semibold text-gray-800">Daftar Voucher Anda</h4>
            <a href="{{ route('mitra.vouchers.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Buat Voucher Baru
            </a>
        </div>

        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left text-gray-700">
                    <thead class="bg-gray-50 text-xs text-gray-700 uppercase">
                        <tr>
                            <th scope="col" class="px-6 py-3">Nama Voucher</th>
                            <th scope="col" class="px-6 py-3">Kode</th>
                            <th scope="col" class="px-6 py-3">Tipe</th>
                            <th scope="col" class="px-6 py-3">Nilai</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Berlaku Sampai</th>
                            <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($vouchers as $voucher)
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $voucher->name }}</td>
                                <td class="px-6 py-4 font-mono text-indigo-600">{{ $voucher->code }}</td>
                                <td class="px-6 py-4">{{ $voucher->type === 'percentage' ? 'Persen' : 'Tetap' }}</td>
                                <td class="px-6 py-4">{{ $voucher->type === 'percentage' ? $voucher->value . '%' : 'Rp ' . number_format($voucher->value) }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $voucher->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($voucher->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">{{ \Carbon\Carbon::parse($voucher->valid_until)->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('mitra.vouchers.edit', $voucher->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                                    <form action="{{ route('mitra.vouchers.destroy', $voucher->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus voucher ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-gray-500">Anda belum membuat voucher.</td>
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
