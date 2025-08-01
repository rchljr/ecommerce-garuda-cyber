<div class="w-full mt-10 max-w-2xl mx-auto text-center">

    @if (isset($statusUser) && $statusUser->status == 'active')

        {{-- TAMPILAN KETIKA SUDAH DISETUJUI --}}
        <div class="border-2 border-red-300 rounded-lg p-8 md:p-12 bg-white"
            style="box-shadow: 8px 6px 10px 4px rgba(101, 174, 56, 0.15); border-radius: 16px;">
            <div class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center mx-auto mb-6">
                {{-- Ikon Centang --}}
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-red-600">Status: Disetujui</h2>
            <p class="mt-4 text-gray-600">
                Selamat! Pengajuan Anda telah kami setujui. Sebuah email yang berisi link untuk melanjutkan ke tahap
                pembayaran telah kami kirimkan.
            </p>
            <p class="mt-4 text-gray-800 font-semibold">
                Silakan periksa inbox atau folder spam pada email Anda.
            </p>
            <div class="mt-8">
                <a href="https://mail.google.com/" target="_blank"
                    class="inline-block bg-red-700 text-white font-bold px-8 py-3 rounded-lg hover:bg-red-800 text-lg transition-colors">
                    Buka Email Sekarang
                </a>
            </div>
        </div>

    @elseif (isset($statusUser) && $statusUser->status == 'inactive')

        {{-- TAMPILAN KETIKA DITOLAK --}}
        <div class="border-2 border-red-300 rounded-lg p-8 md:p-12 bg-white"
            style="box-shadow: 8px 6px 10px 4px rgba(220, 38, 38, 0.15); border-radius: 16px;">
            <div class="w-16 h-16 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                {{-- Ikon Silang --}}
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-red-700">Status: Ditolak</h2>
            <p class="mt-4 text-gray-600">
                Mohon maaf, pengajuan Anda <span class="font-semibold text-red-700">tidak dapat kami setujui</span> pada
                tahap ini.
            </p>
            <p class="mt-4 text-gray-800 font-semibold">
                Silakan cek email Anda untuk informasi lebih lanjut atau hubungi admin jika membutuhkan klarifikasi.
            </p>
            <div class="mt-8">
                <a href="mailto:support@domainanda.com"
                    class="inline-block bg-gray-700 text-white font-bold px-8 py-3 rounded-lg hover:bg-gray-800 text-lg transition-colors">
                    Hubungi Admin
                </a>
            </div>
        </div>

    @else

        {{-- TAMPILAN DEFAULT (MENUNGGU VERIFIKASI) --}}
        <div class="border-2 border-red-300 rounded-lg p-8 md:p-12 bg-white"
            style="box-shadow: 8px 6px 10px 4px rgba(178, 0, 0, 0.15); border-radius: 16px;">
            <div class="w-16 h-16 bg-yellow-400 rounded-full flex items-center justify-center mx-auto mb-6">
                {{-- Ikon Jam --}}
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-red-600">Status: Menunggu Verifikasi</h2>
            <p class="mt-4 text-gray-600">
                Terima kasih, data dan dokumen Anda sudah kami terima. Mohon tunggu proses verifikasi dari admin. Notifikasi
                persetujuan dan link pembayaran akan dikirimkan melalui email setelah proses selesai.
            </p>
        </div>

    @endif

    {{-- Di bagian bawah view verifikasi, setelah info status --}}
    <div class="mt-8 text-center">
        <p class="text-gray-600">Ingin mendaftar dengan data atau email yang berbeda?</p>
        <a href="{{ route('register.clear') }}" class="font-semibold text-red-600 hover:text-red-800 transition">
            Klik di sini untuk memulai pendaftaran baru.
        </a>
    </div>

</div>