{{-- resources/views/landing-page/auth/partials/_step1_template.blade.php --}}

<div class="w-full mx-auto py-10 px-4">
    @include('landing-page.auth.partials._back_button')
    
    <form action="{{ route('register.template.submit') }}" method="POST">
        @csrf

        <div class="text-center mb-8">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">Pilih Tampilan Website Anda</h2>
            <p class="text-gray-500">
                Berdasarkan kategori pilihan Anda, kami merekomendasikan template berikut.
            </p>
        </div>

        @if ($errors->any())
            <div class="max-w-4xl mx-auto bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong class="font-bold">Oops!</strong>
                <span class="block sm:inline">{{ $errors->first() }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse ($templates as $template)
                @php
                    $isRecommended = isset($recommendedCategorySlug) && $template->slug === $recommendedCategorySlug;
                @endphp

                <label class="relative bg-white rounded-2xl shadow-md flex flex-col transition-all duration-300 cursor-pointer hover:shadow-xl hover:-translate-y-1">
                    
                    @if($isRecommended)
                        <div class="absolute top-0 -right-3 -mt-3 z-10">
                            <span class="inline-flex items-center px-3 py-1 text-sm font-semibold text-white bg-red-600 rounded-full shadow-lg">
                                <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                Direkomendasikan
                            </span>
                        </div>
                    @endif

                    <input type="radio" name="template_id" value="{{ $template->id }}" class="sr-only peer" 
                           {{ $isRecommended ? 'checked' : '' }} 
                           required>

                    <img src="{{ asset('storage/' . $template->image_preview) }}" 
                         onerror="this.onerror=null;this.src='https://placehold.co/400x300/f1f5f9/cbd5e1?text=No+Image';"
                         alt="{{ $template->name }}" class="w-full h-48 object-cover rounded-t-lg">

                    <div class="p-5 flex flex-col flex-grow">
                        <h4 class="font-semibold text-gray-800 text-lg">{{ $template->name }}</h4>
                        <p class="font-normal text-gray-500 mt-1 text-sm flex-grow">{{ $template->description }}</p>
                        
                        <div class="mt-4">
                            <a href="{{ route('template.preview', $template) }}" 
                               target="_blank" 
                               class="inline-block w-full text-center font-semibold py-2 px-4 rounded-lg transition-colors bg-gray-200 text-gray-800 hover:bg-gray-300">
                                Lihat Preview
                            </a>
                        </div>
                    </div>

                    <div class="absolute inset-0 rounded-2xl pointer-events-none border-4 border-transparent peer-checked:border-red-500 transition-all">
                        <div class="absolute top-0 right-0 m-3 opacity-0 peer-checked:opacity-100 transition-opacity">
                            <svg class="w-8 h-8 text-white bg-red-600 rounded-full p-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                </label>
            @empty
                <div class="col-span-1 md:col-span-2 lg:col-span-3 w-full text-center text-gray-500 py-10">
                    <p>Tidak ada template yang tersedia saat ini.</p>
                </div>
            @endforelse
        </div>
        
        <div class="mt-12 text-center">
            <button type="submit" class="bg-red-600 text-white font-bold py-3 px-16 rounded-lg hover:bg-red-700 transition-colors">
                Daftar
            </button>
        </div>
    </form>
</div>
