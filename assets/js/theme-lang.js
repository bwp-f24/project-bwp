// assets/js/theme-lang.js
(function () {
    try {
        const html = document.documentElement;
        const saved = localStorage.getItem('theme') || 'light';
        html.setAttribute('data-bs-theme', saved);

        // ربط عناصر القائمة
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.theme-opt');
            if (!btn) return;
            const val = btn.getAttribute('data-theme') || 'light';
            localStorage.setItem('theme', val);
            html.setAttribute('data-bs-theme', val === 'auto' ? (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light') : val);
        });

        // في وضع "auto" استمع لتغير نظام الجهاز
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            const cur = localStorage.getItem('theme') || 'light';
            if (cur === 'auto') {
                html.setAttribute('data-bs-theme', e.matches ? 'dark' : 'light');
            }
        });
    } catch (err) { }
})();
