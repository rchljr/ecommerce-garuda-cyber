<div class="login-left">
    <div class="login-brand">
        <img src="/images/GCI.png" alt="E-COMMERCE" class="login-logo">
        <span class="login-brand-text">E-COMMERCE GCI</span>
    </div>
    <h1 class="login-title">Selamat Datang Kembali!</h1>
    <p class="login-desc">
        Masuk ke akun Anda untuk melanjutkan pengelolaan bisnis dengan solusi digital yang memudahkan setiap
        proses operasional dan monitoring secara efisien.
    </p>
    <div class="login-shape"></div>
    <div class="testimonial-slider">
        <div class="login-testimonial active">
            <div class="testimonial-title">Solusi UMKM Yang Praktis Dan Ekonomis!</div>
            <div class="testimonial-desc">
                Dengan Smart Bisnis, saya bisa membuat website toko online tanpa harus memiliki pengetahuan teknis.
                Biayanya juga sangat terjangkau untuk UMKM. Dukungan pelanggannya cepat dan ramah, saya sangat puas
                dengan layanan ini.
            </div>
            <div class="testimonial-bottom">
                <div class="testimonial-user">
                    <img src="../images/user.svg" alt="Amanda Lauren" class="testimonial-avatar">
                </div>
                <span class="testimonial-name">Amanda Lauren</span>
                <span class="testimonial-stars">★★★★★</span>
            </div>
        </div>
        <script>
            // Tempelkan di bawah sebelum </body> atau di @push('scripts')
            const testimonials = document.querySelectorAll('.login-testimonial');
            let current = 0;

            function showTestimonial(idx) {
                testimonials.forEach((el, i) => {
                    el.classList.toggle('active', i === idx);
                });
            }

            setInterval(() => {
                current = (current + 1) % testimonials.length;
                showTestimonial(current);
            }, 5000); // Ganti setiap 5 detik

            // Tampilkan testimonial pertama saat load
            showTestimonial(current);
        </script>
    </div>
</div>