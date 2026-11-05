/* HEXAGON About Page – accessible navigation and VI/EN switch, no dependencies. */
(() => {
  'use strict';
  const nav = document.getElementById('haNavigation');
  const menuButton = document.getElementById('haMenuButton');
  const toggle = document.getElementById('haLangToggle');
  const flag = document.getElementById('haLangFlag');
  const accountMenu = document.getElementById('haAccountMenu');

  let currentLanguage = 'vi';
  try { currentLanguage = localStorage.getItem('hexagon-language') === 'en' ? 'en' : 'vi'; } catch (_) { /* storage blocked */ }

  const applyLanguage = () => {
    const field = currentLanguage === 'en' ? 'data-hx-en' : 'data-hx-vi';
    document.documentElement.lang = currentLanguage;
    document.querySelectorAll('[data-hx-vi][data-hx-en]').forEach(node => {
      const translated = node.getAttribute(field);
      if (translated !== null && translated !== node.textContent) node.textContent = translated;
    });
    if (flag) {
      flag.src = currentLanguage === 'vi' ? flag.dataset.gb : flag.dataset.vn;
    }
    if (toggle) {
      const label = currentLanguage === 'vi' ? 'Switch to English' : 'Chuyển sang tiếng Việt';
      toggle.setAttribute('aria-label', label);
      toggle.title = label;
    }
    if (menuButton) menuButton.setAttribute('aria-label', currentLanguage === 'vi' ? 'Mở hoặc đóng menu' : 'Toggle navigation');
  };

  toggle?.addEventListener('click', () => {
    currentLanguage = currentLanguage === 'vi' ? 'en' : 'vi';
    try { localStorage.setItem('hexagon-language', currentLanguage); } catch (_) { /* storage blocked */ }
    applyLanguage();
  });

  const closeMenu = () => {
    nav?.classList.remove('ha-navigation-open');
    menuButton?.setAttribute('aria-expanded', 'false');
  };
  menuButton?.addEventListener('click', () => {
    const expanded = menuButton.getAttribute('aria-expanded') === 'true';
    nav?.classList.toggle('ha-navigation-open', !expanded);
    menuButton.setAttribute('aria-expanded', String(!expanded));
  });
  nav?.querySelectorAll('a').forEach(link => link.addEventListener('click', closeMenu));
  document.addEventListener('click', event => {
    if (nav && menuButton && !nav.contains(event.target) && !menuButton.contains(event.target)) closeMenu();
    if (accountMenu && !accountMenu.contains(event.target)) accountMenu.open = false;
  });
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape') {
      closeMenu();
      if (accountMenu?.open) { accountMenu.open = false; accountMenu.querySelector('summary')?.focus(); }
    }
  });
  applyLanguage();
})();
