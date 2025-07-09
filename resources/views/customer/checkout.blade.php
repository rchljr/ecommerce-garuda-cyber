@extends('layouts.customer')
@section('title', 'Checkout')

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- Pastikan Anda memasukkan client key Midtrans Anda di file .env --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>
@endpush

@section('content')
    @php
        $currentSubdomain = request()->route('subdomain');
    @endphp
    <div class="bg-gray-100 py-12">
        <div class="container mx-auto px-4">
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
                <input type="hidden" name="delivery_method" id="delivery_method_input" value="ship">
                <input type="hidden" name="payment_method" value="midtrans">

                <div class="flex flex-col lg:flex-row gap-8">
                    <!-- Kolom Kiri -->
                    <div class="w-full lg:w-2/3 space-y-8">
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h2 class="text-xl font-bold mb-4">Metode Pengambilan</h2>
                            <div class="flex flex-col sm:flex-row gap-4" id="delivery-method-options">
                                <label
                                    class="flex-1 border rounded-lg p-4 cursor-pointer has-[:checked]:bg-red-50 has-[:checked]:border-red-500">
                                    <input type="radio" name="delivery_method_option" value="ship" class="hidden" checked>
                                    <div class="ml-3">
                                        <p class="font-bold">Kirim ke Alamat</p>
                                        <p class="text-sm text-gray-500">Pesanan akan dikirimkan ke alamat Anda.</p>
                                    </div>
                                </label>
                                <label
                                    class="flex-1 border rounded-lg p-4 cursor-pointer has-[:checked]:bg-red-50 has-[:checked]:border-red-500">
                                    <input type="radio" name="delivery_method_option" value="pickup" class="hidden">
                                    <div class="ml-3">
                                        <p class="font-bold">Ambil di Toko</p>
                                        <p class="text-sm text-gray-500">Ambil pesanan langsung di lokasi mitra.</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!--  #1: Container untuk Alamat Pengiriman (SHIP) -->
                        <div id="shipping-details-container" class="space-y-8">
                            <div class="bg-white rounded-lg shadow-md p-6">
                                <h2 class="text-xl font-bold mb-4">Alamat Pengiriman</h2>
                                <p class="text-sm text-gray-500 mb-4">
                                    Alamat ini akan digunakan untuk pengiriman. Anda bisa mengubahnya jika diperlukan.
                                </p>
                                <div class="relative">
                                    <label for="destination_search" class="block text-sm font-medium text-gray-700">Cari
                                        Kecamatan Tujuan</label>
                                    <input type="text" id="destination_search" name="destination_search"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        placeholder="Ketik nama kecamatan, misal: Tampan">
                                    <div id="destination-results"
                                        class="absolute z-10 w-full mt-1 border rounded-md bg-white max-h-48 overflow-y-auto shadow-lg hidden">
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <label for="alamat" class="block text-sm font-medium">Alamat Lengkap</label>
                                    <textarea name="alamat" id="alamat" rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        required>{{ $customer->alamat ?? '' }}</textarea>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg shadow-md p-6">
                                <h2 class="text-xl font-bold mb-4">Pilih Pengiriman</h2>
                                <div id="shipping-options-container" class="space-y-3">
                                    <p class="text-gray-500 text-sm">Silakan cari dan pilih alamat tujuan terlebih dahulu.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!--  #2: Container untuk Alamat Ambil di Toko (PICKUP) -->
                        <div id="pickup-details-container" class="hidden">
                            <div class="bg-white rounded-lg shadow-md p-6">
                                <h2 class="text-xl font-bold mb-4">Lokasi Pengambilan</h2>
                                <div class="space-y-2 text-gray-700">
                                    <p class="font-semibold text-lg">{{ $shop->shop_name ?? 'Nama Toko Mitra' }}</p>
                                    @if($contact)
                                        <div class="mt-2 border-t pt-2">
                                            <p>{{ $contact->address_line1 }}</p>
                                            <p>{{ $contact->city }}{{ $contact->state ? ', ' . $contact->state : '' }} {{ $contact->postal_code ?? '' }}</p>
                                            <p class="mt-2">
                                                <strong>Telepon:</strong> 
                                                @if($contact->phone)
                                                    <a href="https://wa.me/{{ $contact->phone }}" target="_blank" class="text-green-600 hover:underline inline-flex items-center">
                                                        {{ $contact->phone }}
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </p>
                                            <p><strong>Jam Buka:</strong> {{ $contact->working_hours ?? '-' }}</p>
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500 mt-2">Informasi alamat lengkap toko belum tersedia.</p>
                                    @endif
                                    <p class="text-xs text-gray-500 mt-4">
                                        Anda dapat mengambil pesanan di alamat ini setelah pembayaran dikonfirmasi.
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Kolom Kanan -->
                    <div class="w-full lg:w-1/3">
                        <div class="bg-white rounded-lg shadow-md p-6 sticky top-24 space-y-4">
                            <h2 class="text-xl font-bold border-b pb-4">Ringkasan Pesanan</h2>
                            <div class="space-y-4 max-h-64 overflow-y-auto pr-2">
                                @foreach ($checkoutItems as $item)
                                    <div class="flex items-center gap-4 text-sm">
                                        <img src="{{ asset('storage/' . ($item->product->main_image ?? '')) }}"
                                            onerror="this.onerror=null;this.src='https://placehold.co/64x64/f1f5f9/cbd5e1?text=No+Image';"
                                            alt="{{ $item->product->name }}" class="w-16 h-16 rounded-md object-cover">
                                        <div class="flex-grow">
                                            <p class="font-semibold">{{ $item->product->name }}</p>
                                            <p class="text-gray-500">{{ $item->quantity }} x
                                                {{ format_rupiah($item->product->price) }}
                                            </p>
                                        </div>
                                        <p class="font-semibold">{{ format_rupiah($item->product->price * $item->quantity) }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                            <div class="border-t pt-4 space-y-2">
                                <div class="flex justify-between"><span>Subtotal</span><span id="subtotal-amount"
                                        data-value="{{ $subtotal }}">{{ format_rupiah($subtotal) }}</span></div>
                                <div class="flex justify-between" id="shipping-cost-row"><span>Ongkos Kirim</span><span
                                        id="shipping-cost-display">Rp 0</span></div>
                                <div id="voucher-discount-row" class="flex justify-between text-green-600 hidden">
                                    <span>Diskon Voucher</span><span id="voucher-discount-display">- Rp 0</span>
                                </div>
                                <div class="flex justify-between font-bold text-lg border-t pt-4 mt-2">
                                    <span>Total</span><span id="total-amount">{{ format_rupiah($subtotal) }}</span>
                                </div>
                            </div>
                            <div class="pt-4">
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
                            <button type="button" id="btn-process-payment"
                                class="w-full mt-6 bg-gray-800 text-white font-bold py-3 rounded-lg hover:bg-gray-900 transition-colors">Lanjut
                                ke Pembayaran</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- PERMINTAAN #3: Modal Voucher (Hanya menampilkan voucher yang sudah difilter dari controller) -->
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
                    <p class="text-center text-gray-500 py-8">Tidak ada voucher yang tersedia untuk Anda.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // URLs and CSRF Token
            const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const SEARCH_URL = "{{ route('tenant.checkout.search_destination', ['subdomain' => $currentSubdomain]) }}";
            const CALCULATE_URL = "{{ route('tenant.checkout.calculate_shipping', ['subdomain' => $currentSubdomain]) }}";

            // Form and Buttons
            const checkoutForm = document.getElementById('checkout-form');
            const btnProcessPayment = document.getElementById('btn-process-payment');

            // Amounts
            const subtotalAmount = parseFloat(document.getElementById('subtotal-amount').dataset.value);
            let selectedShippingCost = 0;
            let selectedVoucherDiscountAmount = 0;

            // Delivery Elements
            const deliveryMethodOptions = document.getElementById('delivery-method-options');
            const shippingDetailsContainer = document.getElementById('shipping-details-container');
            const pickupDetailsContainer = document.getElementById('pickup-details-container'); // Baru
            const shippingOptionsContainer = document.getElementById('shipping-options-container');
            const shippingCostRow = document.getElementById('shipping-cost-row');

            // Address Search Elements
            const searchInput = document.getElementById('destination_search');
            const resultsContainer = document.getElementById('destination-results');
            let searchTimeout;

            // Voucher Elements
            const voucherModal = document.getElementById('voucher-modal');
            const btnSelectVoucher = document.getElementById('btn-select-voucher');
            const btnCloseModal = document.getElementById('btn-close-modal');
            const selectedVoucherInfo = document.getElementById('selected-voucher-info');
            const selectedVoucherText = document.getElementById('selected-voucher-text');
            const btnRemoveVoucher = document.getElementById('btn-remove-voucher');

            // --- Helper Functions ---
            function formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
            }

            function updateTotals() {
                const deliveryMethod = document.querySelector('input[name="delivery_method_option"]:checked').value;
                const shippingCost = (deliveryMethod === 'ship') ? selectedShippingCost : 0;

                const total = subtotalAmount + shippingCost - selectedVoucherDiscountAmount;
                document.getElementById('shipping-cost-display').textContent = formatRupiah(shippingCost);

                const discountRow = document.getElementById('voucher-discount-row');
                if (selectedVoucherDiscountAmount > 0) {
                    document.getElementById('voucher-discount-display').textContent = `- ${formatRupiah(selectedVoucherDiscountAmount)}`;
                    discountRow.classList.remove('hidden');
                } else {
                    discountRow.classList.add('hidden');
                }
                document.getElementById('total-amount').textContent = formatRupiah(total > 0 ? total : 0);
            }

            // --- PERMINTAAN #4: Logika Pembayaran Midtrans (sudah ada dan benar) ---
            function handleMidtransPayment() {
                // Pastikan alamat diisi jika metode pengiriman adalah 'ship'
                const deliveryMethod = document.querySelector('input[name="delivery_method_option"]:checked').value;
                const alamatLengkap = document.getElementById('alamat').value.trim();
                const destinasiDipilih = document.getElementById('destination_id').value;

                if (deliveryMethod === 'ship' && (alamatLengkap === '' || destinasiDipilih === '')) {
                    Swal.fire('Alamat Tidak Lengkap', 'Silakan cari kecamatan tujuan dan isi alamat lengkap Anda.', 'warning');
                    return;
                }

                const formData = new FormData(checkoutForm);
                Swal.fire({ title: 'Memproses...', text: 'Mohon tunggu sebentar...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                fetch("{{ route('tenant.checkout.process', ['subdomain' => $currentSubdomain]) }}", {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        Swal.close();
                        if (data.snapToken) {
                            snap.pay(data.snapToken, {
                                onSuccess: function (result) { Swal.fire('Pembayaran Berhasil!', '', 'success').then(() => window.location.href = "{{ route('tenant.home', ['subdomain' => $currentSubdomain]) }}"); },
                                onPending: function (result) { Swal.fire('Pembayaran Tertunda', 'Silakan selesaikan pembayaran.', 'info').then(() => window.location.href = "{{ route('tenant.home', ['subdomain' => $currentSubdomain]) }}"); },
                                onError: function (result) { Swal.fire('Pembayaran Gagal', 'Terjadi kesalahan saat memproses pembayaran.', 'error'); },
                                onClose: function () { Swal.fire({ icon: 'warning', title: 'Pembayaran Dibatalkan', text: 'Anda menutup jendela pembayaran.' }); }
                            });
                        } else {
                            Swal.fire('Error', data.error || 'Gagal memulai sesi pembayaran.', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.close();
                        console.error('Fetch Error:', error);
                        Swal.fire('Error', 'Terjadi kesalahan jaringan. Silakan coba lagi.', 'error');
                    });
            }

            // --- Address Search Logic ---
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
                    fetch(`${SEARCH_URL}?keyword=${keyword}`)
                        .then(response => response.json())
                        .then(data => {
                            resultsContainer.innerHTML = '';
                            if (data.error) {
                                resultsContainer.innerHTML = `<div class="p-3 text-sm text-red-500">${data.error}</div>`;
                                return;
                            }
                            if (data && Array.isArray(data) && data.length > 0) {
                                data.forEach(location => {
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
                        .catch(err => {
                            console.error("Search API Error:", err);
                            resultsContainer.innerHTML = '<div class="p-3 text-sm text-red-500">Gagal mencari lokasi. Coba lagi nanti.</div>';
                        });
                }, 500); // Debounce
            });

            function selectDestination(id, name) {
                document.getElementById('destination_id').value = id;
                searchInput.value = name;
                resultsContainer.innerHTML = '';
                resultsContainer.classList.add('hidden');
                calculateShippingCost(id);
            }

            // --- Shipping Calculation Logic ---
            function calculateShippingCost(destinationId) {
                shippingOptionsContainer.innerHTML = '<p class="text-sm text-gray-500">Menghitung ongkos kirim...</p>';
                const originId = document.getElementById('origin_id').value;
                const weight = document.getElementById('total_weight_kg').value;

                fetch(`${CALCULATE_URL}?origin_id=${originId}&destination_id=${destinationId}&weight=${weight}`)
                    .then(response => response.json())
                    .then(data => {
                        shippingOptionsContainer.innerHTML = '';
                        if (data.error) {
                            shippingOptionsContainer.innerHTML = `<p class="text-sm text-red-500">${data.error}</p>`;
                            return;
                        }
                        if (data && Array.isArray(data) && data.length > 0) {
                            data.forEach(courier => {
                                if (courier.costs && Array.isArray(courier.costs)) {
                                    courier.costs.forEach(cost => {
                                        const id = `${courier.code}-${cost.service}`.replace(/\s+/g, '-');
                                        shippingOptionsContainer.innerHTML += `
                                            <label for="${id}" class="block border rounded-lg p-4 cursor-pointer has-[:checked]:bg-red-50 has-[:checked]:border-red-500">
                                                <input type="radio" id="${id}" name="shipping_option" value="${cost.cost[0].value}" class="hidden" data-service-name="${courier.name} - ${cost.service}">
                                                <div class="flex justify-between items-center">
                                                    <div><p class="font-bold">${courier.name} (${cost.service})</p><p class="text-sm text-gray-500">Estimasi ${cost.cost[0].etd}</p></div>
                                                    <p class="font-semibold">${formatRupiah(cost.cost[0].value)}</p>
                                                </div>
                                            </label>`;
                                    });
                                }
                            });
                        } else {
                            shippingOptionsContainer.innerHTML = '<p class="text-sm text-red-500">Tidak ada layanan pengiriman yang tersedia untuk tujuan ini.</p>';
                        }
                    })
                    .catch(err => {
                        console.error("Calculate Shipping API Error:", err);
                        shippingOptionsContainer.innerHTML = '<p class="text-sm text-red-500">Gagal menghitung ongkos kirim.</p>';
                    });
            }

            // --- Event Listeners Setup ---
            if (btnProcessPayment) {
                btnProcessPayment.addEventListener('click', handleMidtransPayment);
            }

            // --- PERUBAHAN #2: Logika untuk menampilkan/menyembunyikan alamat ---
            if (deliveryMethodOptions) {
                deliveryMethodOptions.addEventListener('change', (e) => {
                    const selectedMethod = e.target.value;
                    document.getElementById('delivery_method_input').value = selectedMethod;

                    if (selectedMethod === 'pickup') {
                        shippingDetailsContainer.classList.add('hidden');
                        pickupDetailsContainer.classList.remove('hidden');
                        shippingCostRow.classList.add('hidden');
                        selectedShippingCost = 0;
                        document.getElementById('shipping_cost_input').value = 0;
                        const checkedShipping = document.querySelector('input[name="shipping_option"]:checked');
                        if (checkedShipping) checkedShipping.checked = false;
                    } else { // 'ship'
                        shippingDetailsContainer.classList.remove('hidden');
                        pickupDetailsContainer.classList.add('hidden');
                        shippingCostRow.classList.remove('hidden');
                    }
                    updateTotals();
                });
            }

            if (shippingOptionsContainer) {
                shippingOptionsContainer.addEventListener('change', (e) => {
                    if (e.target.name === 'shipping_option') {
                        selectedShippingCost = parseFloat(e.target.value);
                        document.getElementById('shipping_cost_input').value = selectedShippingCost;
                        document.getElementById('shipping_service_input').value = e.target.dataset.serviceName;
                        updateTotals();
                    }
                });
            }

            // Voucher Logic
            if (btnSelectVoucher) {
                btnSelectVoucher.addEventListener('click', () => voucherModal.classList.remove('hidden'));
            }
            if (btnCloseModal) {
                btnCloseModal.addEventListener('click', () => voucherModal.classList.add('hidden'));
            }
            document.querySelectorAll('.voucher-item').forEach(item => {
                item.addEventListener('click', () => {
                    const discountPercent = parseFloat(item.dataset.discountPercent);
                    selectedVoucherDiscountAmount = (discountPercent / 100) * subtotalAmount;

                    document.getElementById('voucher_id_input').value = item.dataset.id;
                    document.getElementById('discount_amount_input').value = selectedVoucherDiscountAmount;
                    selectedVoucherText.innerHTML = `Voucher <strong>${item.dataset.code}</strong> diterapkan (-${formatRupiah(selectedVoucherDiscountAmount)})`;
                    selectedVoucherInfo.classList.remove('hidden');
                    btnSelectVoucher.classList.add('hidden');
                    updateTotals();
                    voucherModal.classList.add('hidden');
                });
            });
            if (btnRemoveVoucher) {
                btnRemoveVoucher.addEventListener('click', () => {
                    selectedVoucherDiscountAmount = 0;
                    document.getElementById('voucher_id_input').value = '';
                    document.getElementById('discount_amount_input').value = '';
                    selectedVoucherInfo.classList.add('hidden');
                    btnSelectVoucher.classList.remove('hidden');
                    updateTotals();
                });
            }
        });
    </script>
@endpush