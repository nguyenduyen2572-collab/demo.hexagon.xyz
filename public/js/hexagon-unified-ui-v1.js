/* HEXAGON • Unified public header. No API calls, no authentication logic. */
(function () {
  'use strict';
  function init() {
    var actions = document.querySelector('.hx-header .hx-header-actions');
    if (actions) {
      // Move the actual node so keyboard focus follows the visual order.
      var login = actions.querySelector('.hx-header-login');
      var language = actions.querySelector('.hx-language');
      // Preserve Login → Register → Language when registration CTA is present.
      if (login && language && !actions.querySelector('.hx-header-register') && login.nextElementSibling !== language) {
        actions.insertBefore(login, language);
      }
    }

    var nav = document.querySelector('.hx-header .hx-main-nav');
    if (nav) {
      var links = Array.prototype.slice.call(nav.querySelectorAll('a[href]'));
      function highlight() {
        var hash = window.location.hash;
        var chosen = null;
        links.forEach(function (a) {
          var href = a.getAttribute('href') || '';
          if (hash && (href === hash || href.endsWith('/' + hash))) chosen = a;
        });
        if (!chosen && window.location.pathname === '/') {
          chosen = links.find(function (a) { return a.getAttribute('href') === '#domain-search'; });
        }
        if (chosen) {
          links.forEach(function (a) { a.removeAttribute('aria-current'); });
          chosen.setAttribute('aria-current', 'page');
        }
      }
      highlight();
      window.addEventListener('hashchange', highlight);
    }

    // Remove nonessential *promotional* demo labels, not order/price disclosures.
    var notice = document.querySelector('.hx-announcement');
    if (notice && /giai đoạn (?:trải nghiệm|thử nghiệm)|đang trong giai đoạn.*hoàn thiện/i.test(notice.textContent || '')) {
      notice.remove();
    }
    document.querySelectorAll('.hx-preview-live').forEach(function (badge) {
      if ((badge.textContent || '').trim().toUpperCase() === 'DEMO') badge.remove();
    });
    document.querySelectorAll('.hx-footer-trial,.hx-footer-demo').forEach(function (badge) {
      if (/phiên bản thử nghiệm|demo/i.test(badge.textContent || '')) badge.remove();
    });
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
}());
