{{-- resources/views/dashboard-mitra/slides/index_partial.blade.php --}}
<h1 class="text-3xl font-bold mb-6 text-gray-800">Manajemen Slide</h1>

<div class="bg-white p-6 rounded-lg shadow-lg">
    <h2 class="text-xl font-semibold mb-4 text-gray-800">Gambar Slide</h2>

    {{-- Tombol Tambah Slide Image --}}
    <button id="add-slide-image-btn" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded mb-4">
        Tambah Slide Image
    </button>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gambar</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Isi</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posisi Teks</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warna Huruf</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul Tombol</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Link URL</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($slides as $slide)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($slide->image_path)
                                <img src="{{ Storage::url($slide->image_path) }}" alt="{{ $slide->title }}" class="h-16 w-24 object-cover rounded">
                            @else
                                <span class="text-gray-400">No Image</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $slide->title ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ Str::limit($slide->content, 50) ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ ucfirst($slide->text_position) ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $slide->text_color ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $slide->button_text ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-500">{{ Str::limit($slide->button_url, 30) ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button onclick="loadContent('edit-slide', '{{ route('dashboard-mitra.slides.edit.form', $slide->id) }}')" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</button>
                            <button onclick="deleteSlide({{ $slide->id }})" class="text-red-600 hover:text-red-900">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Belum ada slide.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Tombol "Simpan Perubahan" akan berada di layout utama atau di form edit --}}
    {{-- <button class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mt-6">Simpan Perubahan</button> --}}

</div>

<script>
    // Pastikan loadContent sudah didefinisikan di script utama HTML layout
    document.getElementById('add-slide-image-btn').addEventListener('click', function() {
        loadContent('create-slide', '{{ route('dashboard-mitra.slides.create.form') }}');
    });

    function deleteSlide(slideId) {
        if (confirm('Apakah Anda yakin ingin menghapus slide ini?')) {
            fetch(`{{ url('dashboard-mitra/slides') }}/${slideId}`, {
                method: 'POST', // Menggunakan POST untuk spoofing DELETE
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    _method: 'DELETE' // Metode spoofing
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    loadContent('slides', '{{ route('dashboard-mitra.slides.content') }}'); // Muat ulang daftar slide
                } else {
                    alert(data.message || 'Gagal menghapus slide.');
                }
            })
            .catch(error => {
                console.error('Error deleting slide:', error);
                alert('Terjadi kesalahan saat menghapus slide.');
            });
        }
    }
</script>