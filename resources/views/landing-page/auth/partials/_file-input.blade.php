<div class="mb-4">
    <label for="{{ $name }}" class="block text-base font-semibold text-gray-800 mb-1">
        {{ $label1 ?? '' }}
        @if(isset($required) && $required)
            <span class="text-red-600">*</span>
        @else
            {{-- Menampilkan label2 jika ada, dan memastikan ada spasi --}}
            {{ $label2 ?? '' }} 
            <small class="font-normal">(Opsional)</small>
        @endif
    </label>
    
    <input 
        type="file" 
        id="{{ $name }}" 
        name="{{ $name }}" 
        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100"
        {{ (isset($required) && $required) ? 'required' : '' }}
        accept=".pdf,.jpg,.jpeg,.png">
    
    {{-- Tambahan: Pemberitahuan jenis dan ukuran file --}}
    <p class="mt-1 text-xs text-gray-500">
        Tipe file: PDF, JPG, JPEG, PNG (Maks. 2MB).
    </p>
</div>
