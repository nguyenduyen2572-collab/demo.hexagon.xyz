/* HEXAGON customer area: same-page tabs, VI/EN labels, password visibility. */
(() => {
    'use strict';
    const root = document.querySelector('[data-hx-settings]');
    const tabs = root ? [...root.querySelectorAll('[data-hx-tab]')] : [];
    const panels = root ? [...root.querySelectorAll('[data-hx-panel]')] : [];

    function selectTab(name, focus = false) {
        for (const tab of tabs) {
            const active = tab.dataset.hxTab === name;
            tab.setAttribute('aria-selected', active ? 'true' : 'false');
            tab.tabIndex = active ? 0 : -1;
            if (active && focus) tab.focus();
        }
        for (const panel of panels) panel.hidden = panel.dataset.hxPanel !== name;
    }

    for (const [index, tab] of tabs.entries()) {
        tab.addEventListener('click', () => selectTab(tab.dataset.hxTab));
        tab.addEventListener('keydown', event => {
            if (!['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) return;
            event.preventDefault();
            let nextIndex = index;
            if (event.key === 'ArrowLeft') nextIndex = (index - 1 + tabs.length) % tabs.length;
            if (event.key === 'ArrowRight') nextIndex = (index + 1) % tabs.length;
            if (event.key === 'Home') nextIndex = 0;
            if (event.key === 'End') nextIndex = tabs.length - 1;
            selectTab(tabs[nextIndex].dataset.hxTab, true);
        });
    }
    if (root) selectTab(root.dataset.initialTab === 'security' ? 'security' : 'profile');

    function getLanguage() {
        try { return localStorage.getItem('hexagon-language') === 'en' ? 'en' : 'vi'; }
        catch (_) { return 'vi'; }
    }

    function setLanguage(language) {
        const lang = language === 'en' ? 'en' : 'vi';
        document.documentElement.lang = lang;
        document.querySelectorAll('[data-vi][data-en]').forEach(el => {
            el.textContent = el.dataset[lang];
        });
        const flag = document.querySelector('[data-hx-flag]');
        const switcher = document.querySelector('[data-hx-language-switch]');
        if (flag) {
            flag.src = lang === 'vi' ? flag.dataset.flagEn : flag.dataset.flagVi;
            flag.alt = lang === 'vi' ? 'English' : 'Tiếng Việt';
        }
        if (switcher) {
            switcher.setAttribute('aria-label', lang === 'vi' ? 'Switch to English' : 'Chuyển sang tiếng Việt');
            switcher.title = switcher.getAttribute('aria-label');
        }
        try { localStorage.setItem('hexagon-language', lang); } catch (_) { /* Storage may be blocked. */ }
    }

    document.querySelector('[data-hx-language-switch]')?.addEventListener('click', () => {
        setLanguage(getLanguage() === 'vi' ? 'en' : 'vi');
    });
    setLanguage(getLanguage());

    document.querySelectorAll('[data-hx-eye]').forEach(button => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.hxEye);
            if (!input) return;
            const reveal = input.type === 'password';
            input.type = reveal ? 'text' : 'password';
            button.setAttribute('aria-pressed', reveal ? 'true' : 'false');
            const label = reveal
                ? (getLanguage() === 'vi' ? 'Ẩn mật khẩu' : 'Hide password')
                : (getLanguage() === 'vi' ? 'Hiển thị mật khẩu' : 'Show password');
            button.setAttribute('aria-label', label);
            button.title = label;
        });
    });
})();
