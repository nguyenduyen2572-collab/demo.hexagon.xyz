/* HEXAGON Forgot Password V1 — bilingual inline validation, no native browser popups. */
(() => {
    'use strict';
    const form = document.getElementById('hxForgotForm');
    if (!form) return;
    const email = form.querySelector('input[name="email"]');
    const languageField = form.querySelector('input[name="hx_locale"]');
    const error = document.getElementById('hxForgotEmailError');
    const toggle = document.getElementById('hxLangToggle');
    if (!email || !error || !languageField) return;

    const lang = () => {
        try { return localStorage.getItem('hexagon-language') === 'en' ? 'en' : 'vi'; }
        catch (_) { return 'vi'; }
    };
    const words = {
        vi: {
            empty: 'Vui lòng nhập địa chỉ email.',
            invalid: 'Địa chỉ email không đúng định dạng.'
        },
        en: {
            empty: 'Please enter your email address.',
            invalid: 'Please enter a valid email address.'
        }
    };
    let localError = '';

    function displayLocalError() {
        if (!localError) return;
        error.textContent = words[lang()][localError];
        email.setAttribute('aria-invalid', 'true');
    }
    function resetError() {
        localError = '';
        error.textContent = '';
        email.setAttribute('aria-invalid', 'false');
    }
    languageField.value = lang();

    email.addEventListener('input', resetError);
    if (toggle) toggle.addEventListener('click', () => {
        languageField.value = lang();
        displayLocalError();
    });
    form.addEventListener('submit', (event) => {
        languageField.value = lang();
        email.value = email.value.trim();
        if (!email.value) localError = 'empty';
        else if (!email.checkValidity()) localError = 'invalid';
        else localError = '';

        if (localError) {
            event.preventDefault();
            displayLocalError();
            email.focus();
        } else {
            error.textContent = '';
            email.setAttribute('aria-invalid', 'false');
        }
    });
})();
