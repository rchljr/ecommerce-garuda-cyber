<section id="harga" class="relative bg-white overflow-hidden py-2">
    <div class="absolute inset-0 bg-center bg-no-repeat opacity-20">
        <img src="{{ asset('images/bgpaket.png') }}" alt="Grid Background" class="w-full h-full object-cover">
    </div>
    <div class="relative container mx-auto px-4">
        <div class="text-center mb-8 max-w-3xl mx-auto">
            <h2 class="text-4xl font-bold text-gray-900">Pilih Paket Yang Tepat Untuk Anda</h2>
            <p class="mt-2 text-gray-600">Pilih paket yang paling sesuai untuk Anda, jangan ragu untuk menghubungi kami.
            </p>
        </div>

        {{-- Tombol Toggle Bulanan/Tahunan --}}
        <div class="flex justify-center mb-8">
            <div class="inline-flex rounded-lg p-1" style="background-color: #FDBC1A;">
                <button id="monthly-btn"
                    class="px-8 py-2 text-sm font-semibold text-white bg-red-600 rounded-md shadow-md">Tagihan
                    Bulanan</button>
                <button id="yearly-btn"
                    class="px-8 py-2 text-sm font-semibold text-gray-800 bg-transparent rounded-md ml-1">Tagihan
                    Tahunan</button>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 items-stretch max-w-6xl mx-auto">
            @forelse ($packages as $package)
                <div class="border-2 border-red-300 rounded-2xl p-8 flex flex-col bg-white shadow-lg">

                    {{-- Tampilkan harga jika bukan paket Enterprise --}}
                    @if (!is_null($package->monthly_price))
                        <h3 class="text-xl font-bold text-gray-900">{{ $package->package_name }}</h3>
                        <div class="my-4">
                            {{-- PERBAIKAN: Ukuran font harga dibuat responsif --}}
                            <span class="price-display text-3xl sm:text-4xl font-extrabold text-gray-800"
                                data-monthly="{{ format_rupiah($package->monthly_price) }}"
                                data-yearly="{{ format_rupiah($package->yearly_price) }}">
                                {{ format_rupiah($package->monthly_price) }}
                            </span>
                        </div>
                        <p class="price-period text-gray-500 text-sm mb-6" data-monthly="Per pengguna/bulan, ditagih bulanan"
                            data-yearly="Per pengguna/tahun (hemat {{ $package->discount_year ?? 0 }}%)">
                            Per pengguna/bulan, ditagih bulanan
                        </p>
                        <p class="font-semibold text-gray-800 mb-4">{{ $package->description }}</p>
                    @else
                        {{-- Tampilan khusus untuk paket Enterprise --}}
                        <h3 class="text-3xl font-bold text-gray-900">{{ $package->package_name }}</h3>
                        <p class="font-semibold text-gray-800 my-12">{{ $package->description }}</p>
                    @endif

                    <ul class="space-y-3 text-gray-600 mb-8 flex-grow">
                        @foreach ($package->features->sortBy('created_at') as $feature)
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-red-500 mr-2 flex-shrink-0 mt-1" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                {{ $feature->feature }}
                            </li>
                        @endforeach
                    </ul>
                    <div class="relative mt-auto pt-8">
                        @if($package->is_trial && !is_null($package->monthly_price))
                            <span
                                class="absolute top-4 left-0 text-xs text-white bg-red-600 px-3 py-1 rounded-full font-bold transform -rotate-12">
                                Gratis Uji Coba {{ $package->trial_days }} Hari
                            </span>
                        @endif

                        {{-- Tombol Aksi --}}
                        @if(!is_null($package->monthly_price))
                            <form action="{{ route('register.step0') }}" method="POST">
                                @csrf
                                <input type="hidden" name="plan" value="{{ $package->id }}">
                                <input type="hidden" class="billing_period_input" name="billing_period" value="monthly">
                                <button type="submit"
                                    class="w-full block text-center py-3 font-semibold text-gray-800 bg-yellow-400 rounded-lg hover:bg-yellow-500 transition-colors">
                                    Get Started
                                </button>
                            </form>
                        @else
                            <a href="#"
                                class="w-full block text-center py-3 font-semibold text-gray-800 bg-yellow-400 rounded-lg hover:bg-yellow-500 transition-colors">
                                Hubungi Kami
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="lg:col-span-3 md:col-span-2 text-center text-gray-500 py-8">
                    <p>Tidak ada paket berlangganan yang tersedia saat ini.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const monthlyBtn = document.getElementById('monthly-btn');
            const yearlyBtn = document.getElementById('yearly-btn');
            const priceDisplays = document.querySelectorAll('.price-display');
            const periodDisplays = document.querySelectorAll('.price-period');
            const billingPeriodInputs = document.querySelectorAll('.billing_period_input');

            function updateView(period) {
                priceDisplays.forEach(el => {
                    el.textContent = el.dataset[period] || '';
                });
                periodDisplays.forEach(el => {
                    el.textContent = el.dataset[period] || '';
                });
                billingPeriodInputs.forEach(input => {
                    input.value = period;
                });

                if (period === 'monthly') {
                    monthlyBtn.classList.add('bg-red-600', 'text-white', 'shadow-md');
                    monthlyBtn.classList.remove('bg-transparent', 'text-gray-800');
                    yearlyBtn.classList.add('bg-transparent', 'text-gray-800');
                    yearlyBtn.classList.remove('bg-red-600', 'text-white', 'shadow-md');
                } else { // yearly
                    yearlyBtn.classList.add('bg-red-600', 'text-white', 'shadow-md');
                    yearlyBtn.classList.remove('bg-transparent', 'text-gray-800');
                    monthlyBtn.classList.add('bg-transparent', 'text-gray-800');
                    monthlyBtn.classList.remove('bg-red-600', 'text-white', 'shadow-md');
                }
            }

            monthlyBtn.addEventListener('click', () => updateView('monthly'));
            yearlyBtn.addEventListener('click', () => updateView('yearly'));
        });
    </script>
@endpush