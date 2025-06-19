@extends('layouts.admin')
@section('title', 'Kelola Kategori')

@section('content')
    <div class="flex flex-col h-full">
        <!-- Header -->
        <div class="flex-shrink-0 flex justify-between items-center mb-6">
            <div>
                <h1 class="text-4xl font-bold text-gray-800">Kelola Kategori Produk</h1>
                <p class="text-lg text-gray-500 mt-6">Daftar Kategori dan Sub-kategori Produk</p>
            </div>
            <div class="flex items-center gap-4">
                <button class="p-2 rounded-full hover:bg-gray-100" title="Cari">
                    <svg width="20" height="20" fill="none">
                        <path d="M19 19l-4.35-4.35M9 16a7 7 0 1 0 0-14 7 7 0 0 0 0 14Z" stroke="#232323" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
                <button id="add-category-btn"
                    class="bg-red-700 text-white font-semibold px-4 py-2 rounded-lg hover:bg-red-800 flex items-center gap-2 transition-colors">
                    <img src="{{ asset('images/tambah-db.png') }}" alt="Tambah" class="w-5 h-5">
                    <span>Tambah Kategori</span>
                </button>
            </div>
        </div>

        <!-- Tabel Kategori -->
        <div class="flex-grow overflow-auto bg-white rounded-lg shadow border border-gray-200">
            <table class="w-full whitespace-no-wrap min-w-[800px]">
                <thead class="bg-gray-200">
                    <tr class="text-left font-semibold text-sm uppercase text-gray-700 tracking-wider">
                        <th class="px-6 py-3 border-b-2 border-gray-300">Kategori</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300">Sub-Kategori</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="categories-table-body"
                    data-fetch-url="{{ route('admin.kategori.showJson', ['id' => 'CATEGORY_ID']) }}"
                    class="divide-y divide-gray-200">
                    @forelse($categories as $category)
                        <tr class="text-gray-700 text-left align-top">
                            <td class="px-6 py-4 font-bold text-base">{{ $category->name }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($category->subcategories as $subcategory)
                                        <span
                                            class="bg-gray-100 text-gray-700 text-sm font-medium px-3 py-1 rounded-full">{{ $subcategory->name }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 flex justify-center gap-3">
                                <button class="edit-btn p-1 text-gray-600 hover:text-blue-600" data-id="{{ $category->id }}"
                                    title="Edit">
                                    <img src="{{ asset('images/edit.png') }}" alt="Edit" class="w-6 h-6">
                                </button>
                                <form action="{{ route('admin.kategori.destroy', $category->id) }}" method="POST"
                                    class="inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-confirm p-1 text-gray-600 hover:text-red-600"
                                        title="Delete">
                                        <img src="{{ asset('images/delete.png') }}" alt="Delete" class="w-6 h-6">
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-gray-500 py-8">Belum ada kategori yang ditambahkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah/Edit Kategori -->
    <div id="category-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 id="modal-title" class="text-2xl font-bold">Tambah Kategori Baru</h3>
                <button id="close-modal-btn" class="text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
            </div>
            <form id="category-form" method="POST" action="{{ route('admin.kategori.store') }}"
                data-update-url="{{ route('admin.kategori.update', ['id' => 'CATEGORY_ID']) }}" class="mt-4 space-y-4">
                @csrf
                {{-- Method PUT akan disisipkan oleh JS di sini saat mode edit --}}
                <div>
                    <label for="name" class="block text-base font-semibold text-gray-800 mb-1">Nama Kategori<span
                            class="text-red-600">*</span></label>
                    <input type="text" id="name" name="name"
                        class="block w-full h-12 px-4 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:ring-0 transition"
                        required>
                </div>
                <div>
                    <h4 class="text-base font-semibold text-gray-800 mb-2">Sub-Kategori</h4>
                    <div id="subcategories-container" class="space-y-2">
                        <!-- Sub-kategori dinamis akan ditambahkan di sini -->
                    </div>
                    <button type="button" id="add-subcategory-btn"
                        class="mt-2 text-sm bg-blue-500 text-white font-semibold px-3 py-1 rounded-md hover:bg-blue-600 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Tambah Sub-Kategori</span>
                    </button>
                </div>
                <div class="flex justify-end pt-4 gap-3 border-t mt-4">
                    <button type="button" id="cancel-modal-btn"
                        class="px-6 py-2 rounded-lg text-gray-700 bg-gray-200 hover:bg-gray-300">Batal</button>
                    <button type="submit"
                        class="px-6 py-2 rounded-lg text-white bg-red-700 hover:bg-red-800">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Diasumsikan helper alert dan konfirmasi hapus sudah dimuat dari layout utama --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // === ELEMEN DOM ===
            const modal = document.getElementById('category-modal');
            const addBtn = document.getElementById('add-category-btn');
            const closeModalBtn = document.getElementById('close-modal-btn');
            const cancelModalBtn = document.getElementById('cancel-modal-btn');
            const modalTitle = document.getElementById('modal-title');
            const categoryForm = document.getElementById('category-form');
            const tableBody = document.getElementById('categories-table-body');
            const subcategoriesContainer = document.getElementById('subcategories-container');
            const addSubcategoryBtn = document.getElementById('add-subcategory-btn');
            let methodInput = null;

            // === FUNGSI-FUNGSI ===
            const addSubcategoryInput = (value = '') => {
                const div = document.createElement('div');
                div.className = 'flex items-center gap-2';
                div.innerHTML = `
                    <input type="text" name="subcategories[]" class="block w-full h-11 px-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:ring-0 transition" value="${value}" placeholder="Nama Sub-Kategori">
                    <button type="button" class="remove-subcategory-btn text-red-500 hover:text-red-700 p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                `;
                subcategoriesContainer.appendChild(div);
                div.querySelector('.remove-subcategory-btn').addEventListener('click', () => div.remove());
            };

            const resetModal = () => {
                categoryForm.reset();
                subcategoriesContainer.innerHTML = '';
                if (methodInput) {
                    methodInput.remove();
                    methodInput = null;
                }
                categoryForm.action = "{{ route('admin.kategori.store') }}";
            };

            const openModal = (title, data = null) => {
                resetModal();
                modalTitle.textContent = title;

                if (data) { // Edit Mode
                    const updateUrlTemplate = categoryForm.dataset.updateUrl;
                    categoryForm.action = updateUrlTemplate.replace('CATEGORY_ID', data.id);

                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'PUT';
                    categoryForm.prepend(methodInput);

                    document.getElementById('name').value = data.name || '';
                    if (data.subcategories && data.subcategories.length > 0) {
                        data.subcategories.forEach(sub => addSubcategoryInput(sub.name));
                    } else {
                        addSubcategoryInput();
                    }
                } else { // Create Mode
                    addSubcategoryInput();
                }
                modal.classList.remove('hidden');
            };

            const closeModal = () => modal.classList.add('hidden');

            // === EVENT LISTENERS ===
            addBtn.addEventListener('click', () => openModal('Tambah Kategori Baru'));
            closeModalBtn.addEventListener('click', closeModal);
            cancelModalBtn.addEventListener('click', closeModal);
            addSubcategoryBtn.addEventListener('click', () => addSubcategoryInput());

            if (tableBody) {
                tableBody.addEventListener('click', (e) => {
                    const editBtn = e.target.closest('.edit-btn');
                    if (!editBtn) return;

                    e.preventDefault();
                    const categoryId = editBtn.dataset.id;
                    const urlTemplate = tableBody.dataset.fetchUrl;
                    const url = urlTemplate.replace('CATEGORY_ID', categoryId);

                    fetch(url)
                        .then(res => {
                            if (!res.ok) throw new Error(`Gagal memuat data. Status: ${res.status}`);
                            return res.json();
                        })
                        .then(data => {
                            if (data && data.id) {
                                openModal('Edit Kategori', data);
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