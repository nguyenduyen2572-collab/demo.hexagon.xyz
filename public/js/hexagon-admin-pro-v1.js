/* HEXAGON Admin Pro: client-side navigation only; no business-data or auth changes. */
(function () {
  'use strict';
  function ready() {
    var body = document.body;
    var shell = document.querySelector('[data-admin-shell]');
    var menu = document.getElementById('admin-sidebar');
    var mobileButton = document.querySelector('[data-sidebar-toggle]');
    var backdrop = document.querySelector('[data-sidebar-close]');
    var collapse = document.querySelector('[data-sidebar-collapse]');
    var quick = document.querySelector('[data-quick-search]');
    var searchBox = document.getElementById('hx-quick-input');
    var searchButtons = document.querySelectorAll('[data-search-open]');
    var searchClose = document.querySelectorAll('[data-search-close]');
    var items = Array.prototype.slice.call(document.querySelectorAll('[data-search-item]'));
    var savedFocus = null;
    var mobileQuery = window.matchMedia('(max-width: 960px)');

    function closeMenu() {
      body.classList.remove('hx-sidebar-open');
      if (mobileButton) { mobileButton.setAttribute('aria-expanded', 'false'); mobileButton.setAttribute('aria-label', 'Mở menu quản trị'); }
      if (backdrop) backdrop.hidden = true;
    }
    function openMenu() {
      body.classList.add('hx-sidebar-open');
      if (mobileButton) { mobileButton.setAttribute('aria-expanded', 'true'); mobileButton.setAttribute('aria-label', 'Đóng menu quản trị'); }
      if (backdrop) backdrop.hidden = false;
      if (menu) { var first = menu.querySelector('.nav-link'); if (first) first.focus(); }
    }
    if (mobileButton) mobileButton.addEventListener('click', function () { body.classList.contains('hx-sidebar-open') ? closeMenu() : openMenu(); });
    if (backdrop) backdrop.addEventListener('click', closeMenu);
    if (menu) menu.querySelectorAll('a').forEach(function (link) { link.addEventListener('click', closeMenu); });
    if (mobileQuery.addEventListener) mobileQuery.addEventListener('change', function () { closeMenu(); });

    try { if (localStorage.getItem('hexagon_admin_sidebar_compact') === '1') body.classList.add('hx-sidebar-compact'); } catch (_) {}
    if (collapse) collapse.addEventListener('click', function () {
      body.classList.toggle('hx-sidebar-compact');
      try { localStorage.setItem('hexagon_admin_sidebar_compact', body.classList.contains('hx-sidebar-compact') ? '1' : '0'); } catch (_) {}
    });

    function normalize(value) {
      return String(value || '').toLocaleLowerCase('vi').normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/đ/g, 'd');
    }
    function filter() {
      var query = normalize(searchBox && searchBox.value.trim());
      var visible = 0;
      items.forEach(function (link) {
        var matches = normalize(link.textContent + ' ' + link.getAttribute('data-keywords')).includes(query);
        link.hidden = !matches;
        if (matches) visible++;
      });
      if (quick) quick.classList.toggle('hx-quick-empty', visible === 0);
    }
    function openSearch() {
      if (!quick) return;
      closeMenu();
      savedFocus = document.activeElement;
      quick.hidden = false;
      body.classList.add('hx-search-open');
      if (searchBox) { searchBox.value = ''; filter(); searchBox.focus(); }
    }
    function closeSearch() {
      if (!quick || quick.hidden) return;
      quick.hidden = true;
      body.classList.remove('hx-search-open');
      if (savedFocus && typeof savedFocus.focus === 'function') savedFocus.focus();
    }
    searchButtons.forEach(function (button) { button.addEventListener('click', openSearch); });
    searchClose.forEach(function (button) { button.addEventListener('click', closeSearch); });
    if (searchBox) {
      searchBox.addEventListener('input', filter);
      searchBox.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') { var first = items.find(function (item) { return !item.hidden; }); if (first) { event.preventDefault(); window.location.assign(first.href); } }
        if (event.key === 'ArrowDown') { var result = items.find(function (item) { return !item.hidden; }); if (result) { event.preventDefault(); result.focus(); } }
      });
    }
    if (quick) quick.addEventListener('keydown', function (event) {
      if (event.key !== 'Tab') return;
      var focusable = Array.prototype.slice.call(quick.querySelectorAll('input,button,a')).filter(function (el) { return !el.hidden && el.offsetParent !== null; });
      if (!focusable.length) return;
      var first = focusable[0], last = focusable[focusable.length - 1];
      if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
      else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
    });
    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') { closeSearch(); closeMenu(); return; }
      var editable = event.target && (event.target.isContentEditable || /^(INPUT|TEXTAREA|SELECT)$/.test(event.target.tagName));
      if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') { event.preventDefault(); quick && !quick.hidden ? closeSearch() : openSearch(); }
      else if (!editable && event.key === '/' && quick && quick.hidden) { event.preventDefault(); openSearch(); }
    });
    // Close account dropdown when clicking outside.
    var account = document.querySelector('.hx-admin-account');
    if (account) document.addEventListener('click', function (event) { if (!account.contains(event.target)) account.open = false; });
    // Optional admin login password visibility control.
    document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
      button.addEventListener('click', function () {
        var input = document.getElementById(button.getAttribute('aria-controls'));
        if (!input) return;
        var isVisible = input.type === 'password'; input.type = isVisible ? 'text' : 'password';
        button.textContent = isVisible ? 'Ẩn' : 'Hiện'; button.setAttribute('aria-pressed', isVisible ? 'true' : 'false');
      });
    });
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', ready);
  else ready();
}());
