{{-- resources/views/admin/page_sections/partials/text_block_form.blade.php --}}
{{-- Variabel $content akan berisi data lama (old() atau dari database saat edit) --}}

<h3 class="text-xl font-semibold text-gray-700 mb-4">Konten Blok Teks</h3>

<div class="mb-4">
    <label for="content_heading" class="block text-gray-700 text-sm font-bold mb-2">Judul Blok Teks:</label>
    <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('content.heading') border-red-500 @enderror" id="content_heading" name="content[heading]" value="{{ $content['heading'] ?? '' }}" placeholder="Masukkan judul blok teks">
    @error('content.heading')
        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="content_body" class="block text-gray-700 text-sm font-bold mb-2">Isi Teks:</label>
    <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('content.body') border-red-500 @enderror" id="content_body" name="content[body]" rows="6" placeholder="Masukkan isi teks di sini">{{ $content['body'] ?? '' }}</textarea>
    @error('content.body')
        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
    @enderror
    {{-- Pertimbangkan untuk mengintegrasikan editor WYSIWYG seperti TinyMCE atau CKEditor di sini --}}
</div>
