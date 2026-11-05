(() => {
    'use strict';
    const language = () => {
        try { return localStorage.getItem('hexagon-language') === 'en' ? 'en' : 'vi'; }
        catch (_) { return 'vi'; }
    };

    function update() {
        const lang = language();
        document.querySelectorAll('[data-saas-vi][data-saas-en]').forEach((el) => {
            el.textContent = lang === 'en' ? el.dataset.saasEn : el.dataset.saasVi;
        });
        document.querySelectorAll('[data-saas-currency]').forEach((el) => {
            const value = Number(el.dataset.saasCurrency);
            if (Number.isFinite(value)) {
                el.textContent = new Intl.NumberFormat(lang === 'en' ? 'en-US' : 'vi-VN', {
                    maximumFractionDigits: 0,
                }).format(value) + (lang === 'en' ? ' VND' : 'đ');
            }
        });
        const title = lang === 'en' ? document.body.dataset.saasTitleEn : document.body.dataset.saasTitleVi;
        if (title) document.title = title;
    }

    // hexagon-shop-i18n.js cập nhật localStorage trước listener này.
    document.getElementById('hexagonLangToggle')?.addEventListener('click', update);
    window.addEventListener('storage', update);
    update();
})();
