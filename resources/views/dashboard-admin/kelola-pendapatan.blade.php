@extends('layouts.admin')
@section('title', 'Kelola Pendapatan')
@section('content')
    <div class="flex-shrink-0 flex justify-between items-center mb-6">
        <div>
            <h1 class="text-4xl font-bold text-gray-800">Kelola Pendapatan</h1>
            <p class="text-lg text-gray-500 mt-6">Daftar semua transaksi pembayaran yang tercatat.</p>
        </div>
        <div class="flex items-center gap-4">
            {{-- Form Pencarian --}}
            <div class="relative">
                <form action="{{ route('admin.pendapatan.index') }}" method="GET">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                        class="block w-64 pl-10 pr-4 py-2 h-12 border-2 border-gray-300 rounded-lg bg-white focus:border-red-600 focus:ring-0 transition"
                        placeholder="Cari nama, paket, tanggal...">
                </form>
            </div>

            {{-- Tombol Export --}}
            <a href="{{ route('admin.pendapatan.export') }}"
                class="bg-red-700 text-white font-semibold px-4 py-2 h-12 rounded-lg hover:bg-red-800 flex items-center gap-2 flex-shrink-0">
                <img src="{{ asset('images/export.svg') }}" alt="Export" class="w-5 h-5">
                <span>Export Excel</span>
            </a>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow border border-gray-200 overflow-x-auto">
        <table class="w-full whitespace-no-wrap min-w-[900px]">
            <thead class="bg-gray-200">
                <tr class="text-center font-semibold text-sm uppercase text-gray-700 tracking-wider">
                    <th class="px-6 py-3 border-b-2 border-gray-300">Tanggal</th>
                    <th class="px-6 py-3 border-b-2 border-gray-300">Nama Mitra</th>
                    <th class="px-6 py-3 border-b-2 border-gray-300">Paket</th>
                    <th class="px-6 py-3 border-b-2 border-gray-300">Jenis Tagihan</th>
                    <th class="px-6 py-3 border-b-2 border-gray-300">Jumlah</th>
                    <th class="px-6 py-3 border-b-2 border-gray-300">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($payments as $payment)
                    <tr class="text-gray-700 text-center">
                        <td class="px-6 py-4">
                            {{ format_tanggal($payment->created_at) }}
                        </td>
                        <td class="px-6 py-4">
                            {{ optional($payment->user)->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4">
                            {{ optional($payment->subscriptionPackage)->package_name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $planType = optional($payment->user->userPackage)->plan_type;
                            @endphp
                            @if($planType == 'yearly')
                                Tahunan
                            @elseif($planType == 'monthly')
                                Bulanan
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            {{ format_rupiah($payment->total_payment) }}
                        </td>
                        <td class="px-6 py-4">
                            @if(in_array($payment->midtrans_transaction_status, ['settlement', 'capture']))
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-700">Lunas</span>
                            @else
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-700">Belum
                                    Lunas</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            Belum ada data pendapatan yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{-- Link Paginasi --}}
    <div class="mt-4">
        {{ $payments->links() }}
    </div>
@endsection