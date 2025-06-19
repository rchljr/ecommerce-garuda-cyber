@extends('layouts.admin')
@section('title', 'Kelola Voucher')

@section('content')
    <div class="flex flex-col h-full">
        <!-- Header -->
        <div class="flex-shrink-0 flex justify-between items-center mb-6">
            <div>
                <h1 class="text-4xl font-bold text-gray-800">Kelola Voucher</h1>
                <p class="text-lg text-gray-500 mt-6">Daftar-Daftar Voucher</p>
            </div>
            <div class="flex items-center gap-4">
                <button class="p-2 rounded-full hover:bg-gray-100" title="Cari">
                    <svg width="20" height="20" fill="none">
                        <path d="M19 19l-4.35-4.35M9 16a7 7 0 1 0 0-14 7 7 0 0 0 0 14Z" stroke="#232323" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
                <button id="add-voucher-btn"
                    class="bg-red-700 text-white font-semibold px-4 py-2 rounded-lg hover:bg-red-800 flex items-center gap-2 transition-colors">
                    <img src="{{ asset('images/tambah-db.png') }}" alt="Tambah" class="w-5 h-5">
                    <span>Tambah Voucher</span>
                </button>
            </div>
        </div>

        <!-- Tabel Voucher -->
        <div class="flex-grow overflow-auto bg-white rounded-lg shadow border border-gray-200">
            <table class="w-full whitespace-no-wrap min-w-[1200px]">
                <thead class="bg-gray-200">
                    <tr class="text-center font-semibold text-sm uppercase text-gray-700 tracking-wider">
                        <th class="px-6 py-3 border-b-2 border-gray-300">Kode Voucher</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300">Deskripsi</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300">Nominal Diskon</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300">Minimal Transaksi</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300">Berlaku Dari</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300">Berlaku Sampai</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300">Status</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300">Aksi</th>
                    </tr>
                </thead>
                <tbody id="vouchers-table-body" data-fetch-url="{{ route('admin.voucher.json', ['id' => 'VOUCHER_ID']) }}"
                    class="divide-y divide-gray-200">
                    @forelse($vouchers as $voucher)
                        <tr class="text-gray-700 text-center">
                            <td class="px-6 py-4 font-bold">{{ strtoupper($voucher->voucher_code) }}</td>
                            <td class="px-6 py-4 max-w-xs whitespace-normal">{{ $voucher->description }}</td>
                            <td class="px-6 py-4 font-semibold">{{ format_diskon($voucher->discount) }}</td>
                            <td class="px-6 py-4">{{ format_rupiah($voucher->min_spending) }}</td>
                            <td class="px-6 py-4">{{ format_tanggal($voucher->start_date) }}</td>
                            <td class="px-6 py-4">{{ format_tanggal($voucher->expired_date) }}</td>
                            <td class="px-6 py-4">
                                @if (now()->between($voucher->start_date, $voucher->expired_date))
                                    <span
                                        class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-700">Aktif</span>
                                @else
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-700">Tidak
                                        Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 flex justify-center gap-3">
                                <button class="edit-btn p-1 text-gray-600 hover:text-blue-600" title="Edit"
                                    data-id="{{ $voucher->id }}">
                                    <img src="{{ asset('images/edit.png') }}" alt="Edit" class="w-6 h-6">
                                </button>
                                <form action="{{ route('admin.voucher.destroy', $voucher->id) }}" method="POST"
                                    class="inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-gray-600 hover:text-red-600 delete-confirm"
                                        title="Delete">
                                        <img src="{{ asset('images/delete.png') }}" alt="Delete" class="w-6 h-6">
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-gray-500 py-8">Belum ada voucher yang tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah/Edit Voucher -->
    <div id="voucher-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 id="modal-title-voucher" class="text-2xl font-bold">Tambah Voucher Baru</h3>
                <button id="close-voucher-modal-btn" class="text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
            </div>
            <form id="voucher-form" method="POST" class="mt-4 space-y-4" action="{{ route('admin.voucher.store') }}"
                data-update-url="{{ route('admin.voucher.update', ['id' => 'VOUCHER_ID']) }}">
                @csrf
                {{-- Method PUT akan disisipkan oleh JS --}}
                <div>
                    <label for="voucher_code" class="block text-base font-semibold text-gray-800 mb-1">Kode Voucher<span
                            class="text-red-600">*</span></label>
                    <input type="text" id="voucher_code" name="voucher_code"
                        class="block w-full h-12 px-4 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:ring-0 transition"
                        required>
                </div>
                <div>
                    <label for="description" class="block text-base font-semibold text-gray-800 mb-1">Deskripsi</label>
                    <textarea id="description" name="description"
                        class="block w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:ring-0 transition"
                        rows="2"></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="discount" class="block text-base font-semibold text-gray-800 mb-1">Nominal Diskon
                            (Rp)<span class="text-red-600">*</span></label>
                        <input type="number" id="discount" name="discount"
                            class="block w-full h-12 px-4 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:ring-0 transition"
                            required min="0">
                    </div>
                    <div>
                        <label for="min_spending" class="block text-base font-semibold text-gray-800 mb-1">Minimal Transaksi
                            (Rp)</label>
                        <input type="number" id="min_spending" name="min_spending"
                            class="block w-full h-12 px-4 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:ring-0 transition"
                            min="0" value="0">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-base font-semibold text-gray-800 mb-1">Berlaku Dari<span
                                class="text-red-600">*</span></label>
                        <input type="date" id="start_date" name="start_date"
                            class="block w-full h-12 px-4 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:ring-0 transition"
                            required>
                    </div>
                    <div>
                        <label for="expired_date" class="block text-base font-semibold text-gray-800 mb-1">Berlaku
                            Sampai<span class="text-red-600">*</span></label>
                        <input type="date" id="expired_date" name="expired_date"
                            class="block w-full h-12 px-4 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:ring-0 transition"
                            required>
                    </div>
                </div>
                <div class="flex justify-end pt-4 gap-3 border-t mt-4">
                    <button type="button" id="cancel-voucher-modal-btn"
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
            const modal = document.getElementById('voucher-modal');
            const addBtn = document.getElementById('add-voucher-btn');
            const closeModalBtn = document.getElementById('close-voucher-modal-btn');
            const cancelModalBtn = document.getElementById('cancel-voucher-modal-btn');
            const modalTitle = document.getElementById('modal-title-voucher');
            const voucherForm = document.getElementById('voucher-form');
            const tableBody = document.getElementById('vouchers-table-body');
            let methodInput = null;

            const openModal = (title, data = null) => {
                voucherForm.reset();
                modalTitle.textContent = title;
                if (methodInput) {
                    methodInput.remove();
                    methodInput = null;
                }

                if (data) { // Edit Mode
                    const updateUrlTemplate = voucherForm.dataset.updateUrl;
                    voucherForm.action = updateUrlTemplate.replace('VOUCHER_ID', data.id);

                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'PUT';
                    voucherForm.prepend(methodInput);

                    document.getElementById('voucher_code').value = data.voucher_code || '';
                    document.getElementById('description').value = data.description || '';
                    document.getElementById('discount').value = data.discount || '';
                    document.getElementById('min_spending').value = data.min_spending || 0;
                    document.getElementById('start_date').value = data.start_date || '';
                    document.getElementById('expired_date').value = data.expired_date || '';

                } else { // Create Mode
                    voucherForm.action = "{{ route('admin.voucher.store') }}";
                }

                modal.classList.remove('hidden');
            };

            const closeModal = () => modal.classList.add('hidden');

            addBtn.addEventListener('click', () => openModal('Tambah Voucher Baru'));
            closeModalBtn.addEventListener('click', closeModal);
            cancelModalBtn.addEventListener('click', closeModal);

            if (tableBody) {
                tableBody.addEventListener('click', (e) => {
                    const editBtn = e.target.closest('.edit-btn');
                    if (!editBtn) return;

                    e.preventDefault();
                    const voucherId = editBtn.dataset.id;
                    const urlTemplate = tableBody.dataset.fetchUrl;
                    const url = urlTemplate.replace('VOUCHER_ID', voucherId);

                    fetch(url)
                        .then(res => {
                            if (!res.ok) throw new Error(`Gagal memuat data. Status: ${res.status}`);
                            return res.json();
                        })
                        .then(data => {
                            if (data && data.id) {
                                openModal('Edit Voucher', data);
                            } else {
                                throw new Error('Format data yang diterima tidak valid.');
                            }
                        })
                        .catch(err => {
                            console.error('Fetch error:', err);
                            alert(err.message);
                        });
                });
            }
        });
    </script>
@endpush