{{-- resources/views/dashboard-mitra/slides/create_edit_form_partial.blade.php --}}

@php
    $isEdit = isset($slide);
    $formAction = $isEdit ? route('dashboard-mitra.slides.update', $slide->id) : route('dashboard-mitra.slides.store');
    $formMethod = $isEdit ? 'PUT' : 'POST';
    $title = $isEdit ? 'Edit Slide' : 'Tambah Slide Baru';
@endphp

<h1 class="text-3xl font-bold mb-6 text-gray-800">{{ $title }}</h1>

<div class="bg-white p-6 rounded-lg shadow-lg">
    <form id="slideForm" action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($isEdit)
            @method('PUT') {{-- Metode spoofing untuk PUT request --}}
        @endif

        <div class="mb-4">
            <label for="image" class="block text-gray-700 text-sm font-bold mb-2">Gambar Slide:</label>
            <input type="file" name="image" id="image" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            @if($isEdit && $slide->image_path)
                <img src="{{ Storage::url($slide->image_path) }}" alt="Current Slide Image" class="mt-2 h-24 w-auto object-cover rounded">
                <p class="text-xs text-gray-500 mt-1">Gambar saat ini. Unggah yang baru untuk mengganti.</p>
            @endif
            @error('image') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Judul:</label>
            <input type="text" name="title" id="title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('title', $slide->title ?? '') }}">
            @error('title') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label for="content" class="block text-gray-700 text-sm font-bold mb-2">Isi:</label>
            <textarea name="content" id="content" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('content', $slide->content ?? '') }}</textarea>
            @error('content') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label for="text_position" class="block text-gray-700 text-sm font-bold mb-2">Posisi Teks:</label>
            <select name="text_position" id="text_position" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <option value="">Pilih Posisi</option>
                <option value="left" {{ old('text_position', $slide->text_position ?? '') == 'left' ? 'selected' : '' }}>Kiri</option>
                <option value="center" {{ old('text_position', $slide->text_position ?? '') == 'center' ? 'selected' : '' }}>Tengah</option>
                <option value="right" {{ old('text_position', $slide->text_position ?? '') == 'right' ? 'selected' : '' }}>Kanan</option>
            </select>
            @error('text_position') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label for="text_color" class="block text-gray-700 text-sm font-bold mb-2">Warna Huruf (Contoh: white, #FF0000):</label>
            <input type="text" name="text_color" id="text_color" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('text_color', $slide->text_color ?? '') }}">
            @error('text_color') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label for="button_text" class="block text-gray-700 text-sm font-bold mb-2">Judul Tombol:</label>
            <input type="text" name="button_text" id="button_text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('button_text', $slide->button_text ?? '') }}">
            @error('button_text') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label for="button_url" class="block text-gray-700 text-sm font-bold mb-2">Link URL Tombol:</label>
            <input type="url" name="button_url" id="button_url" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('button_url', $slide->button_url ?? '') }}">
            @error('button_url') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label for="order" class="block text-gray-700 text-sm font-bold mb-2">Urutan:</label>
            <input type="number" name="order" id="order" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('order', $slide->order ?? '0') }}" min="0">
            @error('order') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4 flex items-center">
            <input type="hidden" name="is_active" value="0"> {{-- Hidden field agar nilai 0 tetap terkirim jika checkbox tidak dicentang --}}
            <input type="checkbox" name="is_active" id="is_active" class="form-checkbox h-5 w-5 text-blue-600" value="1" {{ old('is_active', $slide->is_active ?? true) ? 'checked' : '' }}>
            <label for="is_active" class="ml-2 block text-gray-700 text-sm font-bold">Aktif</label>
            @error('is_active') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Simpan
            </button>
            <button type="button" onclick="loadContent('slides', '{{ route('dashboard-mitra.slides.content') }}')" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Batal
            </button>
        </div>
    </form>
</div>

<script>
    document.getElementById('slideForm').addEventListener('submit', async function(event) {
        event.preventDefault(); // Mencegah form submit secara default

        const form = event.target;
        const formData = new FormData(form);
        const actionUrl = form.action;
        const method = form.method; // Ini akan jadi POST, kita handle _method di Laravel

        try {
            const response = await fetch(actionUrl, {
                method: 'POST', // Selalu POST untuk form dengan FormData
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json' // Beritahu server kita berharap JSON
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok) { // Status 2xx
                alert(data.message);
                loadContent('slides', '{{ route('dashboard-mitra.slides.content') }}'); // Muat ulang daftar slide
            } else { // Status 4xx, 5xx (misal validasi gagal)
                let errorMessages = '';
                if (data.errors) {
                    for (const key in data.errors) {
                        errorMessages += data.errors[key].join('\n') + '\n';
                    }
                } else if (data.message) {
                    errorMessages = data.message;
                } else {
                    errorMessages = 'Terjadi kesalahan tidak dikenal.';
                }
                alert('Gagal menyimpan slide:\n' + errorMessages);
            }
        } catch (error) {
            console.error('Error submitting form:', error);
            alert('Terjadi kesalahan saat berkomunikasi dengan server.');
        }
    });
</script>