@if (isset($previousStepUrl) && $previousStepUrl)
    <a href="{{ $previousStepUrl }}"
        class="inline-flex ml-6 items-center text-gray-600 hover:text-gray-900 mb-6 font-semibold">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
    </a>
@endif