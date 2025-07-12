@extends('layouts.admin')
@section('title', 'Kelola Landing Page')

@section('content')
    {{-- Tampilkan alert dari helper --}}
    {!! session('success') ? showAlert('success', session('success')) : '' !!}
    {!! session('error') ? showAlert('error', session('error')) : '' !!}

    <div class="flex-shrink-0 flex justify-between items-center mb-6">
        <div>
            <h1 class="text-4xl font-bold text-gray-800">Kelola Statistik Landing Page</h1>
            <p class="text-lg text-gray-500 mt-2">Edit data statistik yang tampil di halaman depan</p>
        </div>
    </div>
    <div class="max-w-4xl">
        <div class="bg-white rounded-lg shadow border border-gray-200 overflow-x-auto">
            <table class="w-full whitespace-no-wrap">
                <thead class="bg-gray-200">
                    <tr class="text-left font-semibold text-sm uppercase text-gray-700 tracking-wider">
                        <th class="px-6 py-3 border-b-2 border-gray-300">Nama Statistik</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300">Nilai</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="stats-table-body" class="divide-y divide-gray-200">
                    @php
                        $statsMap = [
                            'Total Pengguna' => ['value' => $stats->total_users ?? 0, 'key' => 'total_users'],
                            'Total Toko' => ['value' => $stats->total_shops ?? 0, 'key' => 'total_shops'],
                            'Total Pengunjung' => ['value' => $stats->total_visitors ?? 0, 'key' => 'total_visitors'],
                            'Total Transaksi' => ['value' => $stats->total_transactions ?? 0, 'key' => 'total_transactions'],
                        ];
                    @endphp
                    @foreach ($statsMap as $name => $data)
                        <tr class="text-gray-700">
                            <td class="px-6 py-4">{{ $name }}</td>
                            <td class="px-6 py-4 font-bold">{{ number_format($data['value']) }}</td>
                            <td class="px-6 py-4 text-center">
                                <button class="edit-stat-btn p-1 text-gray-600 hover:text-blue-600"
                                    data-stat-key="{{ $data['key'] }}" data-stat-name="{{ $name }}"
                                    data-stat-value="{{ $data['value'] }}" title="Edit">
                                    <img src="{{ asset('images/edit.png') }}" alt="Edit" class="w-6 h-6">
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- Modal Edit Nilai Statistik -->
    <div id="edit-stat-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="text-2xl font-bold" id="modal-title">Edit Nilai Statistik</h3>
                <button id="close-stat-modal-btn" class="text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
            </div>
            <form id="edit-stat-form" action="{{ route('admin.landing-page.statistics.update') }}" method="POST"
                class="mt-4 space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" id="stat_key" name="stat_key">
                <div>
                    <label for="stat_value" class="block text-base font-semibold text-gray-800 mb-1" id="stat-label">Nilai
                        Baru</label>
                    <input type="number" id="stat_value" name="stat_value"
                        class="block w-full h-12 px-4 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:ring-0 transition"
                        required min="0">
                </div>
                <div class="flex justify-end pt-4 gap-3 border-t mt-4">
                    <button type="button" id="cancel-stat-modal-btn"
                        class="px-6 py-2 rounded-lg text-gray-700 bg-gray-200 hover:bg-gray-300">Batal</button>
                    <button type="submit"
                        class="px-6 py-2 rounded-lg text-white bg-red-700 hover:bg-red-800">Simpan</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const editModal = document.getElementById('edit-stat-modal');
            const closeModalBtn = document.getElementById('close-stat-modal-btn');
            const cancelModalBtn = document.getElementById('cancel-stat-modal-btn');
            const modalTitle = document.getElementById('modal-title');
            const statLabel = document.getElementById('stat-label');
            const statKeyInput = document.getElementById('stat_key');
            const statValueInput = document.getElementById('stat_value');
            const tableBody = document.getElementById('stats-table-body');

            const openModal = (statKey, statName, statValue) => {
                modalTitle.textContent = `Edit ${statName}`;
                statLabel.textContent = `Nilai Baru untuk ${statName}`;
                statKeyInput.value = statKey;
                statValueInput.value = statValue;
                editModal.classList.remove('hidden');
            };

            const closeModal = () => {
                editModal.classList.add('hidden');
            };

            if (tableBody) {
                tableBody.addEventListener('click', (e) => {
                    const button = e.target.closest('.edit-stat-btn');
                    if (button) {
                        e.preventDefault();
                        const statKey = button.dataset.statKey;
                        const statName = button.dataset.statName;
                        const statValue = button.dataset.statValue;
                        openModal(statKey, statName, statValue);
                    }
                });
            }

            closeModalBtn.addEventListener('click', closeModal);
            cancelModalBtn.addEventListener('click', closeModal);
        });
    </script>
@endpush