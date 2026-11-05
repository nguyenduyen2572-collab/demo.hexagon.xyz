/* HEXAGON auth UX: login requires both identity and password before enabling submit.
   Does not change server-side validation or authentication. */
(() => {
    'use strict';

    function initializeLoginForms() {
        const path = window.location.pathname.replace(/\/+$/, '') || '/';
        const isLoginPage = /^\/(?:customer|admin)\/login$/i.test(path) ||
            document.body?.dataset?.authPage === 'login';
        if (!isLoginPage) return;

        document.querySelectorAll('form').forEach((form) => {
            const password = form.querySelector(
                'input[name="password"], input[autocomplete="current-password"], input[type="password"], input[id="password"]'
            );
            const identity = form.querySelector(
                'input[name="email"], input[name="username"], input[name="login"], input[autocomplete="username"], input[type="email"], input[id="email"]'
            );
            const submit = form.querySelector('button[type="submit"], input[type="submit"]');
            if (!identity || !password || !submit) return;

            submit.setAttribute('data-hx-login-submit', '');

            const sync = () => {
                const hasIdentity = identity.value.trim().length > 0;
                const hasPassword = password.value.trim().length > 0;
                submit.disabled = !(hasIdentity && hasPassword);
            };

            form.addEventListener('input', sync);
            form.addEventListener('change', sync);
            form.addEventListener('reset', () => queueMicrotask(sync));
            form.addEventListener('submit', (event) => {
                sync();
                if (submit.disabled) event.preventDefault();
            });
            window.addEventListener('pageshow', sync);
            document.addEventListener('visibilitychange', sync);

            sync();
            // Some browsers/password managers fill values just after DOM readiness.
            window.requestAnimationFrame(sync);
            window.setTimeout(sync, 150);
            window.setTimeout(sync, 700);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeLoginForms, { once: true });
    } else {
        initializeLoginForms();
    }
})();
