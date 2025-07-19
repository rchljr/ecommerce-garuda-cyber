@extends('layouts.customer')
@section('title', 'Checkout')

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .disabled-item {
            opacity: 0.6;
            cursor: not-allowed;
            filter: grayscale(80%);
        }

        .disabled-item:hover {
            transform: none !important;
        }

        .selected-option {
            border-color: #ef4444;
            background-color: #fef2f2;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.875rem;
            color: #4b5563;
        }

        .voucher-item-modal {
            display: flex;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.2s ease-in-out;
        }

        .voucher-item-modal:not(.disabled-item):hover {
            transform: scale(1.03);
        }

        .payment-option-label img {
            height: 20px;
            margin-bottom: 4px;
            object-fit: contain;
        }
    </style>
@endpush

@section('content')
    <div class="bg-gray-100 py-12">
        <div class="container mx-auto px-4">
            <div id="checkout-content-wrapper">
                <div id="checkout-loader" class="text-center py-20">
                    <i class="fa fa-spinner fa-spin text-4xl text-gray-400"></i>
                    <p class="mt-4 text-gray-600">Mempersiapkan pesanan Anda...</p>
                </div>
            </div>
        </div>
    </div>

    <div id="voucher-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md max-h-full flex flex-col">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-lg font-bold">Pilih Voucher</h3>
                <button class="modal-close text-gray-500 hover:text-gray-800 text-2xl font-bold">&times;</button>
            </div>
            <div id="voucher-modal-body" class="p-4 space-y-4 overflow-y-auto bg-gray-50"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkoutItemIds = @json($checkoutItemIds);
            const checkoutState = { shops: {}, deliveryMethod: 'pickup', destinationPostalCode: null, grandSubtotal: 0, grandShipping: 0, grandDiscount: 0, grandTotal: 0, fullData: null };
            let searchTimeout;
            const wrapper = document.getElementById('checkout-content-wrapper');
            const voucherModal = document.getElementById('voucher-modal');
            const formatRupiah = (number) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);

            function renderCheckoutPage(data) {
                checkoutState.fullData = data;
                checkoutState.grandSubtotal = data.grandSubtotal;

                let shopsHTML = '';
                data.shopsData.forEach(shopData => {
                    const shopId = shopData.shop.id;
                    // PERBAIKAN: Tambahkan 'notes' ke state
                    checkoutState.shops[shopId] = { subtotal: shopData.subtotal, shippingCost: 0, shippingService: '', voucherId: null, voucherCode: '', voucherDiscount: 0, notes: '' };
                    let itemsHTML = '';
                    shopData.items.forEach(item => {
                        itemsHTML += `<div class="flex gap-4 text-sm"><img src="/storage/${item.main_image || 'images/placeholder.png'}" onerror="this.onerror=null;this.src='https://placehold.co/64x64/f1f5f9/cbd5e1?text=No+Image';" class="w-16 h-16 rounded-md object-cover"><div class="flex-grow"><p class="font-semibold text-gray-800">${item.product_name}</p><p class="text-gray-500">Varian: ${item.variant_color || ''} / ${item.variant_size || ''}</p><p class="text-gray-500">${item.quantity} x ${formatRupiah(item.price)}</p></div><p class="font-semibold text-gray-800">${formatRupiah(item.total_price)}</p></div>`;
                    });

                    // PERBAIKAN: Tambahkan textarea untuk catatan
                    const notesHTML = `
                            <div class="mt-4">
                                <label for="notes-${shopId}" class="text-sm font-medium text-gray-600">Catatan untuk Penjual (Opsional)</label>
                                <textarea id="notes-${shopId}" data-shop-id="${shopId}" class="shop-notes-input mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" rows="2" placeholder="Tinggalkan pesan untuk penjual..."></textarea>
                            </div>
                        `;

                    shopsHTML += `<div class="bg-white rounded-lg shadow-md" data-shop-id="${shopId}" data-origin-postal-code="${shopData.origin_postal_code}" data-item-ids='${JSON.stringify(shopData.item_ids_for_shipping)}'><div class="p-4 border-b"><h3 class="font-bold text-lg flex items-center gap-2"><i class="fa fa-store text-gray-500"></i> ${shopData.shop.shop_name}</h3></div><div class="p-4 space-y-4">${itemsHTML}</div><div class="p-4 border-t space-y-4"><div class="flex justify-between items-center text-sm"><span class="font-medium text-gray-600">Subtotal Toko</span><span class="font-bold text-gray-800">${formatRupiah(shopData.subtotal)}</span></div><div class="flex justify-between items-center"><span class="text-sm font-medium">Voucher Toko</span><button type="button" class="btn-select-voucher text-sm text-red-600 hover:underline" data-shop-id="${shopId}">Pilih Voucher</button></div><div id="selected-voucher-info-${shopId}" class="hidden mt-2 p-2 bg-green-50 border-green-200 rounded-md text-xs flex justify-between items-center"></div>${notesHTML}<div class="delivery-section-pickup mt-4"><p class="text-sm text-gray-600"><i class="fa fa-map-marker-alt mr-2 text-gray-400"></i>Ambil di lokasi: ${shopData.shop.shop_name}</p></div><div class="delivery-section-ship hidden mt-4"><h4 class="text-sm font-medium mb-2">Pilih Pengiriman</h4><div id="shipping-options-${shopId}" class="space-y-2"><p class="text-gray-500 text-xs">Pilih alamat tujuan untuk melihat opsi pengiriman.</p></div></div></div></div>`;
                });

                const mainHTML = `
                        <h1 class="text-3xl font-bold text-gray-800 mb-8">Checkout</h1>
                        <form id="checkout-form" onsubmit="return false;">
                            @csrf
                            ${checkoutItemIds.map(id => `<input type="hidden" name="items[]" value="${id}">`).join('')}
                            <div class="flex flex-col lg:flex-row gap-8">
                                <div class="w-full lg:w-2/3 space-y-8">
                                    <div class="bg-white rounded-lg shadow-md p-6"><h2 class="text-xl font-bold mb-4">Metode Pengambilan</h2><div class="flex flex-col sm:flex-row gap-4" id="delivery-method-options"><label class="flex-1 border-2 rounded-lg p-4 cursor-pointer selected-option"><input type="radio" name="delivery_method_option" value="pickup" class="hidden" checked><div class="flex items-center"><i class="fa fa-store text-xl text-gray-600 mr-4"></i><div><p class="font-bold">Ambil di Toko</p><p class="text-sm text-gray-500">Ambil pesanan di masing-masing toko.</p></div></div></label><label class="flex-1 border-2 rounded-lg p-4 cursor-pointer"><input type="radio" name="delivery_method_option" value="ship" class="hidden"><div class="flex items-center"><i class="fa fa-truck text-xl text-gray-600 mr-4"></i><div><p class="font-bold">Kirim ke Alamat</p><p class="text-sm text-gray-500">Pesanan akan dikirimkan.</p></div></div></label></div></div>
                                    <div id="shipping-address-container" class="bg-white rounded-lg shadow-md p-6 hidden"><h2 class="text-xl font-bold mb-4">Alamat Pengiriman</h2><div class="relative"><label for="destination_search" class="block text-sm font-medium text-gray-700">Cari Kecamatan/Kelurahan</label><input type="text" id="destination_search" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Ketik nama area..."><div id="destination-results" class="absolute z-20 w-full mt-1 border rounded-md bg-white max-h-48 overflow-y-auto shadow-lg hidden"></div></div><div class="mt-4"><label for="alamat" class="block text-sm font-medium">Alamat Lengkap</label><textarea name="alamat" id="alamat" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">${data.customer.alamat || ''}</textarea></div></div>
                                    ${shopsHTML}
                                </div>
                                <div class="w-full lg:w-1/3">
                                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-24 space-y-4">
                                        <h2 class="text-xl font-bold border-b pb-4">Ringkasan Pesanan</h2>
                                        <div id="summary-details" class="border-b pb-4 space-y-2"><div class="summary-row"><span>Subtotal Produk</span><span id="summary-subtotal">${formatRupiah(data.grandSubtotal)}</span></div><div class="summary-row hidden" id="summary-shipping-row"><span>Total Ongkos Kirim</span><span id="summary-shipping-cost">Rp 0</span></div><div class="summary-row text-green-600 hidden" id="summary-discount-row"><span>Total Diskon</span><span id="summary-discount">- Rp 0</span></div></div>
                                        <div class="flex justify-between font-bold text-lg pt-2"><span>Total</span><span id="grand-total">${formatRupiah(data.grandSubtotal)}</span></div>
                                        <div class="pt-4 border-t">
                                            <h3 class="text-base font-semibold mb-3">Metode Pembayaran</h3>
                                            <div class="grid grid-cols-2 gap-3" id="payment-method-options">
                                                <label class="payment-option-label relative flex flex-col items-center justify-center p-3 border-2 rounded-lg cursor-pointer"><input type="radio" name="payment_method" value="va_bca" class="absolute opacity-0 peer"><img src="/images/bca.png" alt="BCA VA"><span class="text-xs font-medium text-center">BCA VA</span></label>
                                                <label class="payment-option-label relative flex flex-col items-center justify-center p-3 border-2 rounded-lg cursor-pointer"><input type="radio" name="payment_method" value="va_bri" class="absolute opacity-0 peer"><img src="/images/bri.png" alt="BRI VA"><span class="text-xs font-medium text-center">BRI VA</span></label>
                                                <label class="payment-option-label relative flex flex-col items-center justify-center p-3 border-2 rounded-lg cursor-pointer"><input type="radio" name="payment_method" value="gopay" class="absolute opacity-0 peer"><img src="/images/gopay.png" alt="Gopay"><span class="text-xs font-medium text-center">Gopay</span></label>
                                                <label class="payment-option-label relative flex flex-col items-center justify-center p-3 border-2 rounded-lg cursor-pointer"><input type="radio" name="payment_method" value="qris" class="absolute opacity-0 peer"><img src="/images/qris.png" alt="QRIS"><span class="text-xs font-medium text-center">QRIS</span></label>
                                            </div>
                                        </div>
                                        <button type="submit" id="btn-process-payment" class="w-full mt-6 bg-gray-800 text-white font-bold py-3 rounded-lg hover:bg-black">Bayar Sekarang</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div id="payment-instructions-container"></div>`;
                wrapper.innerHTML = mainHTML;
                renderSummary();
                addEventListeners();
            }

            function renderSummary() {
                let totalShipping = 0, totalDiscount = 0;
                Object.values(checkoutState.shops).forEach(shop => { totalShipping += shop.shippingCost; totalDiscount += shop.voucherDiscount; });
                checkoutState.grandShipping = totalShipping; checkoutState.grandDiscount = totalDiscount;
                checkoutState.grandTotal = (checkoutState.grandSubtotal - totalDiscount) + totalShipping;
                document.getElementById('summary-subtotal').textContent = formatRupiah(checkoutState.grandSubtotal);
                document.getElementById('summary-shipping-cost').textContent = formatRupiah(totalShipping);
                document.getElementById('summary-discount').textContent = `- ${formatRupiah(totalDiscount)}`;
                document.getElementById('grand-total').textContent = formatRupiah(checkoutState.grandTotal);
                document.getElementById('summary-shipping-row').classList.toggle('hidden', totalShipping <= 0 && checkoutState.deliveryMethod === 'pickup');
                document.getElementById('summary-discount-row').classList.toggle('hidden', totalDiscount <= 0);
            }

            function openVoucherModal(shopId) {
                const modalBody = document.getElementById('voucher-modal-body');
                modalBody.innerHTML = '';
                const shopData = checkoutState.fullData.shopsData.find(s => s.shop.id == shopId);
                if (shopData && shopData.vouchers.length > 0) {
                    shopData.vouchers.forEach(voucher => {
                        const remainingAmount = voucher.min_spending - shopData.subtotal;
                        modalBody.innerHTML += `<div class="voucher-item-modal ${!voucher.is_eligible ? 'disabled-item' : ''}" data-id="${voucher.id}" data-code="${voucher.voucher_code}" data-shop-id="${shopId}" data-discount-percent="${voucher.discount}"><div class="flex-none w-24 bg-red-500 text-white flex flex-col items-center justify-center p-2 ${!voucher.is_eligible ? 'bg-gray-400' : ''}"><p class="font-bold text-2xl">${Math.floor(voucher.discount)}<span class="text-lg">%</span></p><p class="text-xs uppercase">Diskon</p></div><div class="flex-grow p-3 pl-4"><p class="font-bold text-gray-800">${voucher.voucher_code || 'Voucher Spesial'}</p><div class="mt-2 pt-2 border-t border-dashed text-xs text-gray-600 space-y-1"><p>• Min. belanja: ${formatRupiah(voucher.min_spending)}</p><p>• Berlaku hingga: ${new Date(voucher.expired_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}</p>${!voucher.is_eligible && remainingAmount > 0 ? `<p class="text-red-500 font-semibold">• Belanja lagi ${formatRupiah(remainingAmount)} untuk bisa pakai</p>` : ''}</div></div></div>`;
                    });
                } else {
                    modalBody.innerHTML = '<p class="text-center text-gray-500 py-8">Tidak ada voucher untuk toko ini.</p>';
                }
                voucherModal.classList.remove('hidden');
            }

            function selectDestination(postalCode, name) {
                document.getElementById('destination_search').value = name;
                document.getElementById('destination-results').classList.add('hidden');
                checkoutState.destinationPostalCode = postalCode;
                document.querySelectorAll('[data-shop-id]').forEach(shopEl => calculateShippingForShop(shopEl.dataset.shopId));
            }

            function calculateShippingForShop(shopId) {
                const shopEl = document.querySelector(`[data-shop-id="${shopId}"]`);
                const shippingContainer = document.getElementById(`shipping-options-${shopId}`);
                shippingContainer.innerHTML = '<p class="text-gray-500 text-xs">Menghitung...</p>';
                const payload = { origin_postal_code: shopEl.dataset.originPostalCode, destination_postal_code: checkoutState.destinationPostalCode, items: JSON.parse(shopEl.dataset.itemIds) };
                axios.post("{{ route('tenant.checkout.calculate_shipping', ['subdomain' => request()->route('subdomain')]) }}", payload)
                    .then(response => {
                        shippingContainer.innerHTML = '';
                        if (response.data && response.data.length > 0) {
                            response.data.forEach(rate => {
                                const id = `shipping-${shopId}-${rate.courier_code}-${rate.courier_service_code}`;
                                shippingContainer.innerHTML += `<label for="${id}" class="block border rounded-lg p-3 text-xs cursor-pointer hover:bg-red-50 has-[:checked]:bg-red-50 has-[:checked]:border-red-500"><input type="radio" id="${id}" name="shipping_option_${shopId}" value="${rate.price}" class="hidden shipping-radio" data-shop-id="${shopId}" data-service-name="${rate.courier_name} - ${rate.courier_service_name}"><div class="flex justify-between items-center"><div><p class="font-bold">${rate.courier_name} (${rate.courier_service_name})</p><p class="text-gray-500">Estimasi ${rate.duration}</p></div><p class="font-semibold">${formatRupiah(rate.price)}</p></div></label>`;
                            });
                        } else {
                            shippingContainer.innerHTML = '<p class="text-red-500 text-xs">Tidak ada layanan pengiriman.</p>';
                        }
                    }).catch(err => shippingContainer.innerHTML = '<p class="text-red-500 text-xs">Gagal menghitung ongkir.</p>');
            }

            function handlePaymentResponse(data) {
                let html = '';
                const contentWrapper = document.getElementById('checkout-content-wrapper');
                if (!contentWrapper) return;

                if (data.va_numbers && data.va_numbers.length > 0) {
                    const va = data.va_numbers[0];
                    html = `<div class="p-6 border-l-4 border-blue-500 bg-blue-50 rounded-r-lg"><h4 class="font-bold text-lg text-blue-800">Instruksi Pembayaran ${va.bank.toUpperCase()} VA</h4><p class="mt-2 text-sm">Silakan selesaikan pembayaran Anda ke nomor Virtual Account berikut:</p><div class="my-4 p-3 bg-white border rounded-lg text-center flex items-center justify-between"><p id="va-number" class="text-xl md:text-2xl font-bold tracking-wider break-all">${va.va_number}</p><button class="copy-btn ml-4 text-sm text-blue-600 hover:underline">Salin</button></div><p class="text-sm">Total Tagihan: <strong class="font-bold">${formatRupiah(data.gross_amount)}</strong></p><p class="text-xs mt-2">Batas waktu pembayaran: ${new Date(data.expiry_time).toLocaleString('id-ID')}</p></div>`;
                } else if (data.actions && data.actions.some(a => a.name === 'generate-qr-code')) {
                    const qrCodeUrl = data.actions.find(a => a.name === 'generate-qr-code').url;
                    html = `<div class="p-6 border-l-4 border-green-500 bg-green-50 text-center rounded-r-lg"><h4 class="font-bold text-lg text-green-800">Instruksi Pembayaran QRIS</h4><p class="mt-2 text-sm">Pindai kode QR di bawah ini menggunakan aplikasi e-wallet Anda.</p><div class="my-4 flex justify-center"><img src="${qrCodeUrl}" alt="QR Code Pembayaran" class="w-56 h-56 border p-1 bg-white"></div><p class="text-sm">Total Tagihan: <strong class="font-bold">${formatRupiah(data.gross_amount)}</strong></p></div>`;
                } else if (data.actions && data.actions.some(a => a.name === 'deeplink-redirect')) {
                    const gopayUrl = data.actions.find(a => a.name === 'deeplink-redirect').url;
                    html = `<div class="p-6 border-l-4 border-cyan-500 bg-cyan-50 text-center rounded-r-lg"><h4 class="font-bold text-lg text-cyan-800">Lanjutkan Pembayaran Gopay</h4><p class="mt-2 text-sm">Klik tombol di bawah untuk membuka aplikasi Gojek dan menyelesaikan pembayaran.</p><div class="my-4 flex justify-center"><a href="${gopayUrl}" class="bg-cyan-500 text-white font-bold py-3 px-6 rounded-lg hover:bg-cyan-600">Buka Aplikasi Gojek</a></div><p class="text-sm">Total Tagihan: <strong class="font-bold">${formatRupiah(data.gross_amount)}</strong></p></div>`;
                } else {
                    html = `<div class="p-6 border-l-4 border-gray-500 bg-gray-50 rounded-r-lg"><h4 class="font-bold text-lg">Pembayaran Diproses</h4><p class="mt-2 text-sm">Status pembayaran Anda sedang diproses. Silakan cek status pesanan Anda secara berkala.</p></div>`;
                }

                contentWrapper.innerHTML = html;
                const copyBtn = contentWrapper.querySelector('.copy-btn');
                if (copyBtn) {
                    copyBtn.onclick = () => {
                        navigator.clipboard.writeText(document.getElementById('va-number').textContent)
                            .then(() => Swal.fire({ icon: 'success', title: 'Berhasil Disalin!', showConfirmButton: false, timer: 1500 }));
                    };
                }
            }

            function addEventListeners() {
                // ... (event listener lain tetap sama) ...
                document.getElementById('delivery-method-options').addEventListener('change', e => {
                    const selectedMethod = e.target.value;
                    checkoutState.deliveryMethod = selectedMethod;
                    document.querySelectorAll('#delivery-method-options label').forEach(l => l.classList.remove('selected-option'));
                    e.target.closest('label').classList.add('selected-option');
                    document.getElementById('shipping-address-container').classList.toggle('hidden', selectedMethod !== 'ship');
                    document.querySelectorAll('.delivery-section-pickup').forEach(el => el.classList.toggle('hidden', selectedMethod === 'ship'));
                    document.querySelectorAll('.delivery-section-ship').forEach(el => el.classList.toggle('hidden', selectedMethod !== 'ship'));
                    if (selectedMethod === 'pickup') Object.keys(checkoutState.shops).forEach(shopId => { checkoutState.shops[shopId].shippingCost = 0; checkoutState.shops[shopId].shippingService = ''; });
                    renderSummary();
                });

                const searchInput = document.getElementById('destination_search');
                const resultsContainer = document.getElementById('destination-results');
                searchInput.addEventListener('keyup', () => {
                    clearTimeout(searchTimeout);
                    const keyword = searchInput.value;
                    if (keyword.length < 3) { resultsContainer.classList.add('hidden'); return; }
                    resultsContainer.classList.remove('hidden');
                    resultsContainer.innerHTML = '<div class="p-3 text-sm text-gray-500">Mencari...</div>';
                    searchTimeout = setTimeout(() => {
                        axios.post("{{ route('tenant.checkout.search_destination', ['subdomain' => request()->route('subdomain')]) }}", { keyword })
                            .then(response => {
                                resultsContainer.innerHTML = '';
                                if (response.data.length > 0) {
                                    response.data.forEach(area => {
                                        const postalCode = area.name.split('. ')[1] || '';
                                        const displayName = `${area.administrative_division_level_3_name}, ${area.administrative_division_level_2_name}`;
                                        const item = document.createElement('div');
                                        item.className = 'p-3 text-sm cursor-pointer hover:bg-gray-100 border-b';
                                        item.textContent = `${displayName} (${postalCode})`;
                                        item.addEventListener('click', () => selectDestination(postalCode, displayName));
                                        resultsContainer.appendChild(item);
                                    });
                                } else { resultsContainer.innerHTML = '<div class="p-3 text-sm text-gray-500">Lokasi tidak ditemukan.</div>'; }
                            }).catch(err => resultsContainer.innerHTML = '<div class="p-3 text-sm text-red-500">Gagal mencari lokasi.</div>');
                    }, 500);
                });

                document.getElementById('payment-method-options').addEventListener('click', e => {
                    const label = e.target.closest('.payment-option-label');
                    if (label) { document.querySelectorAll('.payment-option-label').forEach(l => l.classList.remove('selected-option')); label.classList.add('selected-option'); }
                });

                document.querySelector('#voucher-modal .modal-close').addEventListener('click', () => voucherModal.classList.add('hidden'));
                document.getElementById('voucher-modal-body').addEventListener('click', e => {
                    const voucherItem = e.target.closest('.voucher-item-modal:not(.disabled-item)');
                    if (!voucherItem) return;
                    const shopId = voucherItem.dataset.shopId;
                    const shopState = checkoutState.shops[shopId];
                    shopState.voucherId = voucherItem.dataset.id;
                    shopState.voucherCode = voucherItem.dataset.code;
                    shopState.voucherDiscount = (shopState.subtotal * parseFloat(voucherItem.dataset.discountPercent)) / 100;
                    document.getElementById(`selected-voucher-info-${shopId}`).innerHTML = `<span>Voucher <strong>${shopState.voucherCode}</strong> diterapkan.</span> <button type="button" class="btn-remove-voucher text-red-500 hover:text-red-700" data-shop-id="${shopId}">&times;</button>`;
                    document.getElementById(`selected-voucher-info-${shopId}`).classList.remove('hidden');
                    renderSummary();
                    voucherModal.classList.add('hidden');
                });

                wrapper.addEventListener('click', e => {
                    if (e.target.classList.contains('btn-select-voucher')) openVoucherModal(e.target.dataset.shopId);
                    if (e.target.classList.contains('btn-remove-voucher')) {
                        const shopId = e.target.dataset.shopId;
                        const shopState = checkoutState.shops[shopId];
                        shopState.voucherId = null; shopState.voucherCode = ''; shopState.voucherDiscount = 0;
                        document.getElementById(`selected-voucher-info-${shopId}`).classList.add('hidden');
                        renderSummary();
                    }
                });

                wrapper.addEventListener('change', e => {
                    if (e.target.classList.contains('shipping-radio')) {
                        const shopId = e.target.dataset.shopId;
                        checkoutState.shops[shopId].shippingCost = parseFloat(e.target.value);
                        checkoutState.shops[shopId].shippingService = e.target.dataset.serviceName;
                        renderSummary();
                    }
                });

                // PERBAIKAN: Event listener untuk input catatan
                wrapper.addEventListener('input', e => {
                    if (e.target.classList.contains('shop-notes-input')) {
                        const shopId = e.target.dataset.shopId;
                        checkoutState.shops[shopId].notes = e.target.value;
                    }
                });

                document.getElementById('checkout-form').addEventListener('submit', e => {
                    e.preventDefault();
                    const payButton = document.getElementById('btn-process-payment');
                    payButton.disabled = true; payButton.innerHTML = 'Memproses...';

                    if (checkoutState.deliveryMethod === 'ship') {
                        for (const shopId in checkoutState.shops) {
                            if (!checkoutState.shops[shopId].shippingService) {
                                const shopData = checkoutState.fullData.shopsData.find(s => s.shop.id == shopId);
                                const shopName = shopData ? shopData.shop.shop_name : `toko`;
                                Swal.fire('Pengiriman Belum Dipilih', `Silakan pilih layanan pengiriman untuk ${shopName}.`, 'warning');
                                payButton.disabled = false; payButton.innerHTML = 'Bayar Sekarang';
                                return;
                            }
                        }
                    }

                    if (!document.querySelector('input[name="payment_method"]:checked')) {
                        Swal.fire('Peringatan', 'Silakan pilih metode pembayaran.', 'warning');
                        payButton.disabled = false; payButton.innerHTML = 'Bayar Sekarang'; return;
                    }

                    const dataToSend = {
                        _token: document.querySelector('input[name="_token"]').value, items: checkoutItemIds,
                        delivery_method: checkoutState.deliveryMethod, payment_method: document.querySelector('input[name="payment_method"]:checked').value,
                        alamat: document.getElementById('alamat').value, shops: checkoutState.shops
                    };
                    axios.post("{{ route('tenant.checkout.charge', ['subdomain' => request()->route('subdomain')]) }}", dataToSend)
                        .then(response => {
                            handlePaymentResponse(response.data);
                        })
                        .catch(err => {
                            Swal.fire('Error', err.response?.data?.error || 'Terjadi kesalahan.', 'error');
                            payButton.disabled = false; payButton.innerHTML = 'Bayar Sekarang';
                        });
                });
            }

            function initCheckout() {
                axios.post("{{ route('tenant.checkout.get_details', ['subdomain' => request()->route('subdomain')]) }}", { items: checkoutItemIds })
                    .then(response => {
                        renderCheckoutPage(response.data);
                    })
                    .catch(error => {
                        document.getElementById('checkout-loader').innerHTML = '<p class="text-red-500">Gagal memuat data pesanan. Silakan coba lagi.</p>';
                        console.error("Error fetching checkout details:", error);
                    });
            }
            initCheckout();
        });
    </script>
@endpush