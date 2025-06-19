@extends('layouts.admin')
@section('title', 'Kelola Testimoni')

@section('content')
    <div class="flex flex-col h-full">
        <!-- Header -->
        <div class="flex-shrink-0 flex justify-between items-center mb-6">
            <div id="header-container" class="flex-grow">
                <div id="header-title">
                    <h1 class="text-4xl font-bold text-gray-800">Kelola Testimoni</h1>
                    <p class="text-lg text-gray-500 mt-6">Daftar Testimoni dari Mitra</p>
                </div>
                <input type="text" id="table-search-input" class="hidden w-full h-16 px-4 text-2xl border-2 border-gray-300 rounded-lg focus:border-red-600 focus:ring-0 transition" placeholder="Ketik untuk mencari...">
            </div>
            <div class="flex items-center gap-4 ml-4">
                <button id="search-btn" class="p-2 rounded-full hover:bg-gray-100" title="Cari">
                    <svg width="20" height="20" fill="none">
                        <path d="M19 19l-4.35-4.35M9 16a7 7 0 1 0 0-14 7 7 0 0 0 0 14Z" stroke="#232323" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
                <button id="add-testimonial-btn"
                    class="bg-red-700 text-white font-semibold px-4 py-2 rounded-lg hover:bg-red-800 flex items-center gap-2 transition-colors">
                    <img src="{{ asset('images/tambah-db.png') }}" alt="Tambah" class="w-5 h-5">
                    <span>Tambah Testimoni</span>
                </button>
            </div>
        </div>

        <!-- Tabel Testimoni -->
        <div class="flex-grow overflow-auto bg-white rounded-lg shadow border border-gray-200">
            <table class="w-full whitespace-no-wrap min-w-[1200px]">
                <thead class="bg-gray-200">
                    <tr class="text-center font-semibold text-sm uppercase text-gray-700 tracking-wider">
                        <th class="px-6 py-3 border-b-2 border-gray-300">Nama Pemberi</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300">Testimoni</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300">Tanggal</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300">Rating</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300">Status</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300">Aksi</th>
                    </tr>
                </thead>
                {{-- PERBAIKAN: Memisahkan id dan class dengan benar --}}
                <tbody id="testimonials-table-body" class="searchable-table divide-y divide-gray-200" data-fetch-url="{{ route('admin.testimoni.showJson', ['id' => 'TESTIMONIAL_ID']) }}">
                    @forelse ($testimonials as $testimonial)
                        <tr class="text-gray-700 text-center search-row">
                            <td class="px-6 py-4">
                                <div class="font-bold text-black">{{ $testimonial->user->name ?? $testimonial->name }}</div>
                            </td>
                            <td class="px-6 py-4 max-w-sm whitespace-normal text-left">{{ $testimonial->content }}</td>
                            <td class="px-6 py-4">{{ format_tanggal($testimonial->created_at) }}</td>
                            <td class="px-6 py-4 text-yellow-400 text-lg">
                                @for ($i = 0; $i < 5; $i++)
                                    <span>{{ $i < $testimonial->rating ? '★' : '☆' }}</span>
                                @endfor
                            </td>
                            <td class="px-6 py-4">
                                @if ($testimonial->status == 'published')
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-700">Tampil</span>
                                @else
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-700">Menunggu</span>
                                @endif
                            </td>
                            <td class="px-6 py-8 flex justify-center gap-4">
                                <button class="edit-btn p-1 text-gray-600 hover:text-blue-600" data-id="{{ $testimonial->id }}" title="Edit">
                                    <img src="{{ asset('images/edit.png') }}" alt="Edit" class="w-6 h-6">
                                </button>
                                <form action="{{ route('admin.testimoni.destroy', $testimonial->id) }}" method="POST" class="inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-confirm p-1 text-gray-600 hover:text-red-600" title="Hapus">
                                        <img src="{{ asset('images/delete.png') }}" alt="Delete" class="w-6 h-6">
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-gray-500 py-8">Belum ada testimoni dari mitra.</td>
                        </tr>
                    @endforelse
                    <tr class="no-results-row hidden">
                        <td colspan="6" class="text-center text-gray-500 py-8">Data tidak ditemukan.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah/Edit Testimoni -->
    <div id="testimonial-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 id="modal-title" class="text-2xl font-bold">Tambah Testimoni Baru</h3>
                <button id="close-modal-btn" class="text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
            </div>
            <form id="testimonial-form" method="POST" action="{{ route('admin.testimoni.store') }}" 
                  data-update-url="{{ route('admin.testimoni.update', ['id' => 'TESTIMONIAL_ID']) }}" class="mt-4 space-y-4">
                @csrf
                {{-- Method PUT akan disisipkan oleh JS --}}
                <div>
                    <label for="name" class="block text-base font-semibold text-gray-800 mb-1">Nama Pemberi Testimoni<span class="text-red-600">*</span></label>
                    <input type="text" id="name" name="name" class="block w-full h-12 px-4 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:ring-0 transition" required>
                </div>
                <div>
                    <label for="content" class="block text-base font-semibold text-gray-800 mb-1">Isi Testimoni<span class="text-red-600">*</span></label>
                    <textarea id="content" name="content" rows="4" class="block w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:ring-0 transition" required></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-base font-semibold text-gray-800 mb-1">Rating<span class="text-red-600">*</span></label>
                        <div id="star-rating-container" class="flex items-center text-3xl text-gray-300">
                            <span class="star cursor-pointer" data-value="1">★</span>
                            <span class="star cursor-pointer" data-value="2">★</span>
                            <span class="star cursor-pointer" data-value="3">★</span>
                            <span class="star cursor-pointer" data-value="4">★</span>
                            <span class="star cursor-pointer" data-value="5">★</span>
                        </div>
                        <input type="hidden" name="rating" id="rating_value" value="5" required>
                    </div>
                    <div>
                        <label for="status" class="block text-base font-semibold text-gray-800 mb-1">Status<span class="text-red-600">*</span></label>
                        <select id="status" name="status" class="block w-full h-12 px-4 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:ring-0 transition" required>
                            <option value="published">Tampilkan</option>
                            <option value="pending">Menunggu</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end pt-4 gap-3 border-t mt-4">
                    <button type="button" id="cancel-modal-btn" class="px-6 py-2 rounded-lg text-gray-700 bg-gray-200 hover:bg-gray-300">Batal</button>
                    <button type="submit" class="px-6 py-2 rounded-lg text-white bg-red-700 hover:bg-red-800">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // === LOGIKA MODAL ===
    const modal = document.getElementById('testimonial-modal');
    const addBtn = document.getElementById('add-testimonial-btn');
    const closeModalBtn = document.getElementById('close-modal-btn');
    const cancelModalBtn = document.getElementById('cancel-modal-btn');
    const modalTitle = document.getElementById('modal-title');
    const testimonialForm = document.getElementById('testimonial-form');
    const tableBody = document.getElementById('testimonials-table-body');
    let methodInput = null;
    
    // Logika Rating Bintang
    const starRatingContainer = document.getElementById('star-rating-container');
    const ratingValueInput = document.getElementById('rating_value');
    const stars = starRatingContainer.querySelectorAll('.star');
    let currentRating = 5;

    const updateStars = (rating) => {
        stars.forEach(star => {
            const starValue = parseInt(star.dataset.value);
            star.classList.toggle('text-yellow-400', starValue <= rating);
            star.classList.toggle('text-gray-300', starValue > rating);
        });
    };
    
    stars.forEach(star => {
        star.addEventListener('mouseover', () => updateStars(star.dataset.value));
        star.addEventListener('mouseout', () => updateStars(currentRating));
        star.addEventListener('click', () => {
            currentRating = star.dataset.value;
            ratingValueInput.value = currentRating;
            updateStars(currentRating);
        });
    });

    const openModal = (title, data = null) => {
        testimonialForm.reset();
        modalTitle.textContent = title;
        if (methodInput) {
            methodInput.remove();
            methodInput = null;
        }

        if (data) { // Edit Mode
            const updateUrlTemplate = testimonialForm.dataset.updateUrl;
            testimonialForm.action = updateUrlTemplate.replace('TESTIMONIAL_ID', data.id);
            
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            testimonialForm.prepend(methodInput);

            document.getElementById('name').value = data.name || '';
            document.getElementById('content').value = data.content || '';
            document.getElementById('status').value = data.status || 'pending';
            
            currentRating = data.rating || 5;
            ratingValueInput.value = currentRating;
            updateStars(currentRating);

        } else { // Create Mode
            testimonialForm.action = "{{ route('admin.testimoni.store') }}";
            currentRating = 5;
            ratingValueInput.value = currentRating;
            updateStars(currentRating);
        }
        modal.classList.remove('hidden');
    };

    const closeModal = () => modal.classList.add('hidden');

    addBtn.addEventListener('click', () => openModal('Tambah Testimoni Baru'));
    closeModalBtn.addEventListener('click', closeModal);
    cancelModalBtn.addEventListener('click', closeModal);

    if (tableBody) {
        tableBody.addEventListener('click', (e) => {
            const editBtn = e.target.closest('.edit-btn');
            if (!editBtn) return;

            e.preventDefault();
            const testimonialId = editBtn.dataset.id;
            const urlTemplate = tableBody.dataset.fetchUrl;
            const url = urlTemplate.replace('TESTIMONIAL_ID', testimonialId);

            fetch(url)
                .then(res => {
                    if (!res.ok) throw new Error(`Gagal memuat data. Status: ${res.status}`);
                    return res.json();
                })
                .then(data => {
                    if (data && data.id) {
                        openModal('Edit Testimoni', data);
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
