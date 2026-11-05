/* HEXAGON: eye icon password visibility; no external libraries. */
(() => {
    'use strict';
    const buttons = Array.from(document.querySelectorAll('[data-password-toggle]'));
    if (!buttons.length) return;

    function language() {
        try { return localStorage.getItem('hexagon-language') === 'en' ? 'en' : 'vi'; }
        catch (_) { return 'vi'; }
    }
    const labels = {
        vi: { show: 'Hiển thị mật khẩu', hide: 'Ẩn mật khẩu' },
        en: { show: 'Show password', hide: 'Hide password' }
    };
    function updateLabels() {
        const words = labels[language()];
        buttons.forEach(button => {
            const input = document.getElementById(button.dataset.target || '');
            if (!input) return;
            const revealed = input.type === 'text';
            const label = revealed ? words.hide : words.show;
            button.setAttribute('aria-label', label);
            button.title = label;
            button.setAttribute('aria-pressed', String(revealed));
        });
    }
    buttons.forEach(button => {
        const input = document.getElementById(button.dataset.target || '');
        if (!input) return;
        button.addEventListener('click', () => {
            const visible = input.type === 'password';
            input.type = visible ? 'text' : 'password';
            updateLabels();
            input.focus({ preventScroll: true });
        });
    });
    document.getElementById('hxLangToggle')?.addEventListener('click', updateLabels);
    document.getElementById('langSwitch')?.addEventListener('click', updateLabels);
    window.addEventListener('storage', (event) => {
        if (event.key === 'hexagon-language') updateLabels();
    });
    updateLabels();
})();
