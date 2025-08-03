<!-- Footer -->
<footer class="bg-[#37383C] text-gray-300">
    <div class="container mx-auto px-6 py-12">
        <div class="grid md:grid-cols-3 lg:grid-cols-4 gap-8">
            <!-- Kolom Logo -->
            <div class="col-span-1 lg:col-span-1">
                <a href="{{ route('landing') }}" title="Kembali ke landing">
                    <img src="{{ asset('images/logosabi.png') }}" alt="Garuda Cyber" />
                </a>
            </div>
            <!-- Kolom Navigasi -->
            <div class="col-span-1 md:col-start-2">
                <h4 class="font-semibold text-white mb-4">Navigasi</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('landing') }}" class="hover:text-white transition-colors">Beranda</a></li>
                    <li><a href="#layanan" class="hover:text-white transition-colors">Layanan</a></li>
                    <li><a href="#faq" class="hover:text-white transition-colors">FAQs</a></li>
                    <li><a href="#testimoni" class="hover:text-white transition-colors">Testimoni</a></li>
                </ul>
            </div>
            <!-- Kolom Kontak & Social Media -->
            <div class="col-span-full md:col-span-1 lg:col-span-2">
                <h4 class="font-semibold text-white mb-4">Hubungi Kami</h4>
                <!-- 1. Beri ID pada form dan input untuk JavaScript -->
                <form id="contact-us-form" class="flex">
                    <input type="email" id="user-email-input" name="email" placeholder="Masukkan alamat email Anda"
                        class="w-full px-4 py-2 text-gray-800 rounded-l-md focus:outline-none" required>
                    <!-- 2. Ganti teks tombol -->
                    <button type="submit"
                        class="bg-red-600 text-white px-4 py-2 font-bold rounded-r-md hover:bg-red-700">Kirim</button>
                </form>
                <div class="flex space-x-4 mt-6">
                    <a href="https://www.facebook.com/GarudaCyber/?locale=id_ID" target="_blank"
                        rel="noopener noreferrer" class="text-gray-400 hover:text-white" title="Facebook"><svg
                            class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                clip-rule="evenodd" />
                        </svg></a>
                    <a href="https://www.instagram.com/garudacyber/?hl=en" target="_blank" rel="noopener noreferrer"
                        class="text-gray-400 hover:text-white" title="Instagram"><svg class="w-6 h-6"
                            fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 012.792 2.792c.247.636.416 1.363.465 2.427.048 1.024.06 1.378.06 3.808s-.012 2.784-.06 3.808c-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-2.792 2.792c-.636.247-1.363.416-2.427.465-1.024.048-1.378.06-3.808.06s-2.784-.012-3.808-.06c-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-2.792-2.792c-.247-.636-.416-1.363-.465-2.427-.048-1.024-.06-1.378-.06-3.808s.012-2.784.06-3.808c.049-1.064.218-1.791.465-2.427a4.902 4.902 0 012.792-2.792c.636-.247 1.363.416 2.427.465C9.53 2.013 9.884 2 12.315 2zM12 7a5 5 0 100 10 5 5 0 000-10zm0 8a3 3 0 110-6 3 3 0 010 6zm5.25-9.75a1.25 1.25 0 100-2.5 1.25 1.25 0 000 2.5z"
                                clip-rule="evenodd" />
                        </svg></a>
                    <a href="https://www.linkedin.com/company/garuda-cyber-indonesia/posts/?feedView=all"
                        target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white"
                        title="LinkedIn"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path
                                d="M20.158 2H3.842C2.825 2 2 2.825 2 3.842v16.315C2 21.175 2.825 22 3.842 22h16.315C21.175 22 22 21.175 22 20.158V3.842C22 2.825 21.175 2 20.158 2zM8.508 19H5.5V8.5h3.008v10.5zM7.004 7.228a1.724 1.724 0 110-3.448 1.724 1.724 0 010 3.448zM19 19h-3v-5.34c0-1.359-.44-2.19-1.636-2.19-1.312 0-1.875.92-1.875 2.19V19h-3V8.5h3v1.35h.04c.4-.75 1.372-1.5 3.018-1.5 3.226 0 3.791 2.115 3.791 4.868V19z" />
                        </svg></a>
                    <a href="https://twitter.com/garudacyberid" target="_blank" rel="noopener noreferrer"
                        class="text-gray-400 hover:text-white" title="Twitter"><svg class="w-6 h-6" fill="currentColor"
                            viewBox="0 0 24 24" aria-hidden="true">
                            <path
                                d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.71v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                        </svg></a>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-8 pt-6 text-center text-sm">
            <p>
                Copyright &copy;
                <script>document.write(new Date().getFullYear())</script> All rights reserved | Ditenagai
                oleh
                <a href="{{ route('tim.developer') }}" target="_blank"
                    class="text-red-600 hover:underline font-medium">Tim E-Commerce Garuda</a>
                by <a href="https://pcr.ac.id/" target="_blank"
                    class="text-red-600 hover:underline font-medium">Politeknik Caltex Riau</a>.
            </p>
        </div>
    </div>
</footer>

@push('scripts')
    <script>
        document.getElementById('contact-us-form').addEventListener('submit', function (event) {
            // Mencegah form untuk submit secara normal
            event.preventDefault();

            // Ambil email yang diketik oleh pengguna
            const userEmail = document.getElementById('user-email-input').value;

            // Siapkan detail untuk email
            const recipientEmail = env('MAIL_USERNAME');
            const subject = 'Pertanyaan dari Website E-Commerce Garuda';
            const body = `Halo, saya ingin bertanya lebih lanjut.\n\nEmail saya: ${userEmail}`;

            // Buat dan buka link mailto
            window.location.href = `mailto:${recipientEmail}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
        });
    </script>
@endpush