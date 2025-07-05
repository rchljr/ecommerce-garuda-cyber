<div class="w-full mx-auto mb-4 px-2 sm:px-4 py-4">
    <div class="flex items-start justify-center">

        @foreach ($steps as $index => $label)
            <div class="flex items-center {{ $loop->first ? 'flex-initial' : 'flex-1' }}">

                {{-- Garis Konektor --}}
                @if (!$loop->first)
                    <div
                        class="flex-1 h-1 {{ $currentStep >= $index ? 'bg-red-600' : 'bg-gray-300' }} transition-colors duration-500 mx-1 sm:mx-2">
                    </div>
                @endif

                {{-- Lingkaran Step & Label --}}
                <div class="flex flex-col items-center flex-shrink-0">
                    <div
                        class="w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center text-white font-medium transition-all duration-500
                                {{ $currentStep > $index ? 'bg-red-600' : ($currentStep == $index ? 'bg-red-600 scale-110' : 'bg-gray-300') }}">
                        @if ($currentStep > $index)
                            <span class="text-lg md:text-xl font-bold">&#10003;</span> {{-- Tanda centang --}}
                        @else
                            <span class="text-sm md:text-base">{{ $loop->iteration }}</span>
                        @endif
                    </div>
                    <p class="mt-2 text-xs text-center font-semibold transition-colors duration-500
                            {{ $currentStep == $index ? 'block text-red-600' : 'hidden md:block' }}
                            {{ $currentStep > $index ? 'text-gray-800' : 'text-gray-500' }}">
                        {{ $label }}
                    </p>
                </div>
            </div>
        @endforeach
    </div>
</div>