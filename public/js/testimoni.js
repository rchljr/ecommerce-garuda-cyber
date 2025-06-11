document.addEventListener('DOMContentLoaded', function () {
    const carousel = document.querySelector('.testimonial-carousel');
    const prevBtn = document.querySelector('.testimonial-btn.prev');
    const nextBtn = document.querySelector('.testimonial-btn.next');
    const card = document.querySelector('.testimonial-card');
    let scrollAmount = card ? card.offsetWidth + 32 : 340;

    if (carousel && prevBtn && nextBtn) {
        prevBtn.onclick = () => {
            carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        };
        nextBtn.onclick = () => {
            carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        };
    }
});
