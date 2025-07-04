<div class="w-full mx-auto">
    @include('landing-page.auth.partials._back_button')
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6 text-center md:text-left">Tentukan Subdomain Toko Anda</h2>

    <form action="{{ route('register.subdomain.submit') }}" method="POST"
        class="flex flex-col md:flex-row md:items-center border border-gray-300 rounded-lg p-0 bg-white md:h-[64px] shadow-sm">
        @csrf
        <label for="subdomain" class="sr-only">Nama Subdomain</label>
        
        <input type="text" name="subdomain" id="subdomain" placeholder="Nama Subdomain Anda"
            class="w-full h-[60px] md:h-full px-5 py-4 text-lg md:text-xl font-semibold text-gray-700 border-none focus:ring-0 bg-transparent rounded-t-lg md:rounded-t-none md:rounded-l-lg"
            value="{{ session('register.original_subdomain_input') ?? '' }}">
        
        <button type="submit"
            class="w-full md:w-auto bg-red-700 text-white font-bold px-6 md:px-8 py-3 md:py-4 md:h-full rounded-b-lg md:rounded-b-none md:rounded-r-lg hover:bg-red-800 whitespace-nowrap text-base md:text-lg transition-colors">
            Cek Ketersediaan
        </button>
    </form>
    @error('subdomain_for_validation')
        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
    @enderror


    {{-- TAMPILAN HASIL (RESPONSIF) --}}
    @if(session('register.subdomain_status'))
        <div class="mt-8">
            <h3 class="font-bold text-lg md:text-xl text-gray-800 mb-4 text-center md:text-left">Subdomain Pilihan Anda:</h3>
            
            {{-- Di mobile (flex-col), elemen akan tersusun ke bawah. Di medium screen ke atas (md:flex-row), akan ke samping. --}}
            <div class="border border-gray-200 rounded-2xl p-4 md:p-6 flex flex-col md:flex-row md:justify-between md:items-center bg-white shadow-md">
                
                {{-- Nama Subdomain --}}
                <p class="text-xl md:text-2xl font-bold text-gray-800 text-center md:text-left mb-4 md:mb-0 break-all">
                    {{ session('register.subdomain_normalized') }}.ecommercegaruda.my.id
                </p>
                
                {{-- Grup Status & Tombol Aksi --}}
                <div class="flex flex-col md:flex-row items-center space-y-3 md:space-y-0 md:space-x-4 w-full md:w-auto">
                    @if(session('register.subdomain_status') == 'Tersedia')
                        <span class="w-full md:w-auto text-center text-base font-bold text-white px-4 py-2 rounded-full" style="background-color: #65AE38;">
                            Tersedia
                        </span>
                        <form action="{{ route('register.subdomain.submit') }}" method="POST" class="m-0 w-full md:w-auto">
                            @csrf
                            <button type="submit" name="choose_subdomain" value="1"
                                class="w-full md:w-auto bg-red-700 text-white font-bold px-8 py-2 rounded-full hover:bg-red-800 text-base md:text-lg transition-colors">
                                Pilih
                            </button>
                        </form>
                    @else
                        <span class="w-full md:w-auto text-center text-base font-bold text-white bg-red-600 px-4 py-2 rounded-full">
                            Tidak Tersedia
                        </span>
                        <button type="button"
                            class="w-full md:w-auto bg-red-700 text-white font-bold px-8 py-2 rounded-full text-base md:text-lg opacity-50 cursor-not-allowed">
                            Pilih
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>