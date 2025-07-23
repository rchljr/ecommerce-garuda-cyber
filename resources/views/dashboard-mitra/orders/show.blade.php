@extends('layouts.mitra')

@section('title', 'Detail Pesanan #' . $order->id)

@push('styles')
<style>
    /* Styling for disabled select */
    select:disabled {
        background-color: #f3f4f6; /* Tailwind gray-100 */
        cursor: not-allowed;
        opacity: 0.7;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('status-select');
        const deliveryServiceGroup = document.getElementById('delivery-service-group');
        const deliveryServiceInput = document.getElementById('delivery-service-input');
        const trackingNumberGroup = document.getElementById('tracking-number-group');
        const trackingNumberInput = document.getElementById('tracking-number-input');
        const updateStatusBtn = document.getElementById('update-status-btn');
        const orderId = updateStatusBtn.dataset.orderId;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const initialOrderStatus = statusSelect.value; // Status awal saat halaman dimuat
        const initialDeliveryMethod = document.getElementById('current-delivery-method-hidden').value.toLowerCase().trim();

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast-notification');
            if (!toast) {
                console.error('Toast notification element not found.');
                alert(message);
                return;
            }
            toast.textContent = message;
            toast.className = `toast-notification ${type} show`;
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }
        
        if (typeof axios === 'undefined') {
            console.error('Axios library is not loaded. AJAX requests will fail.');
            updateStatusBtn.type = 'submit';
            return;
        }
        axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;

        // --- Logika Kontrol Dropdown Status & Input Pengiriman ---
        function updateStatusControls() {
            const currentSelectedStatus = statusSelect.value;

            // Atur ulang semua opsi di dropdown status
            statusSelect.querySelectorAll('option').forEach(option => {
                option.style.display = 'none'; // Sembunyikan semua
            });
            statusSelect.querySelector(`option[value="${currentSelectedStatus}"]`).style.display = 'block'; // Tampilkan status saat ini

            // Sembunyikan input resi & layanan default
            deliveryServiceGroup.classList.add('hidden');
            deliveryServiceInput.removeAttribute('required');
            trackingNumberGroup.classList.add('hidden');
            trackingNumberInput.removeAttribute('required');

            // Nonaktifkan dropdown status secara default
            statusSelect.disabled = true;
            updateStatusBtn.disabled = true; // Nonaktifkan tombol update secara default

            // Logika berdasarkan initialOrderStatus (status order saat ini dari DB)
            switch (initialOrderStatus) {
                case 'pending':
                case 'failed':
                case 'cancelled':
                case 'refund_requested':
                case 'refunded':
                    // Untuk status ini, dropdown status disabled. Tidak ada aksi manual.
                    // Ini diharapkan diatur otomatis oleh sistem pembayaran.
                    statusSelect.querySelector(`option[value="${initialOrderStatus}"]`).selected = true; // Pastikan status awal terpilih
                    updateStatusBtn.textContent = 'Status Tidak Dapat Diubah';
                    break;

                case 'processing': // Order sedang diproses oleh toko
                    statusSelect.disabled = false; // Aktifkan dropdown
                    statusSelect.querySelector(`option[value="processing"]`).style.display = 'block';
                    if (initialDeliveryMethod === 'delivery') {
                        statusSelect.querySelector(`option[value="shipped"]`).style.display = 'block'; // Bisa ke Dikirim
                    } else if (initialDeliveryMethod === 'pickup') {
                        statusSelect.querySelector(`option[value="ready_for_pickup"]`).style.display = 'block'; // Bisa ke Siap Dijemput
                    }
                    updateStatusBtn.disabled = false;
                    updateStatusBtn.textContent = 'Perbarui Status';

                    // Tampilkan input resi/layanan jika ini adalah order 'delivery' dan status dipilih 'shipped' (setelah diubah)
                    if (initialDeliveryMethod === 'delivery' && statusSelect.value === 'shipped') {
                        deliveryServiceGroup.classList.remove('hidden');
                        deliveryServiceInput.setAttribute('required', 'required');
                        trackingNumberGroup.classList.remove('hidden');
                        trackingNumberInput.setAttribute('required', 'required');
                    }
                    break;

                case 'shipped': // Barang sudah dikirim (delivery)
                    statusSelect.disabled = false; // Aktifkan dropdown
                    statusSelect.querySelector(`option[value="shipped"]`).style.display = 'block';
                    statusSelect.querySelector(`option[value="completed"]`).style.display = 'block'; // Bisa ke Selesai
                    updateStatusBtn.disabled = false;
                    updateStatusBtn.textContent = 'Perbarui Status';
                    // Tampilkan resi/layanan sebagai tampilan (read-only)
                    deliveryServiceGroup.classList.remove('hidden');
                    trackingNumberGroup.classList.remove('hidden');
                    deliveryServiceInput.setAttribute('readonly', 'readonly'); // Baca saja
                    trackingNumberInput.setAttribute('readonly', 'readonly'); // Baca saja
                    break;

                case 'ready_for_pickup': // Barang siap dijemput (pickup)
                    statusSelect.disabled = false; // Aktifkan dropdown
                    statusSelect.querySelector(`option[value="ready_for_pickup"]`).style.display = 'block';
                    statusSelect.querySelector(`option[value="completed"]`).style.display = 'block'; // Bisa ke Selesai
                    updateStatusBtn.disabled = false;
                    updateStatusBtn.textContent = 'Perbarui Status';
                    break;
                
                case 'completed': // Order sudah selesai
                    statusSelect.querySelector(`option[value="completed"]`).style.display = 'block';
                    updateStatusBtn.textContent = 'Pesanan Selesai';
                    break;

                default:
                    // Status tidak dikenal, nonaktifkan semuanya
                    statusSelect.disabled = true;
                    updateStatusBtn.disabled = true;
                    updateStatusBtn.textContent = 'Status Tidak Dapat Diubah';
                    break;
            }

            // Atur kembali listener untuk dropdown status agar memanggil fungsi update
            // Ini akan memastikan input resi/layanan muncul/sembunyi saat mitra mengubah status
            statusSelect.removeEventListener('change', toggleShippingInputs); // Hapus yang lama jika ada
            statusSelect.addEventListener('change', () => {
                // Saat status berubah, panggil lagi logic untuk sesuaikan input resi
                // dan juga cek apakah tombol update harus aktif
                const newSelectedValue = statusSelect.value;
                if (newSelectedValue !== initialOrderStatus) {
                    updateStatusBtn.disabled = false;
                    updateStatusBtn.textContent = 'Perbarui Status';
                } else {
                    updateStatusBtn.disabled = true;
                    updateStatusBtn.textContent = 'Perbarui Status';
                }
                toggleShippingInputs(); // Panggil untuk menyesuaikan input resi/layanan
            });
            
            // Panggil ini untuk menyesuaikan input resi/layanan saat halaman dimuat
            // berdasarkan status awal yang terpilih di dropdown
            toggleShippingInputs(); 
        }
        
        updateStatusControls(); // Panggil fungsi utama saat DOMContentLoaded


        if (updateStatusBtn) {
            updateStatusBtn.addEventListener('click', function() {
                const newStatus = statusSelect.value;
                const deliveryService = deliveryServiceInput.value;
                const receiptNumber = trackingNumberInput.value;
                
                const actualDeliveryMethod = initialDeliveryMethod; // Ambil metode pengiriman asli dari hidden input

                if (!newStatus) {
                    showToast('Harap pilih status baru.', 'error');
                    return;
                }

                // Validasi sisi klien untuk nomor resi dan layanan jika kondisi tertentu terpenuhi
                // Kondisi disederhanakan karena toggleShippingInputs sudah mengatur required atribut
                if (deliveryServiceInput.hasAttribute('required') && !deliveryService) {
                    showToast('Layanan pengiriman wajib diisi.', 'error');
                    return;
                }
                if (trackingNumberInput.hasAttribute('required') && !receiptNumber) {
                    showToast('Nomor resi wajib diisi.', 'error');
                    return;
                }

                // Tambahan validasi: Jika user mencoba mengubah ke status yang tidak diizinkan
                // Contoh: dari 'completed' ke 'processing' (jika updateStatusControls men-disable option)
                // Ini juga akan divalidasi di backend, tapi validasi frontend lebih baik
                if (statusSelect.disabled) {
                    showToast('Status ini tidak dapat diubah secara manual.', 'error');
                    return;
                }
                
                updateStatusBtn.disabled = true;
                updateStatusBtn.textContent = 'Memperbarui...';

                axios.put(`/mitra/orders/${orderId}/status`, { 
                    status: newStatus,
                    delivery_method: actualDeliveryMethod, // Kirim metode pengiriman asli
                    delivery_service: deliveryService,
                    receipt_number: receiptNumber
                })
                    .then(response => {
                        if (response.data.success) {
                            showToast(response.data.message, 'success');
                            // Update tampilan status
                            document.getElementById('current-status-display').textContent = ucfirst(response.data.new_status.replace(/_/g, ' '));
                            const statusBadge = document.getElementById('current-status-badge');
                            if (statusBadge) {
                                statusBadge.className = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full';
                                if (response.data.new_status === 'completed') statusBadge.classList.add('bg-green-100', 'text-green-800');
                                else if (response.data.new_status === 'pending' || response.data.new_status === 'processing') statusBadge.classList.add('bg-yellow-100', 'text-yellow-800');
                                else if (response.data.new_status === 'shipped' || response.data.new_status === 'ready_for_pickup') statusBadge.classList.add('bg-blue-100', 'text-blue-800');
                                else if (response.data.new_status === 'cancelled' || response.data.new_status === 'failed') statusBadge.classList.add('bg-red-100', 'text-red-800');
                                else statusBadge.classList.add('bg-gray-100', 'text-gray-800');
                            }
                            // Update tampilan nomor resi dan layanan
                            const trackingNumberDisplayEl = document.getElementById('current-tracking-number-display');
                            const deliveryServiceDisplayEl = document.getElementById('current-delivery-service-display');
                            if (trackingNumberDisplayEl) {
                                trackingNumberDisplayEl.textContent = response.data.new_tracking_number || 'Belum tersedia';
                            }
                            if (deliveryServiceDisplayEl) {
                                deliveryServiceDisplayEl.textContent = response.data.new_delivery_service || 'Belum tersedia';
                            }
                            // PENTING: Setelah update berhasil, set initialOrderStatus yang baru
                            initialOrderStatus = response.data.new_status; // Perbarui status awal
                            updateStatusControls(); // Panggil lagi untuk menyesuaikan UI dengan status baru
                        } else {
                            showToast(response.data.message || 'Gagal memperbarui status.', 'error');
                        }
                    })
                    .catch(error => {
                        const errorMessage = error.response?.data?.message || error.message || 'Terjadi kesalahan saat berkomunikasi dengan server.';
                        showToast(errorMessage, 'error');
                        console.error('Update status error:', error.response || error);
                    })
                    .finally(() => {
                        updateStatusBtn.disabled = false;
                        updateStatusBtn.textContent = 'Perbarui Status';
                    });
            });
        }
    });

    function ucfirst(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
</script>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Detail Pesanan #{{ $order->id }}</h1>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        {{-- Ringkasan Pesanan --}}
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Ringkasan Pesanan</h2>
            <div class="space-y-2 text-gray-600">
                <p><strong>Status:</strong> 
                    <span id="current-status-badge" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        @if($order->status == 'completed') bg-green-100 text-green-800
                        @elseif($order->status == 'pending' || $order->status == 'processing') bg-yellow-100 text-yellow-800
                        @elseif($order->status == 'shipped' || $order->status == 'ready_for_pickup') bg-blue-100 text-blue-800
                        @elseif($order->status == 'cancelled' || $order->status == 'failed') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        <span id="current-status-display">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                    </span>
                </p>
                <p><strong>Tanggal Order:</strong> {{ $order->order_date->format('d F Y H:i') }}</p>
                {{-- Metode Pengiriman (Hanya tampilan, tidak bisa diubah) --}}
                <p><strong>Metode Pengiriman:</strong> <span id="current-delivery-method-display">{{ ucfirst(str_replace('_', ' ', $order->delivery_method ?? 'Tidak Diketahui')) }}</span></p>
                <input type="hidden" id="current-delivery-method-hidden" value="{{ $order->delivery_method ?? '' }}">
                
                {{-- Tampilan Layanan Pengiriman dan Nomor Resi dari relasi shipping --}}
                <p><strong>Layanan Pengiriman:</strong> <span id="current-delivery-service-display">{{ $order->shipping->delivery_service ?? 'Belum tersedia' }}</span></p>
                <p><strong>Nomor Resi:</strong> <span id="current-tracking-number-display">{{ $order->shipping->receipt_number ?? 'Belum tersedia' }}</span></p>
                <p><strong>Catatan:</strong> {{ $order->notes ?? '-' }}</p>
                @if ($order->voucher)
                <p><strong>Voucher Digunakan:</strong> {{ $order->voucher->voucher_code }} (Diskon: {{ $order->voucher->discount }}%)</p>
                @endif
                @if ($order->payment)
                <p><strong>Status Pembayaran:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment->midtrans_transaction_status ?? 'N/A')) }} (Tipe: {{ $order->payment->midtrans_payment_type ?? 'N/A' }})</p>
                @endif
            </div>
        </div>

        {{-- Detail Pelanggan & Pengiriman --}}
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Detail Pelanggan & Pengiriman</h2>
            <div class="space-y-2 text-gray-600">
                <p><strong>Nama Pelanggan:</strong> {{ $order->user->name ?? 'Pengguna Dihapus' }}</p>
                <p><strong>Email:</strong> {{ $order->user->email ?? 'N/A' }}</p>
                <p><strong>Telepon Pengiriman:</strong> {{ $order->shipping_phone }}</p>
                <p><strong>Alamat Pengiriman:</strong> {{ $order->shipping_address }}, {{ $order->shipping_city }} {{ $order->shipping_zip_code }}</p>
            </div>
        </div>
    </div>

    {{-- Item Pesanan --}}
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Item Pesanan</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Varian</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kuantitas</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Satuan</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($order->items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->product->name ?? 'Produk Dihapus' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->variant->name ?? 'N/A' }}
                                @if($item->variant && $item->variant->options_data)
                                    ({{ collect($item->variant->options_data)->pluck('value')->implode(' / ') }})
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ format_rupiah($item->price) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ format_rupiah($item->price * $item->quantity) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Tidak ada item dalam pesanan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Ringkasan Pembayaran & Update Status --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Ringkasan Pembayaran --}}
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Ringkasan Pembayaran</h2>
            <div class="space-y-2 text-gray-600">
                <p class="flex justify-between"><span>Subtotal:</span> <span>{{ format_rupiah($order->subtotal) }}</span></p>
                <p class="flex justify-between"><span>Biaya Pengiriman:</span> <span>{{ format_rupiah($order->shipping_cost) }}</span></p>
                <p class="flex justify-between"><span>Diskon:</span> <span>- {{ format_rupiah($order->discount_amount) }}</span></p>
                <p class="flex justify-between text-lg font-bold"><span>Total Pembayaran:</span> <span>{{ format_rupiah($order->total_price) }}</span></p>
            </div>
        </div>

        {{-- Update Status Pesanan --}}
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Perbarui Status Pesanan</h2>
            <div class="space-y-4">
                <label for="status-select" class="block text-sm font-medium text-gray-700">Pilih Status Baru:</label>
                <select id="status-select" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    {{-- Opsi status akan diisi dan difilter oleh JavaScript --}}
                </select>

                {{-- Metode Pengiriman (Hanya tampilan, tidak bisa diubah) --}}
                <p class="block text-sm font-medium text-gray-700 mt-4">Metode Pengiriman Saat Ini: <span class="font-semibold" id="display-delivery-method-text">{{ ucfirst(str_replace('_', ' ', $order->delivery_method ?? 'Tidak Diketahui')) }}</span></p>
                <input type="hidden" id="current-delivery-method-hidden" value="{{ $order->delivery_method ?? '' }}">
                
                {{-- Input untuk Layanan Pengiriman (Conditional) --}}
                <div id="delivery-service-group" class="mt-4 {{ ($order->delivery_method !== 'delivery' || ($order->status !== 'processing' && $order->status !== 'shipped')) ? 'hidden' : '' }}">
                    <label for="delivery-service-input" class="block text-sm font-medium text-gray-700">Layanan Pengiriman:</label>
                    <input type="text" id="delivery-service-input" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                           value="{{ $order->shipping->delivery_service ?? '' }}" placeholder="Contoh: JNE, J&T, SiCepat">
                </div>

                {{-- Input untuk Nomor Resi Pengiriman (Conditional) --}}
                <div id="tracking-number-group" class="mt-4 {{ ($order->delivery_method !== 'delivery' || ($order->status !== 'processing' && $order->status !== 'shipped')) ? 'hidden' : '' }}">
                    <label for="tracking-number-input" class="block text-sm font-medium text-gray-700">Nomor Resi Pengiriman:</label>
                    <input type="text" id="tracking-number-input" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                           value="{{ $order->shipping->receipt_number ?? '' }}" placeholder="Masukkan nomor resi">
                </div>

                <button id="update-status-btn" data-order-id="{{ $order->id }}" class="primary-btn w-full mt-3">Perbarui Status</button>
            </div>
            {{-- Notifikasi toast akan muncul di sini --}}
        </div>
    </div>
</div>
@endsection