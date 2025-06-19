@extends('layouts.auth')

@section('title', 'Registrasi')

@section('content')

    <div class="flex flex-col h-full">
        {{-- Progress Bar --}}
        @include('landing-page.auth.partials._progress', ['currentStep' => $step])

        {{-- Konten Step --}}
        <div class="flex-grow">
            @switch($step)
                @case(0)
                    {{-- `packages` dikirim dari AuthController::showRegisterForm --}}
                    @include('landing-page.auth.partials._step0', ['packages' => $packages ?? []])
                    @break
                @case(1)
                    @include('landing-page.auth.partials._step1')
                    @break
                @case(2)
                    @include('landing-page.auth.partials._step2')
                    @break
                @case(3)
                    {{-- `categories` dikirim dari AuthController::showRegisterForm --}}
                    @include('landing-page.auth.partials._step3', ['categories' => $categories ?? []])
                    @break
                @case(4)
                    @include('landing-page.auth.partials._step4')
                    @break
                @case(5)
                    {{-- `snapToken` dan `userPackage` dikirim dari PaymentController::createTransaction --}}
                    @include('landing-page.auth.partials._step5', ['snapToken' => $snapToken ?? null, 'userPackage' => $userPackage ?? null])
                    @break
                @default
                    @include('landing-page.auth.partials._step0', ['packages' => $packages ?? []])
                    {{-- Jika step tidak dikenali, tampilkan Step 0 sebagai default --}}
            @endswitch
        </div>
    </div>
@endsection

@push('scripts')
{{-- Script untuk toggle menu mobile --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Navbar Mobile
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        if (mobileMenuButton) {
            mobileMenuButton.addEventListener('click', function () {
                mobileMenu.classList.toggle('hidden');
            });
        }
    });
</script>

{{-- Script untuk toggle harga di Step 0 --}}
    @if(isset($step) && $step == 0)
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const monthlyBtn = document.getElementById('monthly-btn');
            const yearlyBtn = document.getElementById('yearly-btn');
            const priceDisplays = document.querySelectorAll('.price-display');
            const periodDisplays = document.querySelectorAll('.price-period');
            const billingPeriodInputs = document.querySelectorAll('.billing_period_input');

            function updateView(period) {
                // Update Harga dan Teks Periode
                priceDisplays.forEach(el => {
                    el.textContent = el.dataset[period];
                });
                periodDisplays.forEach(el => {
                    el.textContent = el.dataset[period];
                });
                billingPeriodInputs.forEach(input => {
                    input.value = period;
                });

                // Update Styling Tombol
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
    @endif

{{-- Script untuk Midtrans di Step 5 --}}
    @if(isset($step) && $step == 5 && isset($snapToken))
        {{-- Muat skrip Midtrans Snap --}}
        <script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
        <script type="text/javascript">
            // Buka popup pembayaran Midtrans
            snap.pay('{{ $snapToken }}', {
                onSuccess: function(result){
                    // Redirect atau tampilkan pesan sukses
                    console.log('Payment Success:', result);
                    window.location.href = "{{ route('mitra.dashboard') }}"; // Ganti dengan rute halaman sukses Anda
                },
                onPending: function(result){
                    // Redirect atau tampilkan pesan pending
                    console.log('Payment Pending:', result);
                    alert('Pembayaran Anda sedang diproses.');
                },
                onError: function(result){
                    // Tampilkan pesan error
                    console.log('Payment Error:', result);
                    alert('Terjadi kesalahan saat memproses pembayaran.');
                }
            });
        </script>
    @endif
@endpush
