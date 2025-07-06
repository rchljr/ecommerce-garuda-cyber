@include('landing-page.auth.partials._back_button')
<div class="w-full max-w-4xl mx-auto">

    <div class="text-center">
        <h2 class="text-3xl font-bold text-gray-900">Tentukan Alamat Toko Anda</h2>
        <p class="mt-2 text-gray-600">Pilih nama unik untuk URL toko online Anda. Cukup ketik dan kami akan mengeceknya
            secara otomatis.</p>
    </div>

    {{-- Input group dengan desain baru yang lebih menyatu --}}
    <div class="mt-8 relative">
        <div
            class="flex items-center bg-white rounded-xl shadow-lg border-2 border-gray-200 focus-within:border-red-500 focus-within:ring-2 focus-within:ring-red-200 transition-all duration-300 h-14 md:h-16">
            <input type="text" id="subdomain-input" placeholder="toko-baju-keren"
                class="w-full h-full pl-4 md:pl-5 text-base md:text-lg font-semibold text-gray-800 bg-transparent border-none focus:ring-0"
                value="{{ session('register.step_1.subdomain') ?? old('subdomain', session('register.original_subdomain_input')) }}">
            <span class="text-gray-400 text-sm md:text-lg pr-3 md:pr-4 whitespace-nowrap">.ecommercegaruda.my.id</span>
        </div>
    </div>

    {{-- Hasil Pengecekan dan Saran akan ditampilkan di sini --}}
    <div id="result-container" class="mt-4 min-h-[140px]">
        {{-- Konten ini akan diisi oleh JavaScript --}}
    </div>

</div>

@push('scripts')
    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.4s ease-out forwards;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('subdomain-input');
            const resultContainer = document.getElementById('result-container');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            let debounceTimer;
            let currentRequestController = null;

            const checkAvailability = async () => {
                const subdomainValue = input.value;
                if (!subdomainValue.trim()) {
                    resultContainer.innerHTML = '';
                    return;
                }

                if (currentRequestController) {
                    currentRequestController.abort();
                }
                currentRequestController = new AbortController();
                const signal = currentRequestController.signal;

                resultContainer.innerHTML = `
                    <div class="flex justify-center items-center gap-2 p-4 animate-fade-in">
                        <svg class="animate-spin h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-gray-500">Mengecek ketersediaan...</span>
                    </div>
                `;

                try {
                    const response = await fetch("{{ route('register.subdomain.check') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ subdomain: subdomainValue }),
                        signal: signal
                    });

                    if (!response.ok) {
                        throw new Error('Network response was not ok.');
                    }

                    const data = await response.json();
                    const normalizedSubdomain = input.value.trim().toLowerCase().replace(/\s+/g, '-').substring(0, 40);

                    let resultHtml = '';

                    if (data.available) {
                        // PERBAIKAN: Struktur HTML untuk hasil 'Tersedia' dibuat lebih responsif
                        resultHtml = `
                            <div class="bg-green-50 border-2 border-green-200 rounded-xl p-4 transition-all duration-300 animate-fade-in">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:gap-3">
                                    <div class="flex items-center gap-3 w-full sm:w-auto">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                        <div class="flex-grow">
                                            <p class="font-bold text-green-800">Subdomain Tersedia!</p>
                                            <p class="text-sm text-green-700 break-all">${normalizedSubdomain}.ecommercegaruda.my.id</p>
                                        </div>
                                    </div>
                                    <form action="{{ route('register.subdomain.submit') }}" method="POST" class="m-0 mt-4 sm:mt-0 sm:ml-auto w-full sm:w-auto">
                                        @csrf
                                        <input type="hidden" name="chosen_subdomain" value="${normalizedSubdomain}">
                                        <button type="submit" class="w-full sm:w-auto bg-red-600 text-white font-bold px-6 py-2 rounded-lg hover:bg-red-700 transition-colors whitespace-nowrap">
                                            Lanjutkan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        `;
                    } else {
                        let suggestionsHtml = '';
                        if (data.suggestions && data.suggestions.length > 0) {
                            suggestionsHtml = `
                                <div class="mt-3 pt-3 border-t border-red-200">
                                    <p class="text-sm text-gray-600 text-left">Mungkin Anda bisa coba:</p>
                                    <div class="flex flex-wrap gap-2 mt-2 justify-start">
                                        ${data.suggestions.map(s => `<button type="button" class="suggestion-btn bg-gray-200 text-gray-800 text-sm font-semibold px-3 py-1 rounded-full hover:bg-gray-300">${s}</button>`).join('')}
                                    </div>
                                </div>
                            `;
                        }

                        resultHtml = `
                            <div class="bg-red-50 border-2 border-red-200 rounded-xl p-4 transition-all duration-300 animate-fade-in">
                                <div class="flex items-center gap-3">
                                     <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </div>
                                    <div class="flex-grow">
                                        <p class="font-bold text-red-800">Tidak Tersedia</p>
                                        <p class="text-sm text-red-700">${data.message || 'Subdomain ini sudah digunakan.'}</p>
                                    </div>
                                </div>
                                ${suggestionsHtml}
                            </div>
                        `;
                    }
                    resultContainer.innerHTML = resultHtml;

                    document.querySelectorAll('.suggestion-btn').forEach(btn => {
                        btn.addEventListener('click', function () {
                            input.value = this.textContent;
                            input.focus();
                            checkAvailability();
                        });
                    });

                } catch (error) {
                    if (error.name === 'AbortError') {
                        console.log('Fetch aborted');
                        return;
                    }
                    console.error('Error:', error);
                    resultContainer.innerHTML = `<p class="text-center text-red-500 animate-fade-in">Terjadi kesalahan. Silakan coba lagi.</p>`;
                }
            };

            input.addEventListener('keyup', function (event) {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    checkAvailability();
                }, 500);
            });
        });
    </script>
@endpush