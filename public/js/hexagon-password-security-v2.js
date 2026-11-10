/* Chỉ đồng bộ ngôn ngữ biểu mẫu với bộ chuyển VI/EN có sẵn. Không lưu mật khẩu. */
(() => {
    'use strict';
    const fields = [...document.querySelectorAll('[data-hx-password-locale]')];
    if (!fields.length) return;
    function sync() {
        let language = 'vi';
        try { language = localStorage.getItem('hexagon-language') === 'en' ? 'en' : 'vi'; }
        catch (_) { /* LocalStorage có thể bị tắt. */ }
        fields.forEach(field => { field.value = language; });
    }
    sync();
    document.addEventListener('click', event => {
        if (event.target.closest('[data-hx-language-switch], #hxLangToggle')) sync();
    });
    fields.forEach(field => field.form?.addEventListener('submit', sync));
})();
