<!DOCTYPE html>
<html lang="en">

<head>
    @section('title', 'Registrasi')
    @include('layouts._partials.head')
</head>
<style>
    .pricing-section {
        width: 100%;
        max-width: 1380px;
        display: flex;
        position: absolute;
        flex-direction: column;
        margin-top: auto;
    }

    .pricing-image {
        max-width: 100%;
        height: auto;
        border-radius: 16px;
        box-shadow: 0 2px 16px rgba(0, 0, 0, 0.07);
    }
</style>

<body>
    <div class="login-wrapper">
        <!-- KIRI -->
        @include('layouts._partials.auth')
        <!-- KANAN -->
        <div class="register-right">
            <div class="register-container">
                <div class="register-progress">
                    @for ($i = 0; $i <= 5; $i++)
                        <div class="register-progress-step {{ $step == $i ? 'active' : '' }}" data-step="{{ $i }}"></div>
                        @if ($i < 5)
                            <div class="register-progress-line {{ $step > $i ? 'active' : '' }}"></div>
                        @endif
                    @endfor
                </div>
                {{-- Step 0: Pilih Paket --}}
                @if ($step == 0)
                    <form action="{{ route('register.step0') }}" method="POST" class="pricing-section active" data-step="0">
                        @csrf
                        <h1 class="pricing-title">Pilih Paket Yang Tepat Untuk Anda</h1>
                        <p class="pricing-subtitle">Pilih paket yang paling sesuai untuk Anda, jangan ragu untuk menghubungi
                            kami</p>
                        <div class="pricing-toggle">
                            <button class="toggle-btn active">Tagihan Bulanan</button>
                            <button class="toggle-btn">Tagihan Tahunan</button>
                        </div>
                        <div class="pricing-cards">
                            <!-- Starter Plan -->
                            <div class="pricing-card" data-monthly-price="Rp150.000" data-yearly-price="Rp1.500.000">
                                <div class="plan-name">Starter Plan</div>
                                <div class="plan-price">Rp150.000</div>
                                <div class="plan-caption">Per pengguna/bulan, ditagih bulanan</div>
                                <div class="plan-desc">Untuk kebutuhan bisnis sederhana Anda</div>
                                <ul class="plan-features">
                                    <li>Template/tema default saja</li>
                                    <li>Metode pembayaran fleksible</li>
                                    <li>Manajemen penyimpanan</li>
                                    <li>Gratis subdomain (e.g., yourstore.sabi.com)</li>
                                </ul>
                                <div class="plan-note">Gratis Uji Coba 14 Hari</div>
                                <button type="submit" name="plan" value="starter" class="plan-btn">Get Started</button>
                            </div>

                            <!-- Business Plan -->
                            <div class="pricing-card" data-monthly-price="Rp300.000" data-yearly-price="Rp3.000.000">
                                <div class="plan-name">Business Plan</div>
                                <div class="plan-price">Rp300.000</div>
                                <div class="plan-caption">Per pengguna/bulan, ditagih bulanan</div>
                                <div class="plan-desc">Untuk bisnis menengah dan besar dengan kebutuhan lanjutan.</div>
                                <ul class="plan-features">
                                    <li>3 tema/template pilihan yang dapat digunakan</li>
                                    <li>Metode pembayaran fleksible</li>
                                    <li>Manajemen penyimpanan</li>
                                    <li>Gratis subdomain (e.g., yourstore.sabi.com)</li>
                                </ul>
                                <button type="submit" name="plan" value="business" class="plan-btn">Get Started</button>
                            </div>

                            <!-- Enterprise Plan -->
                            <div class="pricing-card" data-monthly-price="" data-yearly-price="">
                                <div class="plan-name">Enterprise Plan</div>
                                <div class="plan-desc"><span style="font-weight:700;">Hubungi langsung ke perusahaan untuk
                                        paket custom
                                        dengan fitur sesuai permintaan Anda.</span></div>
                                <ul class="plan-features">
                                    <li>Fitur dapat di-request</li>
                                    <li>Metode pembayaran fleksible</li>
                                    <li>Manajemen penyimpanan</li>
                                    <li>Custom domain</li>
                                </ul>
                                <button class="plan-btn">Hubungi Kami</button>
                            </div>
                    </form>
                @endif

                {{-- Step 1: Subdomain --}}
                @if ($step == 1)
                    <form action="{{ route('register.step1') }}" method="POST" class="subdomain-search-section active"
                        data-step="1">
                        @csrf
                        <h1 class="register-title">Tentukan Subdomian Toko Anda</h1>
                        <form class="subdomain-search-form">
                            <input type="text" class="subdomain-input" placeholder="Nama Subdomain">
                            <button type="submit" class="subdomain-search-btn">Cari Subdomain</button>
                        </form>
                        @if(session('subdomain_status'))
                            <div class="subdomain-recommend-title">Subdomain Pilihan</div>
                            <div class="subdomain-recommend-card">
                                <span class="subdomain-name"><b>{{ session('subdomain') }}</b></span>
                                <div>
                                    <span class="subdomain-status available">{{ session('subdomain_status') }}</span>
                                    <button class="subdomain-choose-btn">Pilih</button>
                                </div>

                            </div>
                        @endif
                    </form>
                @endif

                {{-- Step 2: Data Diri --}}
                @if ($step == 2)
                    <form action="{{ route('register.step2') }}" method="POST" class="register-form active" data-step="2">
                        @csrf
                        <h1 class="register-title">Lengkapi Data Diri Anda</h1>
                        <label for="username" class="register-form-label">Nama Lengkap <span
                                style="color:#B20000;">*</span></label>
                        <input type="username" id="username" class="register-form-input"
                            placeholder="Masukkan Nama Lengkap Anda" required>

                        <label for="position" class="register-form-label">Jabatan<span
                                style="color:#B20000;">*</span></label>
                        <input type="text" id="position" class="register-form-input" placeholder="Masukkan Jabatan Anda"
                            required>

                        <label for="email" class="register-form-label">Email <span style="color:#B20000;">*</span></label>
                        <input type="email" id="email" class="register-form-input" placeholder="Masukkan Email Anda"
                            required>

                        <label for="phone" class="register-form-label">No HP/Whatsapp <span
                                style="color:#B20000;">*</span></label>
                        <input type="text" id="phone" class="register-form-input" placeholder="Masukkan No HP/Whatsapp Anda"
                            required>

                        <label for="password" class="register-form-label">Kata Sandi <span
                                style="color:#B20000;">*</span></label>
                        <input type="password" id="password" class="register-form-input" placeholder="Masukkan kata sandi"
                            required minlength="8">
                        <span class="register-form-note">*Minimal 8 karakter</span>

                        <label for="password_confirmation" class="register-form-label">Konfirmasi Kata Sandi <span
                                style="color:#B20000;">*</span></label>
                        <input type="password" id="password_confirmation" class="register-form-input"
                            placeholder="Masukkan ulang kata sandi" required minlength="8">
                        <span class="register-form-note">*Minimal 8 karakter</span>

                        <button type="submit" class="register-btn">Lanjut</button>
                    </form>
                @endif

                {{-- Step 3: Data Toko --}}
                @if ($step == 3)
                    <form action="{{ route('register.step3') }}" method="POST" enctype="multipart/form-data"
                        class="register-form active" data-step="3">
                        @csrf
                        <h1 class="register-title">Lengkapi Data Toko Anda</h1>
                        <div class="register-form-row">
                            <div class="register-form-group">
                                <label for="shop_name" class="register-form-label">Nama Toko <span
                                        style="color:#B20000;">*</span></label>
                                <input type="text" id="shop_name" class="register-form-input"
                                    placeholder="Masukkan Nama Toko Anda">
                            </div>
                            <div class="register-form-group">
                                <label for="year_founded" class="register-form-label">Tahun Berdiri
                                    <small>(Opsional)</small></label>
                                <input type="date" id="year_founded" class="register-form-input">
                            </div>
                        </div>

                        <div class="register-form-row">
                            <div class="register-form-group">
                                <label for="shop_address" class="register-form-label">Alamat Lengkap Toko <span
                                        style="color:#B20000;">*</span></label>
                                <input type="text" id="shop_address" class="register-form-input"
                                    placeholder="Masukkan Alamat Lengkap Toko" required>
                            </div>
                            <div class="register-form-group">
                                <label for="product_categories" class="register-form-label">Kategori Produk Toko <span
                                        style="color:#B20000;">*</span></label>
                                <select id="product_categories" class="register-form-input" required>
                                    <option value="">Pilih Kategori Produk</option>
                                    <option value="kuliner">Kuliner</option>
                                    <option value="jasa">Jasa</option>
                                    <option value="travel">Travel</option>
                                    <!-- Tambahkan sesuai kebutuhan -->
                                </select>
                            </div>
                        </div>

                        <div class="register-form-row">
                            <div class="register-form-group">
                                <label for="sku" class="register-form-label">Surat Keterangan Usaha (SKU)
                                    <small>(Opsional)</small></label>
                                <input type="file" id="sku" name="sku" class="register-form-input"
                                    accept=".pdf,.jpg,.jpeg,.png">
                                <span class="register-form-note">*File dengan ekstensi PDF, JPG, JPEG, PNG</span>
                            </div>
                            <div class="register-form-group">
                                <label for="shop_photo" class="register-form-label">Foto Tempat Usaha <span
                                        style="color:#B20000;">*</span></label>
                                <input type="file" id="shop_photo" name="shop_photo" class="register-form-input"
                                    accept="image/*" required>
                                <span class="register-form-note">*File dengan ekstensi JPG, JPEG, PNG</span>
                            </div>
                        </div>

                        <div class="register-form-row">
                            <div class="register-form-group">
                                <label for="npwp" class="register-form-label">Scan/Foto NPWP
                                    <small>(Opsional)</small></label>
                                <input type="file" id="npwp" name="npwp" class="register-form-input"
                                    accept=".pdf,.jpg,.jpeg,.png">
                                <span class="register-form-note">*File dengan ekstensi PDF, JPG, JPEG, PNG</span>
                            </div>
                            <div class="register-form-group">
                                <label for="ktp" class="register-form-label">Scan/Foto KTP <span
                                        style="color:#B20000;">*</span></label>
                                <input type="file" id="ktp" name="ktp" class="register-form-input"
                                    accept=".pdf,.jpg,.jpeg,.png" required>
                                <span class="register-form-note">*File dengan ekstensi PDF, JPG, JPEG, PNG</span>
                            </div>
                        </div>

                        <div class="register-form-row">
                            <div class="register-form-group">
                                <label for="nib" class="register-form-label">Nomor Induk Usaha (NIB)
                                    <small>(Opsional)</small></label>
                                <input type="file" id="nib" name="nib" class="register-form-input"
                                    accept=".pdf,.jpg,.jpeg,.png">
                                <span class="register-form-note">*File dengan ekstensi PDF, JPG, JPEG, PNG</span>
                            </div>
                            <div class="register-form-group">
                                <label for="iumk" class="register-form-label">Surat Izin Usaha Mikro dan Kecil (IUMK)
                                    <small>(Opsional)</small></label>
                                <input type="file" id="iumk" name="iumk" class="register-form-input"
                                    accept=".pdf,.jpg,.jpeg,.png">
                                <span class="register-form-note">*File dengan ekstensi PDF, JPG, JPEG, PNG</span>
                            </div>
                        </div>

                        <button type="submit" class="register-btn">Daftar</button>
                    </form>
                @endif

                {{-- Step 4: Status Verifikasi --}}
                @if ($step == 4)
                    <div class="register-status-section active" data-step="4">
                        <div class="register-status-icon">
                            <svg width="64" height="64" viewBox="0 0 64 64" fill="none">
                                <circle cx="32" cy="32" r="32" fill="#FFC738" />
                                <path d="M32 18v20" stroke="#B20000" stroke-width="3" stroke-linecap="round" />
                                <circle cx="32" cy="44" r="2.5" fill="#B20000" />
                            </svg>
                        </div>
                        <h1 class="register-status-title">Status: Menunggu Verifikasi</h1>
                        <p class="register-status-message">
                            <b>Terima kasih, data dan dokumen Anda sudah kami terima.</b><br>
                            Mohon tunggu proses verifikasi dari admin GCI sebelum lanjut ke pembayaran.<br>
                            <span style="color:#B20000;">Anda tidak bisa mengakses halaman pembayaran sebelum dokumen
                                diverifikasi.</span>
                        </p>
                        {{-- <div class="register-status-info">
                            <ul>
                                <li>Admin GCI akan memeriksa kelengkapan dan keaslian dokumen Anda.</li>
                                <li>Admin GCI akan memeriksa kelengkapan dan keaslian dokumen Anda.</li>
                                <li>Anda akan mendapatkan notifikasi setelah dokumen diverifikasi.</li>
                                <li>Jika dokumen ditolak, Anda dapat memperbaikinya dan mengirim ulang.</li>
                            </ul>
                        </div> --}}
                    </div>
                @endif

                {{-- Step 5: Pembayaran --}}
                @if ($step == 5)
                    <div class="register-payment-section active" data-step="5">
                        <h1 class="register-payment-title">Pembayaran</h1>
                        <p class="register-payment-subtitle">Silakan cek detail dan pilih metode pembayaran untuk
                            melanjutkan.</p>

                        <div class="payment-details">
                            <div class="payment-details-item">
                                <span>Nama Paket</span>
                                <span>Website UMKM Premium</span>
                            </div>
                            <div class="payment-details-item">
                                <span>Durasi</span>
                                <span>12 Bulan</span>
                            </div>
                            <div class="payment-details-item">
                                <span>Harga</span>
                                <span>Rp 550.000</span>
                            </div>
                            <div class="payment-details-item">
                                <span>Diskon</span>
                                <span>-</span>
                            </div>
                            <div class="payment-details-item total">
                                <span>Total Pembayaran</span>
                                <span class="amount">Rp 550.000</span>
                            </div>
                        </div>

                        <div class="register-payment-options">
                            <h2 class="payment-options-title">Pilih Metode Pembayaran</h2>
                            <div class="payment-option">
                                <input type="radio" id="qris" name="payment_method" value="qris" checked>
                                <label for="qris">
                                    <span class="payment-icon">
                                        <img src="images/qris.png" alt="QRIS" />
                                    </span>
                                    <span class="payment-label">QRIS</span>
                                </label>
                            </div>
                            <div class="payment-option">
                                <input type="radio" id="bni" name="payment_method" value="bni" checked>
                                <label for="bni">
                                    <span class="payment-icon">
                                        <img src="images/bni.png" alt="BNI" />
                                    </span>
                                    <span class="payment-label">Bank BNI (Virtual Account)</span>
                                </label>
                            </div>
                            <div class="payment-option">
                                <input type="radio" id="bri" name="payment_method" value="bri" checked>
                                <label for="bri">
                                    <span class="payment-icon">
                                        <img src="images/bri.png" alt="BRI" />
                                    </span>
                                    <span class="payment-label">Bank BRI (Virtual Account)</span>
                                </label>
                            </div>
                            <div class="payment-option">
                                <input type="radio" id="mandiri" name="payment_method" value="mandiri" checked>
                                <label for="mandiri">
                                    <span class="payment-icon">
                                        <img src="images/mandiri.png" alt="mandiri" />
                                    </span>
                                    <span class="payment-label">Bank Mandiri (Virtual Account)</span>
                                </label>
                            </div>

                            <button id="pay-button" class="register-btn">Bayar Sekarang</button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @include('layouts._partials.scripts')
</body>
</html>