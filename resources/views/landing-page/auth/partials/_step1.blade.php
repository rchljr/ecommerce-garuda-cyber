<div class="w-full mx-auto">
    <a href="{{ route('register.form', ['step' => 0]) }}"
        class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-8">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
    </a>

    <h2 class="text-3xl font-bold text-gray-900 mb-6">Tentukan Subdomain Toko Anda</h2>

    <form action="{{ route('register.step1') }}" method="POST"
        class="flex items-center border border-gray-300 rounded-lg p-0 bg-white h-[64px]">
        @csrf
        <label for="subdomain" class="sr-only">Nama Subdomain</label>
        {{-- PERBAIKAN: Menggunakan 'original_subdomain_input' untuk menampilkan kembali input pengguna --}}
        <input type="text" name="subdomain" id="subdomain" placeholder="Nama Subdomain"
            class="w-full h-full px-5 py-4 text-xl font-semibold text-gray-500 border-none focus:ring-0 bg-transparent"
            value="{{ session('register.original_subdomain_input') ?? '' }}">
        <button type="submit"
            class="bg-red-700 text-white font-bold px-8 py-4 h-full rounded-r-lg hover:bg-red-800 whitespace-nowrap text-lg">Cek
            Subdomain</button>
    </form>

    @if(session('register.subdomain_status'))
        <div class="mt-8">
            <h3 class="font-bold text-xl text-gray-800 mb-4">Subdomain Pilihan</h3>
            <div class="border border-gray-200 rounded-2xl p-7 flex justify-between items-center bg-white shadow-md">
                {{-- PERBAIKAN: Menggunakan 'subdomain_normalized' untuk menampilkan hasil yang sudah diformat --}}
                <p class="text-2xl font-bold text-gray-800">{{ session('register.subdomain_normalized') }}.garuda.id</p>
                <div class="flex items-center space-x-4">
                    @if(session('register.subdomain_status') == 'Tersedia')
                        <span class="text-base font-bold text-white px-6 py-2 rounded-full"
                            style="background-color: #65AE38;">Tersedia</span>
                        <form action="{{ route('register.step1') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" name="choose_subdomain" value="1"
                                class="bg-red-700 text-white font-bold px-8 py-2 rounded-full hover:bg-red-800 text-lg">Pilih</button>
                        </form>
                    @else
                        <span class="text-base font-bold text-white bg-red-600 px-6 py-2 rounded-full">Tidak Tersedia</span>
                        <button type="button"
                            class="bg-red-700 text-white font-bold px-8 py-2 rounded-full text-lg opacity-50 cursor-not-allowed">Pilih</button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>