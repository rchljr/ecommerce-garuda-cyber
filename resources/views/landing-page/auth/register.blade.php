{{-- resources/views/landing-page/auth/register.blade.php --}}

@extends('layouts.auth')
@section('title', 'Registrasi')

@section('content')
    <div class="flex flex-col h-full">
        {{-- Progress Bar --}}
        @include('landing-page.auth.partials._progress', ['currentStep' => $step, 'steps' => $steps])

        {{-- Konten Step --}}
        <div class="flex-grow">
            @switch($step)
                @case(0)
                    @include('landing-page.auth.partials._step0', ['packages' => $packages ?? []])
                    @break
                @case(1)
                    @include('landing-page.auth.partials._step1') {{-- Partial Subdomain --}}
                    @break
                @case(2)
                    @include('landing-page.auth.partials._step2') {{-- Partial Data Diri --}}
                    @break
                @case(3)
                    @include('landing-page.auth.partials._step3', ['categories' => $categories ?? []]) {{-- Partial Data Toko --}}
                    @break
                @case(4)
                    @if(isset($isBusinessPlan) && $isBusinessPlan)
                        {{-- Untuk Business Plan, step 4 adalah Pilih Template --}}
                        @include('landing-page.auth.partials._step1_template', ['templates' => $templates ?? []])
                    @else
                        {{-- Untuk Starter Plan, step 4 adalah Verifikasi --}}
                        @include('landing-page.auth.partials._step4', ['statusUser' => $statusUser ?? null])
                    @endif
                    @break

                @case(5)
                    @if(isset($isBusinessPlan) && $isBusinessPlan)
                        {{-- Untuk Business Plan, step 5 adalah Verifikasi --}}
                        @include('landing-page.auth.partials._step4', ['statusUser' => $statusUser ?? null])
                    @else
                        {{-- Untuk Starter Plan, step 5 adalah Pembayaran --}}
                        @include('landing-page.auth.partials._step5')
                    @endif
                    @break

                @case(6)
                    {{-- Step 6 hanya ada untuk Business Plan, yaitu Pembayaran --}}
                    @include('landing-page.auth.partials._step5')
                    @break
                @default
                    @include('landing-page.auth.partials._step0', ['packages' => $packages ?? []])
            @endswitch
        </div>
    </div>
@endsection

@push('scripts')
    {{-- ... script Anda yang sudah ada ... --}}
@endpush