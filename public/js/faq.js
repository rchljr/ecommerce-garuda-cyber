document.querySelectorAll('.faq-question').forEach(btn => {
    btn.addEventListener('click', function () {
        const item = this.parentElement;
        document.querySelectorAll('.faq-item').forEach(faq => {
            if (faq !== item) faq.classList.remove('active');
        });
        item.classList.toggle('active');
    });
});
