<div class="w-full mx-auto mb-4 px-4">
    <div class="flex items-start">
        @php
            $steps = [
                0 => 'Pilih Paket',
                1 => 'Subdomain',
                2 => 'Data Diri',
                3 => 'Data Toko',
                4 => 'Verifikasi',
                5 => 'Pembayaran'
            ];
            $totalSteps = count($steps);
        @endphp

        @foreach ($steps as $index => $label)
            <div class="flex items-center {{ $index > 0 ? 'flex-1' : '' }}">
                {{-- Garis Konektor --}}
                @if ($index > 0)
                    <div class="flex-1 h-1 {{ $currentStep >= $index ? 'bg-red-600' : 'bg-gray-300' }}"></div>
                @endif

                {{-- Lingkaran Step --}}
                <div class="flex flex-col items-center">
                    <div
                        class="w-10 h-10 rounded-full flex items-center justify-center text-white
                            {{ $currentStep > $index ? 'bg-red-600' : ($currentStep == $index ? 'bg-red-600' : 'bg-gray-300') }}">
                        @if ($currentStep > $index)
                            <span class="font-bold">&#10003;</span> {{-- Tanda centang --}}
                        @else
                            {{ $index + 1 }}
                        @endif
                    </div>
                    <p
                        class="mt-2 text-xs text-center hidden md:block {{ $currentStep >= $index ? 'text-gray-800 font-semibold' : 'text-gray-500' }}">
                        {{ $label }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>