@extends('layouts.admin')
@section('title', 'Kelola Paket Berlangganan')

@section('content')
    <div class="flex-shrink-0 flex justify-between items-center mb-6">
        <div>
            <h1 class="text-4xl font-bold text-gray-800">Kelola Paket Berlangganan</h1>
            <p class="text-lg text-gray-500 mt-6">Daftar-Daftar Paket Berlangganan</p>
        </div>
        <div class="flex items-center gap-4">
            <button id="add-package-btn"
                class="bg-red-700 text-white font-semibold px-4 py-2 rounded-lg hover:bg-red-800 flex items-center gap-2 transition-colors">
                <img src="{{ asset('images/tambah-db.png') }}" alt="Tambah" class="w-5 h-5">
                <span>Tambah Paket</span>
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow border border-gray-200 overflow-x-auto">
        <table class="w-full whitespace-no-wrap min-w-[1200px]">
            <thead class="bg-gray-200">
                <tr class="text-left font-semibold text-sm uppercase text-gray-700 tracking-wider">
                    <th class="px-6 py-3 border-b-2 border-gray-300">Nama Paket</th>
                    <th class="px-6 py-3 border-b-2 border-gray-300">Fitur</th>
                    <th class="px-6 py-3 border-b-2 border-gray-300">Deskripsi</th>
                    <th class="px-6 py-3 border-b-2 border-gray-300">Harga</th>
                    <th class="px-6 py-3 border-b-2 border-gray-300">Diskon Tahunan</th>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="packages-table-body" data-fetch-url="{{ route('admin.paket.showJson', ['id' => 'PACKAGE_ID']) }}"
                class="divide-y divide-gray-200">
                @forelse($packages as $package)
                    <tr class="text-gray-700 text-left align-top">
                        <td class="px-6 py-4 font-bold text-base">{{ $package->package_name }}</td>
                        <td class="px-6 py-4 text-sm">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($package->features as $feature)
                                    <li>{{ $feature->feature }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="px-6 py-4 max-w-sm whitespace-normal">{{ $package->description }}</td>
                        <td class="px-6 py-4">
                            @if(is_null($package->monthly_price))
                                <span class="font-semibold text-green-600">Hubungi Admin</span>
                            @else
                                {{ format_rupiah($package->monthly_price) }} /bln<br>
                                {{ format_rupiah($package->yearly_price) }} /thn
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">{{ $package->discount_year ?? 0 }}%</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button class="edit-btn p-1 text-gray-600 hover:text-blue-600" data-id="{{ $package->id }}"
                                    title="Edit">
                                    <img src="{{ asset('images/edit.png') }}" alt="Edit" class="w-6 h-6">
                                </button>
                                <form action="{{ route('admin.paket.destroy', $package->id) }}" method="POST"
                                    class="inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-btn p-1 text-gray-600 hover:text-red-600"
                                        title="DELETE">
                                        <img src="{{ asset('images/delete.png') }}" alt="Delete" class="w-6 h-6">
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 py-8">Belum ada paket berlangganan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah/Edit Paket -->
    <div id="package-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 id="modal-title-package" class="text-2xl font-bold">Tambah Paket Baru</h3>
                <button id="close-modal-btn" class="text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
            </div>
            <form id="package-form" class="mt-4 space-y-4" method="POST" action="{{ route('admin.paket.store') }}"
                data-update-url="{{ route('admin.paket.update', ['id' => 'PACKAGE_ID']) }}">
                @csrf
                {{-- Method akan ditambahkan oleh JS untuk PUT request --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="package_name" class="block text-base font-semibold text-gray-800 mb-1">Nama Paket<span
                                class="text-red-600">*</span></label>
                        <input type="text" id="package_name" name="package_name"
                            class="block w-full h-12 px-4 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:ring-0 transition"
                            required>
                    </div>
                    <div>
                        <label for="discount_year" class="block text-base font-semibold text-gray-800 mb-1">Diskon Tahunan
                            (%)</label>
                        <input type="number" id="discount_year" name="discount_year"
                            class="block w-full h-12 px-4 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:ring-0 transition"
                            min="0" max="100" placeholder="Contoh: 10">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="monthly_price" class="block text-base font-semibold text-gray-800 mb-1">Harga
                            Bulanan</label>
                        <input type="number" id="monthly_price" name="monthly_price"
                            class="block w-full h-12 px-4 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:ring-0 transition"
                            min="0" placeholder="Kosongkan jika Harga Harus Disesuaikan">
                    </div>
                    <div class="flex items-center pt-8">
                        <input type="hidden" name="is_trial" value="0">
                        <input type="checkbox" id="is_trial" name="is_trial" value="1"
                            class="h-5 w-5 rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <label for="is_trial" class="ml-2 text-gray-700">Jadikan paket trial</label>
                    </div>
                </div>
                <div id="trial-days-container" class="hidden">
                    <label for="trial_days" class="block text-base font-semibold text-gray-800 mb-1">Jumlah Hari
                        Trial</label>
                    <input type="number" id="trial_days" name="trial_days"
                        class="block w-full h-12 px-4 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:ring-0 transition"
                        min="1" placeholder="Contoh: 7">
                </div>
                <div>
                    <label for="description" class="block text-base font-semibold text-gray-800 mb-1">Deskripsi Singkat<span
                            class="text-red-600">*</span></label>
                    <textarea id="description" name="description" rows="2"
                        class="block w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:ring-0 transition"
                        required></textarea>
                </div>
                <div>
                    <h4 class="text-base font-semibold text-gray-800 mb-2">Fitur-fitur Paket</h4>
                    <div id="features-container" class="space-y-2">
                    </div>
                    <button type="button" id="add-feature-btn"
                        class="mt-2 text-sm bg-blue-500 text-white font-semibold px-3 py-1 rounded-md hover:bg-blue-600 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Tambah Fitur</span>
                    </button>
                </div>
                <div class="flex justify-end pt-4 gap-3 border-t mt-4">
                    <button type="button" id="cancel-modal-btn"
                        class="px-6 py-2 rounded-lg text-gray-700 bg-gray-200 hover:bg-gray-300">Batal</button>
                    <button type="submit" id="save-package-btn"
                        class="px-6 py-2 rounded-lg text-white bg-red-700 hover:bg-red-800">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // === LOGIKA SPESIFIK UNTUK HALAMAN INI ===
            // Notifikasi dan konfirmasi hapus ditangani oleh helper (jika ada)

            // Modal Elements
            const packageModal = document.getElementById('package-modal');
            const modalTitle = document.getElementById('modal-title-package');
            const packageForm = document.getElementById('package-form');

            // Buttons
            const addBtn = document.getElementById('add-package-btn');
            const closeModalBtn = document.getElementById('close-modal-btn');
            const cancelModalBtn = document.getElementById('cancel-modal-btn');
            const addFeatureBtn = document.getElementById('add-feature-btn');

            // Form Fields
            const featuresContainer = document.getElementById('features-container');
            const isTrialCheckbox = document.getElementById('is_trial');
            const trialDaysContainer = document.getElementById('trial-days-container');
            const trialDaysInput = document.getElementById('trial_days');
            const tableBody = document.getElementById('packages-table-body');

            let methodInput = null;

            const toggleTrialDays = () => {
                if (isTrialCheckbox.checked) {
                    trialDaysContainer.classList.remove('hidden');
                    trialDaysInput.required = true;
                } else {
                    trialDaysContainer.classList.add('hidden');
                    trialDaysInput.required = false;
                }
            };

            isTrialCheckbox.addEventListener('change', toggleTrialDays);

            const addFeatureInput = (value = '') => {
                const div = document.createElement('div');
                div.className = 'flex items-center gap-2';
                div.innerHTML = `
                            <input type="text" name="features[]" class="block w-full h-11 px-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:ring-0 transition" value="${value}" placeholder="Tuliskan fitur...">
                            <button type="button" class="remove-feature-btn text-red-500 hover:text-red-700 p-1">
                                 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        `;
                featuresContainer.appendChild(div);
                div.querySelector('.remove-feature-btn').addEventListener('click', () => div.remove());
            };

            const resetModal = () => {
                packageForm.reset();
                featuresContainer.innerHTML = '';
                if (methodInput) {
                    methodInput.remove();
                    methodInput = null;
                }
                packageForm.action = `{{ route('admin.paket.store') }}`;
                toggleTrialDays();
            };

            const openModal = (title, data = null) => {
                resetModal();
                modalTitle.textContent = title;

                if (data) { // Edit Mode
                    const updateUrlTemplate = packageForm.dataset.updateUrl;
                    packageForm.action = updateUrlTemplate.replace('PACKAGE_ID', data.id);

                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'PUT';
                    packageForm.appendChild(methodInput);

                    document.getElementById('package_name').value = data.package_name || '';
                    document.getElementById('discount_year').value = data.discount_year || '';
                    document.getElementById('monthly_price').value = data.monthly_price || '';
                    document.getElementById('description').value = data.description || '';
                    isTrialCheckbox.checked = data.is_trial === true || parseInt(data.is_trial) === 1;
                    trialDaysInput.value = data.trial_days || '';

                    if (data.features && data.features.length > 0) {
                        data.features.forEach(feature => addFeatureInput(feature.feature));
                    } else {
                        addFeatureInput();
                    }
                } else { // Create Mode
                    addFeatureInput();
                }

                toggleTrialDays();
                packageModal.classList.remove('hidden');
            };

            const closeModal = () => {
                packageModal.classList.add('hidden');
            };

            addBtn.addEventListener('click', () => openModal('Tambah Paket Baru'));
            closeModalBtn.addEventListener('click', closeModal);
            cancelModalBtn.addEventListener('click', closeModal);
            addFeatureBtn.addEventListener('click', () => addFeatureInput());

            if (tableBody) {
                tableBody.addEventListener('click', function (e) {
                    const editBtn = e.target.closest('.edit-btn');
                    if (!editBtn) return;

                    e.preventDefault();
                    const packageId = editBtn.dataset.id;
                    const urlTemplate = tableBody.dataset.fetchUrl;
                    const url = urlTemplate.replace('PACKAGE_ID', packageId);

                    fetch(url)
                        .then(res => {
                            if (!res.ok) {
                                console.error('Fetch Error:', res.statusText);
                                alert(`Gagal memuat data. Status: ${res.status}`);
                                throw new Error(`Gagal memuat data. Status: ${res.status}`);
                            }
                            return res.json();
                        })
                        .then(data => {
                            if (data && data.id) {
                                openModal('Edit Paket', data);
                            } else {
                                throw new Error('Format data yang diterima tidak valid.');
                            }
                        })
                        .catch(err => {
                            console.error('Fetch error:', err.message);
                        });
                });
            }
        });
    </script>
@endpush