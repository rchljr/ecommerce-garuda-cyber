<footer class="bg-gray-800 text-white">
    <div class="container mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8">

            <!-- Layanan Pelanggan -->
            <div class="col-span-1">
                <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">Produk & Layanan</h3>
                <ul class="mt-4 space-y-2">
                    <li><a href="{{ route('landing') }}" class="text-base text-gray-300 hover:text-white">Buat Toko
                            Online</a></li>
                    <li><a href="{{ route('landing') }}#harga" class="text-base text-gray-300 hover:text-white">Harga
                            Paket</a></li>
                    <li><a href="{{ route('landing') }}#fitur" class="text-base text-gray-300 hover:text-white">Fitur
                            Kami</a></li>
                    <li><a href="{{ route('tenants.index') }}" class="text-base text-gray-300 hover:text-white">Jelajahi
                            Toko</a></li>
                </ul>
            </div>

            <!-- Jelajahi Kami -->
            <div class="col-span-1">
                <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">Perusahaan</h3>
                <ul class="mt-4 space-y-2">
                    <li><a href="{{ route('landing') }}" class="text-base text-gray-300 hover:text-white">Tentang
                            GCI</a></li>
                    <li><a href="{{ route('landing') }}" class="text-base text-gray-300 hover:text-white">Karir</a></li>
                    <li><a href="{{ route('landing') }}" class="text-base text-gray-300 hover:text-white">Blog</a></li>
                    <li><a href="{{ route('landing') }}" class="text-base text-gray-300 hover:text-white">Syarat &
                            Ketentuan</a></li>
                    <li><a href="{{ route('landing') }}" class="text-base text-gray-300 hover:text-white">Kebijakan
                            Privasi</a></li>
                </ul>
            </div>

            <!-- Pembayaran & Pengiriman -->
            <div class="col-span-2 md:col-span-2 lg:col-span-2">
                <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">Pembayaran</h3>
                <div class="flex flex-wrap gap-4 mt-4">
                    <div class="bg-white p-2 rounded-md border flex items-center justify-center">
                        <img src="{{ asset('images/bca.png')}}" alt="BCA" class="h-5">
                    </div>
                    <div class="bg-white p-2 rounded-md border flex items-center justify-center">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2e/BRI_2020.svg/2560px-BRI_2020.svg.png"
                            alt="BRI" class="h-5">
                    </div>
                    <div class="bg-white p-2 rounded-md border flex items-center justify-center">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/86/Gopay_logo.svg/2560px-Gopay_logo.svg.png"
                            alt="Gopay" class="h-4">
                    </div>
                    <div class="bg-white p-2 rounded-md border flex items-center justify-center">
                        <img src="{{ asset('images/qris.png')}}" alt="QRIS" class="h-5">
                    </div>
                </div>

                <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase mt-8">Pilihan Pengiriman</h3>
                <div class="flex flex-wrap gap-4 mt-4">
                    <div class="bg-white p-2 rounded-md border flex items-center justify-center">
                        <img src="{{ asset('images/jne.png')}}" alt="JNE" class="h-6">
                    </div>
                    <div class="bg-white p-2 rounded-md border flex items-center justify-center">
                        <img src="{{ asset('images/jnt.png')}}" alt="J&T" class="h-5">
                    </div>
                    <div class="bg-white p-2 rounded-md border flex items-center justify-center">
                        <img src="{{ asset('images/sicepat.png')}}" alt="SiCepat" class="h-5">
                    </div>
                </div>
            </div>

            <!-- Ikuti Kami -->
            <div class="col-span-2 md:col-span-1">
                <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">Ikuti Kami</h3>
                <ul class="mt-4 space-y-2">
                    <li><a href="https://www.facebook.com/GarudaCyber/?locale=id_ID"
                            class="flex items-center gap-2 text-base text-gray-300 hover:text-white"><i
                                class="fab fa-facebook-f w-5"></i> Facebook</a></li>
                    <li><a href="https://www.instagram.com/garudacyber/?hl=en"
                            class="flex items-center gap-2 text-base text-gray-300 hover:text-white"><i
                                class="fab fa-instagram w-5"></i> Instagram</a></li>
                    <li><a href="https://twitter.com/garudacyberid"
                            class="flex items-center gap-2 text-base text-gray-300 hover:text-white"><i
                                class="fab fa-twitter w-5"></i> Twitter</a></li>
                    <li><a href="https://www.linkedin.com/company/garuda-cyber-indonesia/posts/?feedView=all"
                            class="flex items-center gap-2 text-base text-gray-300 hover:text-white"><i
                                class="fab fa-linkedin-in w-5"></i> LinkedIn</a></li>
                </ul>
            </div>

        </div>

        <div class="mt-12 border-t border-gray-700 pt-8 text-center">
            <p class="text-base text-gray-400">&copy; {{ date('Y') }}
                {{ config('app.name', 'Garuda Cyber Indonesia') }}. All rights reserved.
            </p>
        </div>
    </div>
</footer>