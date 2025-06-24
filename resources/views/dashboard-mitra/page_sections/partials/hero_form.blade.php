{{-- resources/views/admin/page_sections/partials/hero_form.blade.php --}}
{{-- Variabel $content akan berisi data lama (old() atau dari database saat edit) --}}

<h3 class="text-xl font-semibold text-gray-700 mb-4">Konten Seksi Hero</h3>

<div id="slides-container" class="space-y-6">
    @php
        // Pastikan $content['slides'] adalah array, bahkan jika kosong
        $slides = $content['slides'] ?? [];
        // Jika tidak ada slide, tambahkan satu slide kosong untuk form awal
        if (empty($slides)) {
            $slides = [[]]; // Tambahkan satu slide kosong
        }
    @endphp

    @foreach ($slides as $index => $slide)
        <div class="slide-item bg-gray-50 p-4 rounded-lg border border-gray-200" data-slide-index="{{ $index }}">
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-lg font-semibold text-gray-800">Slide #<span class="slide-number">{{ $index + 1 }}</span></h4>
                <button type="button" class="remove-slide-btn text-red-600 hover:text-red-800 font-semibold text-sm">Hapus Slide</button>
            </div>

            <input type="hidden" name="content[slides][{{ $index }}][id]" value="{{ $slide['id'] ?? '' }}">

            <div class="mb-4">
                <label for="slide_subtitle_{{ $index }}" class="block text-gray-700 text-sm font-bold mb-2">Subtitle:</label>
                <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="slide_subtitle_{{ $index }}" name="content[slides][{{ $index }}][subtitle]" value="{{ $slide['subtitle'] ?? '' }}" placeholder="Masukkan subtitle">
            </div>

            <div class="mb-4">
                <label for="slide_title_{{ $index }}" class="block text-gray-700 text-sm font-bold mb-2">Judul <span class="text-red-500">*</span>:</label>
                <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="slide_title_{{ $index }}" name="content[slides][{{ $index }}][title]" value="{{ $slide['title'] ?? '' }}" required placeholder="Masukkan judul">
            </div>

            <div class="mb-4">
                <label for="slide_description_{{ $index }}" class="block text-gray-700 text-sm font-bold mb-2">Deskripsi:</label>
                <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="slide_description_{{ $index }}" name="content[slides][{{ $index }}][description]" rows="3" placeholder="Masukkan deskripsi">{{ $slide['description'] ?? '' }}</textarea>
            </div>

            <div class="mb-4">
                <label for="slide_button_text_{{ $index }}" class="block text-gray-700 text-sm font-bold mb-2">Teks Tombol:</label>
                <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="slide_button_text_{{ $index }}" name="content[slides][{{ $index }}][button_text]" value="{{ $slide['button_text'] ?? '' }}" placeholder="Contoh: Belanja Sekarang">
            </div>

            <div class="mb-4">
                <label for="slide_button_url_{{ $index }}" class="block text-gray-700 text-sm font-bold mb-2">URL Tombol:</label>
                <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="slide_button_url_{{ $index }}" name="content[slides][{{ $index }}][button_url]" value="{{ $slide['button_url'] ?? '' }}" placeholder="Contoh: /shop">
            </div>

            <div class="mb-4">
                <label for="slide_background_image_{{ $index }}" class="block text-gray-700 text-sm font-bold mb-2">Gambar Latar Belakang:</label>
                <input type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" id="slide_background_image_{{ $index }}" name="content[slides][{{ $index }}][background_image]">
                @if(isset($slide['background_image']) && $slide['background_image'])
                    <div class="mt-2">
                        Gambar Saat Ini: <img src="{{ asset('storage/' . $slide['background_image']) }}" alt="Gambar Latar Belakang Saat Ini" class="w-32 h-20 object-cover rounded shadow">
                        <input type="hidden" name="content[slides][{{ $index }}][current_background_image]" value="{{ $slide['background_image'] }}">
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>

<button type="button" id="add-slide-btn" class="inline-flex items-center mt-6 px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
    Tambah Slide Baru
</button>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const slidesContainer = document.getElementById('slides-container');
        const addSlideBtn = document.getElementById('add-slide-btn');
        let slideIndex = slidesContainer.children.length > 0 ? parseInt(slidesContainer.lastElementChild.dataset.slideIndex) + 1 : 0;

        function updateSlideNumbers() {
            slidesContainer.querySelectorAll('.slide-item').forEach((item, idx) => {
                item.querySelector('.slide-number').textContent = idx + 1;
                item.dataset.slideIndex = idx; // Update data-slide-index
                // Update input names and IDs for consistency (optional but good practice)
                item.querySelectorAll('[name^="content[slides]"]').forEach(input => {
                    const oldName = input.name;
                    const newName = oldName.replace(/content\[slides\]\[\d+\]/, `content[slides][${idx}]`);
                    input.name = newName;
                    if (input.id) {
                        const oldId = input.id;
                        const newId = oldId.replace(/slide_\w+_\d+/, `${oldId.split('_').slice(0, -1).join('_')}_${idx}`);
                        input.id = newId;
                    }
                });
            });
        }

        function addSlide(slideData = {}) {
            const newIndex = slideIndex++; // Gunakan indeks saat ini lalu tingkatkan
            const slideHtml = `
                <div class="slide-item bg-gray-50 p-4 rounded-lg border border-gray-200" data-slide-index="${newIndex}">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-lg font-semibold text-gray-800">Slide #<span class="slide-number">${newIndex + 1}</span></h4>
                        <button type="button" class="remove-slide-btn text-red-600 hover:text-red-800 font-semibold text-sm">Hapus Slide</button>
                    </div>

                    <input type="hidden" name="content[slides][${newIndex}][id]" value="${slideData.id || ''}">

                    <div class="mb-4">
                        <label for="slide_subtitle_${newIndex}" class="block text-gray-700 text-sm font-bold mb-2">Subtitle:</label>
                        <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="slide_subtitle_${newIndex}" name="content[slides][${newIndex}][subtitle]" value="${slideData.subtitle || ''}" placeholder="Masukkan subtitle">
                    </div>

                    <div class="mb-4">
                        <label for="slide_title_${newIndex}" class="block text-gray-700 text-sm font-bold mb-2">Judul <span class="text-red-500">*</span>:</label>
                        <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="slide_title_${newIndex}" name="content[slides][${newIndex}][title]" value="${slideData.title || ''}" required placeholder="Masukkan judul">
                    </div>

                    <div class="mb-4">
                        <label for="slide_description_${newIndex}" class="block text-gray-700 text-sm font-bold mb-2">Deskripsi:</label>
                        <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="slide_description_${newIndex}" name="content[slides][${newIndex}][description]" rows="3" placeholder="Masukkan deskripsi">${slideData.description || ''}</textarea>
                    </div>

                    <div class="mb-4">
                        <label for="slide_button_text_${newIndex}" class="block text-gray-700 text-sm font-bold mb-2">Teks Tombol:</label>
                        <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="slide_button_text_${newIndex}" name="content[slides][${newIndex}][button_text]" value="${slideData.button_text || ''}" placeholder="Contoh: Belanja Sekarang">
                    </div>

                    <div class="mb-4">
                        <label for="slide_button_url_${newIndex}" class="block text-gray-700 text-sm font-bold mb-2">URL Tombol:</label>
                        <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="slide_button_url_${newIndex}" name="content[slides][${newIndex}][button_url]" value="${slideData.button_url || ''}" placeholder="Contoh: /shop">
                    </div>

                    <div class="mb-4">
                        <label for="slide_background_image_${newIndex}" class="block text-gray-700 text-sm font-bold mb-2">Gambar Latar Belakang:</label>
                        <input type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" id="slide_background_image_${newIndex}" name="content[slides][${newIndex}][background_image]">
                        ${slideData.background_image ? `
                            <div class="mt-2">
                                Gambar Saat Ini: <img src="{{ asset('storage') }}/${slideData.background_image}" alt="Gambar Latar Belakang Saat Ini" class="w-32 h-20 object-cover rounded shadow">
                                <input type="hidden" name="content[slides][${newIndex}][current_background_image]" value="${slideData.background_image}">
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
            slidesContainer.insertAdjacentHTML('beforeend', slideHtml);
            attachRemoveListeners(); // Attach listener to new button
            updateSlideNumbers();
        }

        function attachRemoveListeners() {
            slidesContainer.querySelectorAll('.remove-slide-btn').forEach(button => {
                button.onclick = function() {
                    // Pastikan tidak menghapus semua slide
                    if (slidesContainer.children.length > 1) {
                        this.closest('.slide-item').remove();
                        updateSlideNumbers();
                    } else {
                        alert('Minimal harus ada satu slide.');
                    }
                };
            });
        }

        addSlideBtn.addEventListener('click', () => addSlide());
        attachRemoveListeners(); // Attach listeners for existing buttons on page load
        updateSlideNumbers(); // Ensure initial numbers are correct

        // Re-calculate initial slideIndex based on existing elements to avoid overwriting
        if (slidesContainer.children.length > 0) {
            slideIndex = parseInt(slidesContainer.lastElementChild.dataset.slideIndex) + 1;
        } else {
            // If no slides initially, add one empty slide
            addSlide();
        }
    });
</script>
@endpush
