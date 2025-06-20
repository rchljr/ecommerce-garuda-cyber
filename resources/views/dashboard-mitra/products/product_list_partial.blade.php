{{-- HANYA konten HTML untuk daftar produk --}}
<h1 class="text-3xl font-bold mb-6 text-gray-800">Daftar Produk</h1>
<div class="bg-white p-6 rounded-lg shadow-lg">
    <p class="mb-4">Daftar produk Anda saat ini:</p>

    <button id="add-product-button" class="inline-block bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded mb-4 shadow">
        + Tambah Produk Baru
    </button>

    @if($products->isEmpty())
        <p class="text-gray-500 text-center">Belum ada produk.</p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($products as $product)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $product->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $product->category->name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">Rp{{ number_format($product->price, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $product->stock }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $product->status }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                {{-- Gunakan loadContent untuk edit form --}}
                                <button onclick="loadContent('edit-product', '{{ route('dashboard-mitra.products.edit', $product->id) }}')" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</button>
                                {{-- Tambahkan form DELETE untuk AJAX atau refresh --}}
                                <button onclick="deleteProductViaAjax({{ $product->id }})" class="text-red-600 hover:text-red-900">Hapus</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- Script khusus untuk partial ini. Ini akan dieksekusi oleh loadContent. --}}
<script>
    document.getElementById('add-product-button').addEventListener('click', function() {
        loadContent('create-product', '{{ route('dashboard-mitra.products.create.content') }}');
    });

    // Fungsi deleteProductViaAjax harus ada di scope global (di script utama)
    // atau didefinisikan di sini jika Anda tidak ingin fungsi global
    function deleteProductViaAjax(productId) {
        if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
            fetch(`{{ url('dashboard-mitra/products') }}/${productId}`, {
                method: 'POST', // POST karena Laravel akan melihat _method DELETE
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), // Pastikan ada meta CSRF di layout utama
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    _method: 'DELETE' // Metode spoofing untuk Laravel
                })
            })
            .then(response => response.json()) // Asumsikan controller mengembalikan JSON
            .then(data => {
                if (data.success) {
                    alert(data.message || 'Produk berhasil dihapus.');
                    loadContent('products', '{{ route('dashboard-mitra.products.content') }}'); // Muat ulang daftar produk
                } else {
                    alert(data.message || 'Gagal menghapus produk.');
                }
            })
            .catch(error => {
                console.error('Error deleting product:', error);
                alert('Terjadi kesalahan saat menghapus produk.');
            });
        }
    }
</script>