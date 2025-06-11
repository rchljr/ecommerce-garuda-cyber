<!-- pricing section start -->
<section id="paket" class="pricing-section">
    <h1 class="pricing-title">Pilih Paket Yang Tepat Untuk Anda</h1>
    <p class="pricing-subtitle">Pilih paket yang paling sesuai untuk Anda, jangan ragu untuk menghubungi kami</p>
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
            <button class="plan-btn">Get Started</button>
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
            <button class="plan-btn">Get Started</button>
        </div>

        <!-- Enterprise Plan -->
        <div class="pricing-card" data-monthly-price="" data-yearly-price="">
            <div class="plan-name">Enterprise Plan</div>
            <div class="plan-desc"><span style="font-weight:700;">Hubungi langsung ke perusahaan untuk paket custom
                    dengan fitur sesuai permintaan Anda.</span></div>
            <ul class="plan-features">
                <li>Fitur dapat di-request</li>
                <li>Metode pembayaran fleksible</li>
                <li>Manajemen penyimpanan</li>
                <li>Custom domain</li>
            </ul>
            <button class="plan-btn">Hubungi Kami</button>
        </div>
    </div>
</section>
<!-- pricing section end -->

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtns = document.querySelectorAll('.toggle-btn');
            const pricingCards = document.querySelectorAll('.pricing-card');

            toggleBtns.forEach(btn => {
                btn.addEventListener('click', function () {
                    // Hapus class active dari semua toggle
                    toggleBtns.forEach(b => b.classList.remove('active'));
                    // Tambah active ke yang diklik
                    this.classList.add('active');

                    // Tentukan mode (bulanan/tahunan)
                    const isMonthly = this.textContent.includes('Bulanan');

                    // Update harga & caption di semua card
                    pricingCards.forEach(card => {
                        const priceElement = card.querySelector('.plan-price');
                        const captionElement = card.querySelector('.plan-caption');
                        const monthlyPrice = card.getAttribute('data-monthly-price');
                        const yearlyPrice = card.getAttribute('data-yearly-price');

                        priceElement.textContent = isMonthly ? monthlyPrice : yearlyPrice;
                        captionElement.textContent = isMonthly
                            ? 'Per pengguna/bulan, ditagih bulanan'
                            : 'Per pengguna/tahun, ditagih tahunan';
                    });
                });
            });
        });
    </script>
@endpush