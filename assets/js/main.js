// تحقق مبسّط لنموذج "اتصل بنا"
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('contactForm');
    if (!form) return;

    const alertBox = document.getElementById('formAlert');

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const fd = new FormData(form);
        const name = (fd.get('name') || '').trim();
        const email = (fd.get('email') || '').trim();
        const message = (fd.get('message') || '').trim();

        if (!name || !email || !message || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            alertBox.className = 'alert alert-danger';
            alertBox.textContent = 'يرجى ملء الحقول بشكل صحيح.';
            return;
        }
        alertBox.className = 'alert alert-success';
        alertBox.textContent = 'تم إرسال رسالتك بنجاح (نموذج واجهة فقط).';
        form.reset();
    });
});
// فلترة فورية للكروت في events.php
document.addEventListener('DOMContentLoaded', () => {
    const q = document.getElementById('liveFilter');
    if (!q) return;
    const cards = Array.from(document.querySelectorAll('.card .card-title'))
        .map(titleEl => ({ titleEl, card: titleEl.closest('.col-12, .col-md-6, .col-lg-4') || titleEl.closest('.card').parentElement }));

    q.addEventListener('input', () => {
        const needle = q.value.trim().toLowerCase();
        cards.forEach(({ titleEl, card }) => {
            const hay = (titleEl.textContent || '').toLowerCase() + ' ' +
                ((card.querySelector('.card-text')?.textContent) || '').toLowerCase();
            card.style.display = needle && !hay.includes(needle) ? 'none' : '';
        });
    });
});
