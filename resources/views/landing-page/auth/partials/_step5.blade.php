@extends('layouts.auth')
@section('title', 'Pembayaran')

@section('content')
    <div class="flex flex-col h-full items-center justify-center p-4 sm:p-6">
        <div class="w-full max-w-2xl mx-auto">
            <div class="bg-white rounded-xl shadow-lg p-5 md:p-8 border border-gray-200">
                <div class="text-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Selesaikan Pembayaran</h1>
                    <p class="text-gray-500 mt-1 text-sm">Satu langkah lagi untuk mengaktifkan akun Anda.</p>
                </div>

                @if(isset($order) && $order->userPackage && $order->userPackage->subscriptionPackage)
                    {{-- Container Utama --}}
                    <div id="payment-container">

                        {{-- Bagian 1: Detail Pesanan & Harga --}}
                        <div class="mb-6">
                            <div class="space-y-2 text-sm p-4 bg-gray-50 rounded-lg border">
                                <div class="flex justify-between items-center">
                                    <span class="font-semibold text-gray-600">Nomor Pesanan:</span>
                                    <span class="font-mono text-gray-800">{{ $order->id }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="font-semibold text-gray-600">Paket Langganan:</span>
                                    <span
                                        class="text-gray-800">{{ $order->userPackage->subscriptionPackage->package_name }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="font-semibold text-gray-600">Periode Tagihan:</span>
                                    <span class="capitalize text-gray-800">{{ $order->userPackage->plan_type }}</span>
                                </div>
                                <div class="pt-2 border-t flex justify-between items-center">
                                    <span class="font-semibold text-gray-600">Harga Asli:</span>
                                    <span id="original-price">Rp
                                        {{ number_format($order->userPackage->price_paid, 0, ',', '.') }}</span>
                                </div>
                                <div id="discount-details"
                                    class="flex justify-between items-center text-green-600 font-semibold {{ $order->voucher ? '' : 'hidden' }}">
                                    <span>Diskon ("{{ $order->voucher->voucher_code ?? '' }}"):</span>
                                    <span id="discount-amount">
                                        {{ format_diskon($order->voucher->discount ?? 0, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Bagian 2: Form Voucher --}}
                        <div class="mb-6">
                            <form id="voucher-form">
                                <label for="voucher_code" class="block text-sm font-medium text-gray-700 mb-1">Punya Kode
                                    Voucher?</label>
                                <div class="flex space-x-2">
                                    <input type="text" id="voucher_code" name="voucher_code"
                                        value="{{ $order->voucher->voucher_code ?? '' }}"
                                        class="flex-grow block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                    <button type="submit" id="apply-voucher-btn"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-600 hover:bg-gray-700">Terapkan</button>
                                </div>
                            </form>
                            <div class="flex justify-between items-center mt-2">
                                <div id="voucher-message" class="text-xs"></div>
                                <button type="button" id="remove-voucher-btn"
                                    class="text-xs text-red-600 hover:underline font-semibold hidden">Hapus Voucher</button>
                            </div>
                        </div>

                        {{-- Garis Pemisah dengan Total Harga --}}
                        <div class="my-6 py-4 border-t border-b">
                            <div class="flex justify-between items-center">
                                <span class="text-base font-bold">Total Pembayaran:</span>
                                <span id="final-price" class="text-2xl font-bold text-red-600">Rp
                                    {{ number_format($order->total_price, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        {{-- Bagian 3: Pilihan Metode Pembayaran --}}
                        <form id="payment-form">
                            <h3 class="text-lg font-semibold mb-4 text-gray-700">Pilih Metode Pembayaran</h3>
                            <div class="grid grid-cols-2 gap-4">
                                {{-- Opsi BCA VA --}}
                                <label
                                    class="relative flex flex-col items-center justify-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-all duration-200 peer-checked:border-red-500">
                                    <input type="radio" name="payment_method" value="bca_va"
                                        class="absolute opacity-0 w-full h-full peer">
                                    <img src="{{ asset('images/bca.png')}}" class="h-6 mb-2">
                                    <span class="text-sm font-medium text-center text-gray-600">BCA Virtual Account</span>
                                    <div
                                        class="absolute top-2 right-2 w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-red-600 peer-checked:border-red-600 flex items-center justify-center">
                                        <svg class="w-3 h-3 text-white hidden peer-checked:block" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </label>
                                {{-- Opsi BNI VA --}}
                                <label
                                    class="relative flex flex-col items-center justify-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-all duration-200 peer-checked:border-red-500">
                                    <input type="radio" name="payment_method" value="bni_va"
                                        class="absolute opacity-0 w-full h-full peer">
                                    <img src="https://upload.wikimedia.org/wikipedia/id/thumb/5/55/BNI_logo.svg/1280px-BNI_logo.svg.png"
                                        class="h-6 mb-2">
                                    <span class="text-sm font-medium text-center text-gray-600">BNI Virtual Account</span>
                                    <div
                                        class="absolute top-2 right-2 w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-red-600 peer-checked:border-red-600 flex items-center justify-center">
                                        <svg class="w-3 h-3 text-white hidden peer-checked:block" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </label>
                                {{-- Opsi GoPay --}}
                                <label
                                    class="relative flex flex-col items-center justify-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-all duration-200 peer-checked:border-red-500">
                                    <input type="radio" name="payment_method" value="gopay"
                                        class="absolute opacity-0 w-full h-full peer">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/86/Gopay_logo.svg/2560px-Gopay_logo.svg.png"
                                        class="h-5 mb-2">
                                    <span class="text-sm font-medium text-center text-gray-600">GoPay</span>
                                    <div
                                        class="absolute top-2 right-2 w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-red-600 peer-checked:border-red-600 flex items-center justify-center">
                                        <svg class="w-3 h-3 text-white hidden peer-checked:block" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </label>
                                {{-- Opsi QRIS --}}
                                <label
                                    class="relative flex flex-col items-center justify-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-all duration-200 peer-checked:border-red-500">
                                    <input type="radio" name="payment_method" value="qris"
                                        class="absolute opacity-0 w-full h-full peer">
                                    <img src="{{ asset('images/qris.png')}}" class="h-6 mb-2">
                                    <span class="text-sm font-medium text-center text-gray-600">QRIS</span>
                                    <div
                                        class="absolute top-2 right-2 w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:bg-red-600 peer-checked:border-red-600 flex items-center justify-center">
                                        <svg class="w-3 h-3 text-white hidden peer-checked:block" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </label>
                            </div>
                            <div class="mt-8">
                                <button type="submit" id="pay-button"
                                    class="w-full bg-red-600 text-white font-bold px-6 py-3 rounded-lg hover:bg-red-700 text-base transition-all duration-300 shadow-lg">
                                    Lanjutkan Pembayaran
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Area untuk menampilkan instruksi pembayaran --}}
                    <div id="payment-instructions" class="mt-6 hidden"></div>

                @else
                    <div class="text-center p-8 bg-red-50 text-red-700 rounded-lg">
                        <h3 class="font-bold text-lg">Data Pesanan Tidak Lengkap</h3>
                        <p class="mt-2">Tidak dapat menampilkan detail pembayaran. Silakan hubungi admin.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            // --- Elemen UI ---
            const paymentContainer = document.getElementById('payment-container');
            const paymentForm = document.getElementById('payment-form');
            const payButton = document.getElementById('pay-button');
            const instructionsDiv = document.getElementById('payment-instructions');

            // --- Event Listener untuk Form Pembayaran ---
            paymentForm.addEventListener('submit', function (e) {
                e.preventDefault();
                payButton.disabled = true;
                payButton.innerHTML = 'Memproses...';
                instructionsDiv.innerHTML = '';

                const formData = new FormData(paymentForm);
                const selectedMethod = formData.get('payment_method');

                if (!selectedMethod) {
                    alert('Silakan pilih metode pembayaran terlebih dahulu.');
                    payButton.disabled = false;
                    payButton.innerHTML = 'Lanjutkan Pembayaran';
                    return;
                }

                axios.post('{{ route("payment.charge") }}', {
                    payment_method: selectedMethod,
                    _token: '{{ csrf_token() }}'
                })
                    .then(response => handlePaymentResponse(response.data))
                    .catch(function (error) {
                        // PERBAIKAN: Error handling yang lebih detail
                        let errorMessage = 'Terjadi kesalahan.';
                        if (error.response) {
                            // Server merespons dengan status error (4xx atau 5xx)
                            console.error('Error Data:', error.response.data);
                            if (error.response.data && error.response.data.error) {
                                errorMessage = error.response.data.error;
                            } else if (error.response.data && error.response.data.message) {
                                // Menangkap pesan error default dari Laravel
                                errorMessage = `Error: ${error.response.data.message}`;
                            } else {
                                errorMessage = `Terjadi error dengan kode: ${error.response.status}. Silakan cek log server untuk detail.`;
                            }
                        } else if (error.request) {
                            // Request dibuat tapi tidak ada respons
                            errorMessage = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
                        } else {
                            // Error lain saat membuat request
                            errorMessage = 'Terjadi kesalahan saat menyiapkan permintaan.';
                        }

                        alert(errorMessage);
                        payButton.disabled = false;
                        payButton.innerHTML = 'Lanjutkan Pembayaran';
                    });
            });

            // --- Fungsi untuk Menangani Respons dari Core API ---
            function handlePaymentResponse(data) {
                let html = '';
                paymentContainer.style.display = 'none'; // Sembunyikan form awal
                instructionsDiv.classList.remove('hidden'); // Tampilkan area instruksi

                if (data.va_numbers && data.va_numbers.length > 0) {
                    const va = data.va_numbers[0];
                    html = `
                    <div class="p-4 border-l-4 border-blue-500 bg-blue-50">
                        <h4 class="font-bold text-lg">Instruksi Pembayaran ${va.bank.toUpperCase()} VA</h4>
                        <p class="mt-2 text-sm">Silakan selesaikan pembayaran Anda ke nomor Virtual Account berikut:</p>
                        <div class="my-4 p-3 bg-white border rounded-lg text-center flex items-center justify-between">
                            <p class="text-2xl font-bold tracking-wider">${va.va_number}</p>
                            <button onclick="copyToClipboard('${va.va_number}')" class="ml-4 text-sm text-blue-600 hover:underline">Salin</button>
                        </div>
                        <p class="text-sm">Total Tagihan: <strong class="font-bold">${formatRupiah(data.gross_amount)}</strong></p>
                        <p class="text-xs mt-2">Batas waktu pembayaran: ${new Date(data.expiry_time).toLocaleString('id-ID')}</p>
                    </div>
                `;
                } else if (data.actions && data.actions.some(action => action.name === 'generate-qr-code')) {
                    const qrCodeUrl = data.actions.find(action => action.name === 'generate-qr-code').url;
                    html = `
                    <div class="p-4 border-l-4 border-green-500 bg-green-50 text-center">
                        <h4 class="font-bold text-lg">Instruksi Pembayaran QRIS</h4>
                        <p class="mt-2 text-sm">Pindai kode QR di bawah ini menggunakan aplikasi e-wallet Anda.</p>
                        <div class="my-4 flex justify-center">
                            <img src="${qrCodeUrl}" alt="QR Code Pembayaran" class="w-56 h-56">
                        </div>
                        <p class="text-sm">Total Tagihan: <strong class="font-bold">${formatRupiah(data.gross_amount)}</strong></p>
                    </div>
                `;
                } else if (data.actions && data.actions.some(action => action.name === 'deeplink-redirect')) {
                    const deepLink = data.actions.find(action => action.name === 'deeplink-redirect').url;
                    html = `
                    <div class="p-4 border-l-4 border-cyan-500 bg-cyan-50 text-center">
                        <h4 class="font-bold text-lg">Instruksi Pembayaran GoPay</h4>
                        <p class="mt-2 text-sm">Klik tombol di bawah untuk membuka aplikasi Gojek dan menyelesaikan pembayaran.</p>
                        <a href="${deepLink}" class="inline-block mt-4 bg-cyan-500 text-white font-bold px-6 py-3 rounded-lg">Buka Aplikasi Gojek</a>
                    </div>
                `;
                }

                instructionsDiv.innerHTML = html;
            }

            // --- Fungsi Bantuan ---
            function formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
            }

            window.copyToClipboard = function (text) {
                navigator.clipboard.writeText(text).then(() => {
                    alert('Nomor VA berhasil disalin!');
                }, () => {
                    alert('Gagal menyalin nomor VA.');
                });
            }

            // --- Logika Voucher (tetap sama) ---
            const voucherForm = document.getElementById('voucher-form');
            const voucherInput = document.getElementById('voucher_code');
            const applyBtn = document.getElementById('apply-voucher-btn');
            const removeBtn = document.getElementById('remove-voucher-btn');
            const voucherMessage = document.getElementById('voucher-message');
            const finalPriceEl = document.getElementById('final-price');
            const discountDetailsEl = document.getElementById('discount-details');
            const discountAmountEl = document.getElementById('discount-amount');

            function setVoucherUIState(state, data = {}) {
                voucherMessage.textContent = data.message || '';
                if (state === 'VOUCHER_APPLIED') {
                    voucherMessage.className = 'text-xs text-green-600';
                    voucherInput.disabled = true;
                    applyBtn.classList.add('hidden');
                    removeBtn.classList.remove('hidden');
                    discountDetailsEl.classList.remove('hidden');
                    discountAmountEl.textContent = '- ' + formatRupiah(data.discount_amount || 0);
                    finalPriceEl.textContent = formatRupiah(data.final_price || 0);
                } else if (state === 'NO_VOUCHER') {
                    voucherMessage.className = 'text-xs text-blue-600';
                    voucherInput.disabled = false;
                    voucherInput.value = '';
                    applyBtn.classList.remove('hidden');
                    removeBtn.classList.add('hidden');
                    discountDetailsEl.classList.add('hidden');
                    finalPriceEl.textContent = formatRupiah(data.final_price || 0);
                } else if (state === 'ERROR') {
                    voucherMessage.className = 'text-xs text-red-600';
                }
            }

            const initialVoucher = @json($order->voucher);
            if (initialVoucher) {
                setVoucherUIState('VOUCHER_APPLIED', {
                    message: 'Voucher "' + initialVoucher.voucher_code + '" diterapkan.',
                    discount_amount: initialVoucher.discount,
                    final_price: {{ $order->total_price }}
            });
            }

            voucherForm.addEventListener('submit', function (e) {
                e.preventDefault();
                setVoucherUIState('LOADING', { message: 'Memvalidasi voucher...' });
                axios.post('{{ route("payment.applyVoucher") }}', {
                    voucher_code: voucherInput.value,
                    _token: '{{ csrf_token() }}'
                })
                    .then(response => setVoucherUIState('VOUCHER_APPLIED', response.data))
                    .catch(error => setVoucherUIState('ERROR', { message: error.response.data.error || 'Terjadi kesalahan.' }));
            });

            removeBtn.addEventListener('click', function (e) {
                e.preventDefault();
                setVoucherUIState('LOADING', { message: 'Menghapus voucher...' });
                axios.post('{{ route("payment.removeVoucher") }}', { _token: '{{ csrf_token() }}' })
                    .then(response => setVoucherUIState('NO_VOUCHER', response.data))
                    .catch(error => setVoucherUIState('ERROR', { message: error.response.data.error || 'Gagal menghapus voucher.' }));
            });
        });
    </script>
@endpush