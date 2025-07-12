@extends('layouts.customer')
@section('title', 'Checkout')

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Gaya untuk voucher yang tidak bisa dipilih */
        .voucher-item.disabled {
            opacity: 0.6;
            cursor: not-allowed;
            filter: grayscale(80%);
            transform: none !important;
        }

        .voucher-item.disabled:hover {
            /* Tidak ada efek hover khusus untuk item yang dinonaktifkan */
        }

        /* Gaya untuk pilihan pembayaran */
        .payment-option-label {
            transition: all 0.2s ease-in-out;
        }

        .payment-option-label.selected {
            border-color: #ef4444;
            background-color: #fef2f2;
        }
    </style>
@endpush

@section('content')
    @php
        $currentSubdomain = request()->route('subdomain');
    @endphp
    <div class="bg-gray-100 py-12">
        <div class="container mx-auto px-4">
            <div id="checkout-container">
                <h1 class="text-3xl font-bold text-gray-800 mb-8">Checkout</h1>

                <form id="checkout-form" onsubmit="return false;">
                    @csrf
                    @foreach ($checkoutItems as $item)
                        <input type="hidden" name="items[]" value="{{ $item->id ?? $item['id'] }}">
                    @endforeach

                    <input type="hidden" name="origin_postal_code" id="origin_postal_code" value="{{ $originPostalCode }}">
                    <input type="hidden" name="destination_postal_code" id="destination_postal_code">

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
                                            Kecamatan/Kelurahan Tujuan</label>
                                        <input type="text" id="destination_search"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                            placeholder="Ketik nama area, misal: Menteng, Jakarta">
                                        <div id="destination-results"
                                            class="absolute z-20 w-full mt-1 border rounded-md bg-white max-h-48 overflow-y-auto shadow-lg hidden">
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <label for="alamat" class="block text-sm font-medium">Alamat Lengkap (Jalan, No.
                                            Rumah, RT/RW)</label>
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
                                    <div class="space-y-2 text-gray-700">
                                        <p class="font-semibold text-lg">{{ $shop->shop_name ?? 'Nama Toko Mitra' }}</p>
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

    <div id="voucher-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md max-h-full flex flex-col">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-lg font-bold">Pilih Voucher</h3>
                <button class="modal-close text-gray-500 hover:text-gray-800 text-2xl font-bold">&times;</button>
            </div>
            <div class="p-4 space-y-4 overflow-y-auto bg-gray-50">
                @if($vouchers->isEmpty())
                    <p class="text-center text-gray-500 py-8">Tidak ada voucher yang tersedia saat ini.</p>
                @else
                    @foreach ($vouchers as $voucher)
                        <div class="voucher-item relative flex bg-white rounded-lg shadow-sm overflow-hidden cursor-pointer transition-transform transform hover:scale-105"
                             data-id="{{ $voucher->id }}"
                             data-code="{{ $voucher->code }}"
                             data-min-spending="{{ $voucher->min_spending }}"
                             data-discount-percent="{{ $voucher->discount }}">
                            
                            <!-- Bagian Kiri - Info Diskon -->
                            <div class="flex-none w-24 bg-red-500 text-white flex flex-col items-center justify-center p-2">
                                <p class="font-bold text-2xl">{{ (int)$voucher->discount }}<span class="text-lg">%</span></p>
                                <p class="text-xs uppercase tracking-wider">Diskon</p>
                            </div>

                            <!-- Garis putus-putus pemisah -->
                            <div class="absolute top-0 bottom-0 left-24 w-px bg-gray-50" style="background-image: linear-gradient(to bottom, #e5e7eb 5px, transparent 5px); background-size: 100% 10px;"></div>
                            <div class="absolute top-0 bottom-0 left-24 flex items-center">
                                <div class="w-4 h-4 rounded-full bg-gray-50 transform -translate-x-1/2"></div>
                            </div>
                             <div class="absolute top-0 bottom-0 right-auto left-24 flex items-center">
                                <div class="w-4 h-4 rounded-full bg-gray-50 transform -translate-x-1/2" style="top: -0.5rem"></div>
                                <div class="w-4 h-4 rounded-full bg-gray-50 transform -translate-x-1/2" style="bottom: -0.5rem"></div>
                            </div>
                            
                            <!-- Bagian Kanan - Detail -->
                            <div class="flex-grow p-3 pl-6">
                                <p class="font-bold text-gray-800">{{ $voucher->name }}</p>
                                <p class="text-xs text-gray-500 mt-1">Gunakan kode: <span class="font-semibold text-red-600">{{ $voucher->code }}</span></p>
                                <div class="mt-2 pt-2 border-t border-dashed text-xs text-gray-600 space-y-1">
                                    <p>• Min. belanja: {{ format_rupiah($voucher->min_spending) }}</p>
                                    <p>• Berlaku hingga: {{ \Carbon\Carbon::parse($voucher->expired_date)->format('d M Y') }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
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
            const btnCloseModal = document.querySelector('#voucher-modal .modal-close');
            const selectedVoucherInfo = document.getElementById('selected-voucher-info');
            const selectedVoucherText = document.getElementById('selected-voucher-text');
            const btnRemoveVoucher = document.getElementById('btn-remove-voucher');
            const paymentOptionLabels = document.querySelectorAll('.payment-option-label');

            // --- URLs ---
            const SEARCH_URL = "{{ route('tenant.checkout.search_destination', ['subdomain' => $currentSubdomain]) }}";
            const CALCULATE_URL = "{{ route('tenant.checkout.calculate_shipping', ['subdomain' => $currentSubdomain]) }}";
            const CHARGE_URL = "{{ route('tenant.checkout.charge', ['subdomain' => $currentSubdomain]) }}";

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
                voucherDiscountDisplay.textContent = `- ${formatRupiah(voucherDiscount)}`;
                voucherDiscountRow.classList.toggle('hidden', voucherDiscount <= 0);
                totalAmountDisplay.textContent = formatRupiah(finalTotal > 0 ? finalTotal : 0);

                document.getElementById('shipping_cost_input').value = shippingCost;
                document.getElementById('discount_amount_input').value = voucherDiscount;
                updateVoucherEligibility();
            }

            // --- DELIVERY & ADDRESS LOGIC ---
            if (deliveryMethodOptions) {
                deliveryMethodOptions.addEventListener('change', (e) => {
                    const selectedMethod = document.querySelector('input[name="delivery_method_option"]:checked').value;
                    document.getElementById('delivery_method_input').value = selectedMethod;

                    if (selectedMethod === 'ship') {
                        shippingDetailsContainer.classList.remove('hidden');
                        pickupDetailsContainer.classList.add('hidden');
                        shippingCostSummaryRow.classList.remove('hidden');
                    } else {
                        shippingDetailsContainer.classList.add('hidden');
                        pickupDetailsContainer.classList.remove('hidden');
                        shippingCostSummaryRow.classList.add('hidden');
                        shippingCost = 0; // Reset ongkir jika pilih pickup
                        document.getElementById('shipping_service_input').value = '';
                        updateTotals();
                    }
                });
            }

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
                    axios.post(SEARCH_URL, { keyword: keyword })
                        .then(response => {
                            resultsContainer.innerHTML = '';
                            const areas = response.data;
                            if (areas && Array.isArray(areas) && areas.length > 0) {
                                areas.forEach(area => {
                                    const nameParts = area.name.split('. ');
                                    const postalCode = nameParts.length > 1 ? nameParts[1] : '';
                                    
                                    const item = document.createElement('div');
                                    item.className = 'p-3 text-sm cursor-pointer hover:bg-gray-100 border-b';
                                    const displayName = `${area.administrative_division_level_3_name}, ${area.administrative_division_level_2_name}, ${area.administrative_division_level_1_name} (${postalCode})`;
                                    item.textContent = displayName;
                                    item.addEventListener('click', () => selectDestination(postalCode, displayName));
                                    resultsContainer.appendChild(item);
                                });
                            } else {
                                resultsContainer.innerHTML = '<div class="p-3 text-sm text-gray-500">Lokasi tidak ditemukan.</div>';
                            }
                        })
                        .catch(err => {
                            console.error("Search Error:", err);
                            resultsContainer.innerHTML = `<div class="p-3 text-sm text-red-500">Gagal mencari lokasi.</div>`;
                        });
                }, 500);
            });

            function selectDestination(postalCode, name) {
                document.getElementById('destination_postal_code').value = postalCode;
                searchInput.value = name;
                resultsContainer.innerHTML = '';
                resultsContainer.classList.add('hidden');
                calculateShippingCost(postalCode);
            }

            // --- SHIPPING CALCULATION ---
            function calculateShippingCost(destinationPostalCode) {
                shippingOptionsContainer.innerHTML = '<p class="text-sm text-gray-500">Menghitung ongkos kirim...</p>';
                const originPostalCode = document.getElementById('origin_postal_code').value;
                const itemInputs = document.querySelectorAll('input[name="items[]"]');
                const itemIds = Array.from(itemInputs).map(input => input.value);

                const payload = {
                    origin_postal_code: originPostalCode,
                    destination_postal_code: destinationPostalCode,
                    items: itemIds
                };

                axios.post(CALCULATE_URL, payload)
                    .then(response => {
                        shippingOptionsContainer.innerHTML = '';
                        const pricing = response.data;
                        if (pricing && Array.isArray(pricing) && pricing.length > 0) {
                            pricing.forEach(rate => {
                                const id = `${rate.courier_code}-${rate.courier_service_code}`;
                                shippingOptionsContainer.innerHTML += `
                                <label for="${id}" class="block border rounded-lg p-4 cursor-pointer has-[:checked]:bg-red-50 has-[:checked]:border-red-500">
                                    <input type="radio" id="${id}" name="shipping_option" value="${rate.price}" class="hidden" data-service-name="${rate.courier_name} - ${rate.courier_service_name}">
                                    <div class="flex justify-between items-center">
                                        <div><p class="font-bold">${rate.courier_name} (${rate.courier_service_name})</p><p class="text-sm text-gray-500">Estimasi ${rate.duration}</p></div>
                                        <p class="font-semibold">${formatRupiah(rate.price)}</p>
                                    </div>
                                </label>`;
                            });
                        } else {
                            shippingOptionsContainer.innerHTML = '<p class="text-sm text-red-500">Tidak ada layanan pengiriman yang tersedia untuk tujuan ini.</p>';
                        }
                    })
                    .catch(err => {
                        console.error("Shipping Rate Error:", err);
                        const errorMessage = err.response?.data?.error || 'Gagal menghitung ongkir.';
                        shippingOptionsContainer.innerHTML = `<p class="text-sm text-red-500">${errorMessage}</p>`;
                    });
            }

            // --- EVENT LISTENER FOR SHIPPING OPTIONS ---
            shippingOptionsContainer.addEventListener('change', function(e) {
                // Pastikan yang diklik adalah radio button pengiriman
                if (e.target && e.target.matches('input[name="shipping_option"]')) {
                    const selectedOption = e.target;
                    
                    // Update state ongkos kirim
                    shippingCost = parseFloat(selectedOption.value);
                    
                    // Simpan nama layanan pengiriman ke input tersembunyi
                    const serviceName = selectedOption.dataset.serviceName;
                    document.getElementById('shipping_service_input').value = serviceName;

                    // Update total ringkasan pesanan
                    updateTotals();
                }
            });

            // --- VOUCHER LOGIC ---
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

            if (btnCloseModal) {
                btnCloseModal.addEventListener('click', () => {
                    voucherModal.classList.add('hidden');
                    voucherModal.classList.remove('flex');
                });
            }

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

            // --- PAYMENT LOGIC ---
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

                const deliveryMethod = document.querySelector('input[name="delivery_method_option"]:checked').value;
                if (deliveryMethod === 'ship') {
                    if (!document.getElementById('alamat').value.trim() || !document.getElementById('destination_postal_code').value) {
                        Swal.fire('Alamat Tidak Lengkap', 'Silakan lengkapi alamat pengiriman Anda.', 'warning');
                        payButton.disabled = false;
                        payButton.innerHTML = 'Bayar Sekarang';
                        return;
                    }
                    if (shippingCost <= 0) {
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

                axios.post(CHARGE_URL, dataToSend)
                    .then(response => handlePaymentResponse(response.data))
                    .catch(error => {
                        const message = error.response?.data?.error || 'Terjadi kesalahan.';
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

            // Inisialisasi total saat halaman dimuat
            updateTotals();
        });
    </script>
@endpush
