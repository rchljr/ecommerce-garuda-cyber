@extends('layouts.mitra')
@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Daftar Pesanan</h1>
            {{-- Tempat untuk tombol seperti "Tambah Order Baru" --}}
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3">ID Pesanan</th>
                            <th scope="col" class="px-6 py-3">Pembeli</th>
                            <th scope="col" class="px-6 py-3">Detail Item</th>
                            <th scope="col" class="px-6 py-3 text-center">Status</th>
                            <th scope="col" class="px-6 py-3 text-right">Total</th>
                            <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    <span class="font-mono">{{ Str::limit($order->id, 8) }}</span>
                                    <div class="text-xs text-gray-500 font-normal">{{ $order->order_date->diffForHumans() }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">{{ $order->user->name }}</td>
                                <td class="px-6 py-4">
                                    <ul class="space-y-1">
                                        @foreach ($order->items as $item)
                                            <li class="text-xs">{{ $item->product->name ?? 'N/A' }} <span
                                                    class="font-semibold">({{ $item->quantity }} pcs)</span></li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $statusClasses = '';
                                        switch (strtolower($order->status)) {
                                            case 'pending':
                                                $statusClasses = 'bg-yellow-100 text-yellow-800';
                                                break;
                                            case 'paid':
                                                $statusClasses = 'bg-green-100 text-green-800';
                                                break;
                                            case 'shipped':
                                                $statusClasses = 'bg-blue-100 text-blue-800';
                                                break;
                                            case 'completed':
                                                $statusClasses = 'bg-indigo-100 text-indigo-800';
                                                break;
                                            case 'canceled':
                                                $statusClasses = 'bg-red-100 text-red-800';
                                                break;
                                            default:
                                                $statusClasses = 'bg-gray-100 text-gray-800';
                                        }
                                    @endphp
                                    <span
                                        class="px-3 py-1 text-xs font-semibold leading-tight rounded-full {{ $statusClasses }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-semibold text-gray-900">
                                    Rp{{ number_format($order->total_price) }}</td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('orders.show', $order->id) }}"
                                        class="font-medium text-indigo-600 hover:text-indigo-900">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12">
                                    <div class="text-gray-400">
                                        <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="mt-2 text-sm font-semibold">Belum ada pesanan</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 bg-white border-t border-gray-200">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
@endsection
