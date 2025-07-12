@extends('layouts.customer')
@section('title', 'Checkout')

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Gaya untuk voucher yang tidak bisa dipilih */
        .voucher-item.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background-color: #f8f9fa;
        }

        .voucher-item.disabled:hover {
            background-color: #f8f9fa;
            /* Mencegah perubahan warna saat hover */
        }

        /* Gaya untuk pilihan pembayaran */
        .payment-option-label {
            transition: all 0.2s ease-in-out;
        }

        .payment-option-label.selected {
            border-color: #ef4444;
            /* red-500 */
            background-color: #fef2f2;
            /* red-50 */
        }
    </style>
@endpush

@section('content')
    @php
        $currentSubdomain = request()->route('subdomain');
    @endphp
    <div class="bg-gray-100 py-12">
        <div class="container mx-auto px-4">

            {{-- Kontainer utama yang akan disembunyikan setelah pembayaran berhasil --}}
            <div id="checkout-container">
                <h1 class="text-3xl font-bold text-gray-800 mb-8">Checkout</h1>
                <form id="checkout-form" onsubmit="return false;">
                    @csrf
                    @foreach ($checkoutItems as $item)
                        <input type="hidden" name="items[]" value="{{ $item->id ?? $item['id'] }}">
                    @endforeach

                    {{-- Input tersembunyi untuk menyimpan data penting --}}
                    <input type="hidden" name="origin_id" id="origin_id" value="{{ $originId }}">
                    <input type="hidden" name="destination_id" id="destination_id">
                    <input type="hidden" name="total_weight_kg" id="total_weight_kg" value="{{ $totalWeightInKg }}">
                    <input type="hidden" name="shipping_service" id="shipping_service_input">
                    <input type="hidden" name="shipping_cost" id="shipping_cost_input">
                    <input type="hidden" name="voucher_id" id="voucher_id_input">
                    <input type="hidden" name="discount_amount" id="discount_amount_input">
                    <input type="hidden" name="delivery_method" id="delivery_method_input" value="pickup">

                    <div class="flex flex-col lg:flex-row gap-8">
                        <div class="w-full lg:w-2/3 space-y-8">
                            <div class="bg-white rounded-lg shadow-md p-6">
                                <h2 class="text-xl font-bold mb-4">Metode Pengambilan</h2>
                                <div class="flex flex-col sm:flex-row gap-4" id="delivery-method-options">
                                    <label
                                        class="flex-1 border rounded-lg p-4 cursor-pointer has-[:checked]:bg-red-50 has-[:checked]:border-red-500">
                                        <input type="radio" name="delivery_method_option" value="pickup" class="hidden"
                                            checked>
                                        <div class="ml-3">
                                            <p class="font-bold">Ambil di Toko</p>
                                            <p class="text-sm text-gray-500">Ambil pesanan langsung di lokasi mitra.</p>
                                        </div>
                                    </label>
                                    <label
                                        class="flex-1 border rounded-lg p-4 cursor-pointer has-[:checked]:bg-red-50 has-[:checked]:border-red-500">
                                        <input type="radio" name="delivery_method_option" value="ship" class="hidden">
                                        <div class="ml-3">
                                            <p class="font-bold">Kirim ke Alamat</p>
                                            <p class="text-sm text-gray-500">Pesanan akan dikirimkan ke alamat Anda.</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div id="shipping-details-container" class="space-y-8 hidden">
                                <div class="bg-white rounded-lg shadow-md p-6">
                                    <h2 class="text-xl font-bold mb-4">Alamat Pengiriman</h2>
                                    <div class="relative">
                                        <label for="destination_search" class="block text-sm font-medium text-gray-700">Cari
                                            Kecamatan Tujuan</label>
                                        <input type="text" id="destination_search"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                            placeholder="Ketik nama kecamatan">
                                        <div id="destination-results"
                                            class="absolute z-20 w-full mt-1 border rounded-md bg-white max-h-48 overflow-y-auto shadow-lg hidden">
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <label for="alamat" class="block text-sm font-medium">Alamat Lengkap</label>
                                        <textarea name="alamat" id="alamat" rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">{{ $customer->alamat ?? '' }}</textarea>
                                    </div>
                                </div>
                                <div class="bg-white rounded-lg shadow-md p-6">
                                    <h2 class="text-xl font-bold mb-4">Pilih Pengiriman</h2>
                                    <div id="shipping-options-container" class="space-y-3">
                                        <p class="text-gray-500 text-sm">Silakan cari dan pilih alamat tujuan terlebih
                                            dahulu.</p>
                                    </div>
                                </div>
                            </div>

                            <div id="pickup-details-container" class="space-y-8">
                                <div class="bg-white rounded-lg shadow-md p-6">
                                    <h2 class="text-xl font-bold mb-4">Lokasi Pengambilan</h2>
                                    <div class="space-y-2 text-gray-700 text-sm">
                                        <p class="font-semibold text-base">{{ $shop->shop_name ?? 'Nama Toko Mitra' }}</p>
                                        @if($contact)
                                            <div class="mt-2 border-t pt-2">
                                                <p>{{ $contact->address_line1 }}</p>
                                                <p>{{ $contact->city }}{{ $contact->state ? ', ' . $contact->state : '' }}
                                                    {{ $contact->postal_code ?? '' }}</p>
                                                <p class="mt-2"><strong>Telepon:</strong> {{ $contact->phone ?? '-' }}</p>
                                                <p><strong>Jam Buka:</strong> {{ $contact->working_hours ?? '-' }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="w-full lg:w-1/3">
                            <div class="bg-white rounded-lg shadow-md p-6 sticky top-24 space-y-4">
                                <h2 class="text-xl font-bold border-b pb-4">Ringkasan Pesanan</h2>
                                <div class="border-b pb-4 space-y-2">
                                    <div class="flex justify-between"><span>Subtotal Produk</span><span id="subtotal-amount"
                                            data-value="{{ $subtotal }}">{{ format_rupiah($subtotal) }}</span></div>
                                    <div id="shipping-cost-summary-row" class="flex justify-between hidden">
                                        <span>Ongkos Kirim</span><span id="shipping-cost-display">Rp 0</span>
                                    </div>
                                    <div id="voucher-discount-row" class="flex justify-between text-green-600 hidden">
                                        <span>Diskon Voucher</span><span id="voucher-discount-display">- Rp 0</span>
                                    </div>
                                </div>
                                <div class="flex justify-between font-bold text-lg pt-2">
                                    <span>Total</span><span id="total-amount">{{ format_rupiah($subtotal) }}</span>
                                </div>

                                <div class="pt-4 border-t">
                                    <button type="button" id="btn-select-voucher"
                                        class="w-full text-left bg-gray-50 border rounded-md p-3 text-sm text-gray-700 hover:bg-gray-100">Pilih
                                        Voucher</button>
                                    <div id="selected-voucher-info"
                                        class="hidden mt-2 p-3 bg-green-50 border-green-200 rounded-md text-xs flex justify-between items-center">
                                        <span id="selected-voucher-text"></span>
                                        <button type="button" id="btn-remove-voucher"
                                            class="text-red-500 hover:text-red-700 font-bold text-lg">&times;</button>
                                    </div>
                                </div>

                                <div class="pt-4 border-t">
                                    <h3 class="text-base font-semibold mb-3 text-gray-700">Pilih Metode Pembayaran</h3>
                                    <div class="grid grid-cols-2 gap-3">
                                        <label
                                            class="payment-option-label relative flex flex-col items-center justify-center p-3 border-2 rounded-lg cursor-pointer hover:bg-gray-50">
                                            <input type="radio" name="payment_method" value="bca_va"
                                                class="absolute opacity-0 w-full h-full peer">
                                            <img src="{{ asset('images/bca.png')}}" class="h-5 mb-1" alt="BCA">
                                            <span class="text-xs font-medium text-center text-gray-600">BCA VA</span>
                                        </label>
                                        <label
                                            class="payment-option-label relative flex flex-col items-center justify-center p-3 border-2 rounded-lg cursor-pointer hover:bg-gray-50">
                                            <input type="radio" name="payment_method" value="bni_va"
                                                class="absolute opacity-0 w-full h-full peer">
                                            <img src="https://upload.wikimedia.org/wikipedia/id/thumb/5/55/BNI_logo.svg/1280px-BNI_logo.svg.png"
                                                class="h-5 mb-1" alt="BNI">
                                            <span class="text-xs font-medium text-center text-gray-600">BNI VA</span>
                                        </label>
                                        <label
                                            class="payment-option-label relative flex flex-col items-center justify-center p-3 border-2 rounded-lg cursor-pointer hover:bg-gray-50">
                                            <input type="radio" name="payment_method" value="qris"
                                                class="absolute opacity-0 w-full h-full peer">
                                            <img src="{{ asset('images/qris.png')}}" class="h-5 mb-1" alt="QRIS">
                                            <span class="text-xs font-medium text-center text-gray-600">QRIS</span>
                                        </label>
                                        <label
                                            class="payment-option-label relative flex flex-col items-center justify-center p-3 border-2 rounded-lg cursor-pointer hover:bg-gray-50">
                                            <input type="radio" name="payment_method" value="gopay"
                                                class="absolute opacity-0 w-full h-full peer">
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/86/Gopay_logo.svg/2560px-Gopay_logo.svg.png"
                                                class="h-4 mb-1" alt="GoPay">
                                            <span class="text-xs font-medium text-center text-gray-600">GoPay</span>
                                        </label>
                                    </div>
                                </div>

                                <button type="submit" id="btn-process-payment"
                                    class="w-full mt-6 bg-gray-800 text-white font-bold py-3 rounded-lg hover:bg-black transition-colors">Bayar
                                    Sekarang</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div id="payment-instructions" class="mt-6 hidden w-full max-w-2xl mx-auto"></div>
        </div>
    </div>

    <div id="voucher-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md m-4">
            <div class="p-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-bold">Pilih Voucher</h3>
                <button type="button" id="btn-close-modal" class="text-2xl">&times;</button>
            </div>
            <div id="voucher-list-container" class="p-4 max-h-96 overflow-y-auto space-y-3">
                @forelse($vouchers as $voucher)
                    <div class="voucher-item border rounded-lg p-3 cursor-pointer hover:bg-gray-50" data-id="{{ $voucher->id }}"
                        data-code="{{ $voucher->voucher_code }}" data-discount-percent="{{ $voucher->discount }}"
                        data-min-spending="{{ $voucher->min_spending }}">
                        <p class="font-bold text-red-600">{{ $voucher->voucher_code }} (Diskon {{ $voucher->discount }}%)</p>
                        <p class="text-sm">{{ $voucher->description }}</p>
                        <p class="text-xs text-gray-500 mt-1">Min. belanja {{ format_rupiah($voucher->min_spending) }}</p>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-8">Tidak ada voucher yang tersedia.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- STATE MANAGEMENT ---
            const subtotal = parseFloat(document.getElementById('subtotal-amount').dataset.value);
            let shippingCost = 0;
            let voucherDiscount = 0;
            let selectedVoucher = null;
            let searchTimeout;

            // --- ELEMENTS ---
            const checkoutContainer = document.getElementById('checkout-container');
            const paymentInstructions = document.getElementById('payment-instructions');
            const checkoutForm = document.getElementById('checkout-form');
            const deliveryMethodOptions = document.getElementById('delivery-method-options');
            const shippingDetailsContainer = document.getElementById('shipping-details-container');
            const pickupDetailsContainer = document.getElementById('pickup-details-container');
            const searchInput = document.getElementById('destination_search');
            const resultsContainer = document.getElementById('destination-results');
            const shippingOptionsContainer = document.getElementById('shipping-options-container');
            const shippingCostDisplay = document.getElementById('shipping-cost-display');
            const shippingCostSummaryRow = document.getElementById('shipping-cost-summary-row');
            const voucherDiscountRow = document.getElementById('voucher-discount-row');
            const voucherDiscountDisplay = document.getElementById('voucher-discount-display');
            const totalAmountDisplay = document.getElementById('total-amount');
            const voucherModal = document.getElementById('voucher-modal');
            const btnSelectVoucher = document.getElementById('btn-select-voucher');
            const btnCloseModal = document.getElementById('btn-close-modal');
            const selectedVoucherInfo = document.getElementById('selected-voucher-info');
            const selectedVoucherText = document.getElementById('selected-voucher-text');
            const btnRemoveVoucher = document.getElementById('btn-remove-voucher');
            const paymentOptionLabels = document.querySelectorAll('.payment-option-label');

            // --- HELPER FUNCTIONS ---
            const formatRupiah = (number) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
            const copyToClipboard = (text) => {
                navigator.clipboard.writeText(text).then(() => {
                    Swal.fire({ icon: 'success', title: 'Berhasil Disalin!', showConfirmButton: false, timer: 1500 });
                }, () => {
                    Swal.fire({ icon: 'error', title: 'Gagal Menyalin', text: 'Browser Anda mungkin tidak mendukung fitur ini.' });
                });
            };

            function updateTotals() {
                const totalBelanja = subtotal + shippingCost;

                if (selectedVoucher) {
                    const discountPercent = parseFloat(selectedVoucher.dataset.discountPercent);
                    voucherDiscount = (totalBelanja * discountPercent) / 100;
                } else {
                    voucherDiscount = 0;
                }

                const finalTotal = totalBelanja - voucherDiscount;

                shippingCostDisplay.textContent = formatRupiah(shippingCost);

                if (voucherDiscount > 0) {
                    voucherDiscountDisplay.textContent = `- ${formatRupiah(voucherDiscount)}`;
                    voucherDiscountRow.classList.remove('hidden');
                } else {
                    voucherDiscountRow.classList.add('hidden');
                }

                totalAmountDisplay.textContent = formatRupiah(finalTotal > 0 ? finalTotal : 0);
                document.getElementById('shipping_cost_input').value = shippingCost;
                document.getElementById('discount_amount_input').value = voucherDiscount;
                updateVoucherEligibility();
            }

            // --- DELIVERY LOGIC ---
            deliveryMethodOptions.addEventListener('change', (e) => {
                const method = e.target.value;
                document.getElementById('delivery_method_input').value = method;

                if (method === 'ship') {
                    shippingDetailsContainer.classList.remove('hidden');
                    pickupDetailsContainer.classList.add('hidden');
                    shippingCostSummaryRow.classList.remove('hidden');
                } else {
                    shippingDetailsContainer.classList.add('hidden');
                    pickupDetailsContainer.classList.remove('hidden');
                    shippingCostSummaryRow.classList.add('hidden');
                    shippingCost = 0;
                    shippingOptionsContainer.innerHTML = '<p class="text-gray-500 text-sm">Silakan cari dan pilih alamat tujuan terlebih dahulu.</p>';
                    searchInput.value = '';
                    document.getElementById('destination_id').value = '';
                }
                updateTotals();
            });

            shippingOptionsContainer.addEventListener('change', (e) => {
                if (e.target.name === 'shipping_option') {
                    shippingCost = parseFloat(e.target.value);
                    document.getElementById('shipping_service_input').value = e.target.dataset.serviceName;
                    updateTotals();
                }
            });

            searchInput.addEventListener('keyup', () => {
                clearTimeout(searchTimeout);
                const keyword = searchInput.value;
                if (keyword.length < 3) {
                    resultsContainer.innerHTML = '';
                    resultsContainer.classList.add('hidden');
                    return;
                }
                resultsContainer.classList.remove('hidden');
                resultsContainer.innerHTML = '<div class="p-3 text-sm text-gray-500">Mencari...</div>';

                searchTimeout = setTimeout(() => {
                    axios.post("{{ route('tenant.checkout.search_destination', ['subdomain' => $currentSubdomain]) }}", { keyword: keyword })
                        .then(response => {
                            resultsContainer.innerHTML = '';
                            if (response.data && Array.isArray(response.data) && response.data.length > 0) {
                                response.data.forEach(location => {
                                    const item = document.createElement('div');
                                    item.className = 'p-3 text-sm cursor-pointer hover:bg-gray-100 border-b';
                                    item.textContent = `${location.subdistrict_name}, ${location.city_name}, ${location.province_name}`;
                                    item.addEventListener('click', () => selectDestination(location.id, item.textContent));
                                    resultsContainer.appendChild(item);
                                });
                            } else {
                                resultsContainer.innerHTML = '<div class="p-3 text-sm text-gray-500">Lokasi tidak ditemukan.</div>';
                            }
                        })
                        .catch(() => resultsContainer.innerHTML = '<div class="p-3 text-sm text-red-500">Gagal mencari lokasi.</div>');
                }, 500);
            });

            function selectDestination(id, name) {
                document.getElementById('destination_id').value = id;
                searchInput.value = name;
                resultsContainer.innerHTML = '';
                resultsContainer.classList.add('hidden');
                calculateShippingCost(id);
            }

            function calculateShippingCost(destinationId) {
                shippingOptionsContainer.innerHTML = '<p class="text-sm text-gray-500">Menghitung ongkos kirim...</p>';
                const originId = document.getElementById('origin_id').value;
                const weight = document.getElementById('total_weight_kg').value;

                axios.post("{{ route('tenant.checkout.calculate_shipping', ['subdomain' => $currentSubdomain]) }}", {
                    origin_id: parseInt(originId),
                    destination_id: parseInt(destinationId),
                    weight: parseFloat(weight)
                })
                    .then(response => {
                        shippingOptionsContainer.innerHTML = '';
                        const data = response.data;
                        if (data && Array.isArray(data) && data.length > 0) {
                            data.forEach(courier => {
                                if (courier.costs && Array.isArray(courier.costs)) {
                                    courier.costs.forEach(cost => {
                                        const id = `${courier.code}-${cost.service}`.replace(/\s+/g, '-');
                                        shippingOptionsContainer.innerHTML += `
                                <label class="block border rounded-lg p-3 cursor-pointer has-[:checked]:bg-red-50 has-[:checked]:border-red-500">
                                    <input type="radio" id="${id}" name="shipping_option" value="${cost.cost[0].value}" class="hidden" data-service-name="${courier.name} - ${cost.service}">
                                    <div class="flex justify-between items-center text-sm">
                                        <div><p class="font-bold">${courier.name} (${cost.service})</p><p class="text-xs text-gray-500">Estimasi ${cost.cost[0].etd}</p></div>
                                        <p class="font-semibold">${formatRupiah(cost.cost[0].value)}</p>
                                    </div>
                                </label>`;
                                    });
                                }
                            });
                        } else {
                            shippingOptionsContainer.innerHTML = '<p class="text-sm text-red-500">Tidak ada layanan pengiriman yang tersedia.</p>';
                        }
                    })
                    .catch(() => shippingOptionsContainer.innerHTML = '<p class="text-sm text-red-500">Gagal menghitung ongkir.</p>');
            }

            function updateVoucherEligibility() {
                const currentTotal = subtotal + shippingCost;
                document.querySelectorAll('.voucher-item').forEach(item => {
                    const minSpending = parseFloat(item.dataset.minSpending);
                    item.classList.toggle('disabled', currentTotal < minSpending);
                });
            }

            btnSelectVoucher.addEventListener('click', () => {
                updateVoucherEligibility();
                voucherModal.classList.remove('hidden');
                voucherModal.classList.add('flex');
            });

            btnCloseModal.addEventListener('click', () => {
                voucherModal.classList.add('hidden');
                voucherModal.classList.remove('flex');
            });

            document.querySelectorAll('.voucher-item').forEach(item => {
                item.addEventListener('click', function () {
                    if (this.classList.contains('disabled')) {
                        Swal.fire('Oops!', 'Total belanja Anda tidak memenuhi syarat untuk menggunakan voucher ini.', 'warning');
                        return;
                    }
                    selectedVoucher = this;
                    document.getElementById('voucher_id_input').value = this.dataset.id;
                    selectedVoucherText.innerHTML = `Voucher <strong>${this.dataset.code}</strong> diterapkan.`;
                    selectedVoucherInfo.classList.remove('hidden');
                    btnSelectVoucher.classList.add('hidden');
                    updateTotals();
                    voucherModal.classList.add('hidden');
                    voucherModal.classList.remove('flex');
                });
            });

            btnRemoveVoucher.addEventListener('click', () => {
                selectedVoucher = null;
                document.getElementById('voucher_id_input').value = '';
                selectedVoucherInfo.classList.add('hidden');
                btnSelectVoucher.classList.remove('hidden');
                updateTotals();
            });

            paymentOptionLabels.forEach(label => {
                label.addEventListener('click', () => {
                    paymentOptionLabels.forEach(l => l.classList.remove('selected'));
                    label.classList.add('selected');
                });
            });

            checkoutForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const payButton = document.getElementById('btn-process-payment');
                payButton.disabled = true;
                payButton.innerHTML = '<span class="animate-pulse">Memproses...</span>';

                // --- VALIDATION LOGIC ---
                const deliveryMethod = document.querySelector('input[name="delivery_method_option"]:checked').value;
                if (deliveryMethod === 'ship') {
                    const alamatLengkap = document.getElementById('alamat').value.trim();
                    const destinasiDipilih = document.getElementById('destination_id').value;
                    const pengirimanDipilih = document.querySelector('input[name="shipping_option"]:checked');

                    if (alamatLengkap === '' || destinasiDipilih === '') {
                        Swal.fire('Alamat Tidak Lengkap', 'Silakan cari kecamatan tujuan dan isi alamat lengkap Anda.', 'warning');
                        payButton.disabled = false;
                        payButton.innerHTML = 'Bayar Sekarang';
                        return;
                    }
                    if (!pengirimanDipilih) {
                        Swal.fire('Pengiriman Belum Dipilih', 'Silakan pilih layanan pengiriman yang tersedia.', 'warning');
                        payButton.disabled = false;
                        payButton.innerHTML = 'Bayar Sekarang';
                        return;
                    }
                }

                const selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked');
                if (!selectedPaymentMethod) {
                    Swal.fire('Peringatan', 'Silakan pilih metode pembayaran.', 'warning');
                    payButton.disabled = false;
                    payButton.innerHTML = 'Bayar Sekarang';
                    return;
                }

                // --- DATA GATHERING ---
                const items = Array.from(document.querySelectorAll('input[name="items[]"]')).map(input => input.value);
                const dataToSend = {
                    _token: this.querySelector('input[name="_token"]').value,
                    items: items,
                    delivery_method: document.getElementById('delivery_method_input').value,
                    shipping_cost: document.getElementById('shipping_cost_input').value,
                    shipping_service: document.getElementById('shipping_service_input').value,
                    alamat: document.getElementById('alamat').value,
                    voucher_id: document.getElementById('voucher_id_input').value,
                    discount_amount: document.getElementById('discount_amount_input').value,
                    payment_method: selectedPaymentMethod.value
                };

                axios.post("{{ route('tenant.checkout.charge', ['subdomain' => $currentSubdomain]) }}", dataToSend)
                    .then(response => handlePaymentResponse(response.data))
                    .catch(error => {
                        console.error('Payment Error:', error.response);
                        let message = 'Terjadi kesalahan.';
                        if (error.response && error.response.data) {
                            if (error.response.data.errors) {
                                message = Object.values(error.response.data.errors)[0][0];
                            } else if (error.response.data.error) {
                                message = error.response.data.error;
                            }
                        }
                        Swal.fire('Error', message, 'error');
                        payButton.disabled = false;
                        payButton.innerHTML = 'Bayar Sekarang';
                    });
            });

            function handlePaymentResponse(data) {
                let html = '';
                checkoutContainer.style.display = 'none';
                paymentInstructions.classList.remove('hidden');

                if (data.va_numbers && data.va_numbers.length > 0) {
                    const va = data.va_numbers[0];
                    html = `<div class="p-6 border-l-4 border-blue-500 bg-blue-50 rounded-r-lg"><h4 class="font-bold text-lg text-blue-800">Instruksi Pembayaran ${va.bank.toUpperCase()} VA</h4><p class="mt-2 text-sm">Silakan selesaikan pembayaran Anda ke nomor Virtual Account berikut:</p><div class="my-4 p-3 bg-white border rounded-lg text-center flex items-center justify-between"><p id="va-number" class="text-2xl font-bold tracking-wider">${va.va_number}</p><button class="ml-4 text-sm text-blue-600 hover:underline">Salin</button></div><p class="text-sm">Total Tagihan: <strong class="font-bold">${formatRupiah(data.gross_amount)}</strong></p><p class="text-xs mt-2">Batas waktu pembayaran: ${new Date(data.expiry_time).toLocaleString('id-ID')}</p></div>`;
                } else if (data.actions && data.actions.some(a => a.name === 'generate-qr-code')) {
                    const qrCodeUrl = data.actions.find(a => a.name === 'generate-qr-code').url;
                    html = `<div class="p-6 border-l-4 border-green-500 bg-green-50 text-center rounded-r-lg"><h4 class="font-bold text-lg text-green-800">Instruksi Pembayaran QRIS</h4><p class="mt-2 text-sm">Pindai kode QR di bawah ini menggunakan aplikasi e-wallet Anda.</p><div class="my-4 flex justify-center"><img src="${qrCodeUrl}" alt="QR Code Pembayaran" class="w-56 h-56 border p-1 bg-white"></div><p class="text-sm">Total Tagihan: <strong class="font-bold">${formatRupiah(data.gross_amount)}</strong></p></div>`;
                } else if (data.actions && data.actions.some(a => a.name === 'deeplink-redirect')) {
                    const deepLink = data.actions.find(a => a.name === 'deeplink-redirect').url;
                    html = `<div class="p-6 border-l-4 border-cyan-500 bg-cyan-50 text-center rounded-r-lg"><h4 class="font-bold text-lg text-cyan-800">Instruksi Pembayaran GoPay</h4><p class="mt-2 text-sm">Klik tombol di bawah untuk membuka aplikasi Gojek dan menyelesaikan pembayaran.</p><a href="${deepLink}" class="inline-block mt-4 bg-cyan-500 text-white font-bold px-6 py-3 rounded-lg hover:bg-cyan-600">Buka Aplikasi Gojek</a></div>`;
                }
                paymentInstructions.innerHTML = html;
                const copyBtn = paymentInstructions.querySelector('button');
                if (copyBtn) {
                    copyBtn.onclick = () => copyToClipboard(paymentInstructions.querySelector('#va-number').textContent);
                }
            }

            updateTotals();
        });
    </script>
@endpush