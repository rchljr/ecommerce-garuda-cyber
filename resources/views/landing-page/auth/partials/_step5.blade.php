<div class="w-full mx-auto flex flex-col h-full">
    <div class="flex-shrink-0">
        <h1 class="text-2xl font-bold text-[#B20000] mt-6">Pembayaran</h1>
        <p class="text-lg text-gray-600 mt-1 mb-6">Detail pesanan Anda sudah benar. Silakan selesaikan pembayaran untuk
            mengaktifkan akun Anda.</p>
    </div>

    <div class="flex-grow space-y-6">
        <div class="bg-gray-50 rounded-lg p-6 border">
            @if(isset($order))
                @php
                    $userPackage = $order->user->userPackage ?? null;
                    $subscriptionPackage = $userPackage ? $userPackage->subscriptionPackage : null;
                @endphp
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="text-gray-600">Nama Paket</span>
                        <span
                            class="font-semibold text-gray-800">{{ optional($subscriptionPackage)->package_name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="text-gray-600">Durasi</span>
                        <span
                            class="font-semibold text-gray-800">{{ optional($userPackage)->plan_type == 'yearly' ? '12 Bulan' : '1 Bulan' }}</span>
                    </div>

                    {{-- Tampilkan rincian harga dan diskon --}}
                    @if(optional($userPackage)->plan_type == 'yearly' && optional($subscriptionPackage)->yearly_discount > 0)
                        <div class="flex justify-between items-center py-2 border-b">
                            <span class="text-gray-600">Harga Asli Tahunan</span>
                            <span
                                class="font-semibold text-gray-800 line-through">{{ format_rupiah(optional($subscriptionPackage)->yearly_price) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 text-green-600">
                            <span class="">Diskon ({{ optional($subscriptionPackage)->yearly_discount }}%)</span>
                            @php
                                $discountAmount = (optional($subscriptionPackage)->yearly_price * optional($subscriptionPackage)->yearly_discount) / 100;
                            @endphp
                            <span class="font-semibold">- {{ format_rupiah($discountAmount) }}</span>
                        </div>
                    @endif
                </div>
                {{-- Total Pembayaran --}}
                <div class="flex justify-between items-center pt-4 mt-4 border-t border-gray-300">
                    <span class="text-lg font-bold text-gray-900">Total Pembayaran</span>
                    <span id="total-payment"
                        class="text-lg font-bold text-red-600">{{ format_rupiah($order->total_price) }}</span>
                </div>
            @else
                <p class="text-center text-gray-500">Detail pembayaran tidak ditemukan.</p>
            @endif
        </div>

        {{-- Logika voucher bisa ditambahkan di sini jika perlu --}}

        <div class="flex-shrink-0 pt-4">
            {{-- Tambahkan kondisi untuk hanya menampilkan tombol jika ada order --}}
            @if(isset($order))
                <button id="pay-button"
                    class="w-full bg-[#B20000] text-white font-bold py-3 rounded-lg text-lg hover:bg-[#900000] transition-colors">
                    Bayar Sekarang
                </button>
            @endif
        </div>
    </div>
</div>

@push('scripts')
    <script
        src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            const payButton = document.getElementById('pay-button');

            if (payButton) {
                payButton.addEventListener('click', async function () {
                    // Ubah teks tombol dan nonaktifkan untuk mencegah klik ganda
                    payButton.disabled = true;
                    payButton.textContent = 'Memproses...';

                    try {
                        // 1. Ambil Snap Token dari backend secara dinamis
                        const response = await fetch("{{ route('payment.token') }}");

                        if (!response.ok) {
                            throw new Error('Gagal mendapatkan token pembayaran.');
                        }

                        const data = await response.json();
                        const snapToken = data.snap_token;

                        if (!snapToken) {
                            throw new Error('Token pembayaran tidak valid.');
                        }

                        // 2. Tampilkan popup pembayaran Midtrans dengan token yang baru didapat
                        snap.pay(snapToken, {
                            onSuccess: function (result) {
                                console.log('Payment Success:', result);
                                // Redirect ke halaman sukses atau dashboard
                                window.location.href = "{{ route('mitra.dashboard') }}?status=success";
                            },
                            onPending: function (result) {
                                console.log('Payment Pending:', result);
                                alert("Pembayaran Anda sedang menunggu penyelesaian.");
                                window.location.href = "{{ route('mitra.dashboard') }}?status=pending";
                            },
                            onError: function (result) {
                                console.error('Payment Error:', result);
                                alert("Terjadi kesalahan saat memproses pembayaran.");
                                // Kembalikan tombol ke keadaan semula
                                payButton.disabled = false;
                                payButton.textContent = 'Bayar Sekarang';
                            },
                            onClose: function () {
                                console.log('Popup ditutup tanpa menyelesaikan pembayaran.');
                                // Kembalikan tombol ke keadaan semula jika popup ditutup
                                payButton.disabled = false;
                                payButton.textContent = 'Bayar Sekarang';
                            }
                        });

                    } catch (error) {
                        console.error('AJAX Error:', error);
                        alert(error.message || 'Tidak dapat memproses pembayaran. Silakan muat ulang halaman.');
                        // Kembalikan tombol ke keadaan semula
                        payButton.disabled = false;
                        payButton.textContent = 'Bayar Sekarang';
                    }
                });
            }
        });
    </script>
@endpush