/* HEXAGON V4 footer bilingual text. Works with existing VI/EN flags. */
(() => {
  'use strict';
  const display = () => {
    let language = 'vi';
    try { language = localStorage.getItem('hexagon-language') === 'en' ? 'en' : 'vi'; } catch (_) { /* storage disabled */ }
    document.querySelectorAll('[data-hx-vi][data-hx-en]').forEach(el => {
      const message = el.getAttribute(language === 'en' ? 'data-hx-en' : 'data-hx-vi');
      if (message && el.textContent !== message) el.textContent = message;
    });
  };
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', display, { once:true });
  else display();
  window.addEventListener('storage', display);
  document.addEventListener('click', e => {
    if (e.target.closest('#langSwitch, #hexagonLangToggle, [data-language-toggle]')) {
      requestAnimationFrame(display);
    }
  });
})();
