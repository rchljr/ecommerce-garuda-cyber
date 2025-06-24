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
                {{-- Form pencarian bisa diimplementasikan di sini nanti --}}
            </div>
            <div class="flex items-center gap-4 ml-4">
                <div class="relative">
                    <form action="{{ route('admin.testimoni.index') }}" method="GET">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </span>
                        <input type="text" name="search" value="{{ $search ?? '' }}" class="block w-64 pl-10 pr-4 py-2 h-12 border-2 border-gray-300 rounded-lg bg-white focus:border-red-600 focus:ring-0 transition" placeholder="Cari nama atau isi...">
                    </form>
                </div>
                <button id="add-testimonial-btn" class="bg-red-700 text-white font-semibold px-4 py-2 rounded-lg hover:bg-red-800 flex items-center gap-2 transition-colors flex-shrink-0">
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
                <tbody id="testimonials-table-body" class="divide-y divide-gray-200" data-fetch-url="{{ route('admin.testimoni.showJson', ['id' => 'TESTIMONIAL_ID']) }}">
                    @forelse ($testimonials as $testimonial)
                        <tr class="text-gray-700 text-center">
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
                                <form action="{{ route('admin.testimoni.updateStatus', $testimonial->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    @if ($testimonial->status == 'published')
                                        <input type="hidden" name="status" value="pending">
                                        <button type="submit" class="p-1 text-gray-600 hover:text-yellow-600" title="Sembunyikan">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a9.97 9.97 0 01-1.563 3.029m0 0l-3.29-3.29m0 0l-3.29 3.29"></path></svg>
                                        </button>
                                    @else
                                        <input type="hidden" name="status" value="published">
                                        <button type="submit" class="p-1 text-gray-600 hover:text-green-600" title="Tampilkan">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.522 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>
                                    @endif
                                </form>

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
                            <td colspan="6" class="text-center text-gray-500 py-8">Belum ada testimoni.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $testimonials->links() }}
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
                    <label for="name" class="block text-base font-semibold text-gray-800 mb-1">Nama Pemberi<span class="text-red-600">*</span></label>
                    <input type="text" id="name" name="name" class="block w-full h-12 px-4 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:ring-0 transition" required>
                </div>
                <div>
                    <label for="content" class="block text-base font-semibold text-gray-800 mb-1">Isi Testimoni<span class="text-red-600">*</span></label>
                    <textarea id="content" name="content" rows="4" class="block w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:ring-0 transition" required></textarea>
                </div>
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
                {{-- PERUBAHAN: Input status dihapus dari sini --}}
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
