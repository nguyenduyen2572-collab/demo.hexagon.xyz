/* HEXAGON Domain Suggest V1: no API calls during typing; 3s cooldown only on actual checks. */
(() => {
  'use strict';
  function init() {
    const form = document.getElementById('domainForm');
    const field = document.getElementById('domainInput');
    const searchField = form?.querySelector('.hx-search-field');
    const submitButton = document.getElementById('searchButton');
    if (!form || !field || !searchField || !submitButton) return;
    if (document.getElementById('hxDomainSuggestions')) return;

    const extensions = Array.from(document.querySelectorAll('.hx-price-card[data-extension]'))
      .map(card => {
        const extension = String(card.dataset.extension || '').trim().toLowerCase();
        const amount = Number(card.querySelector('[data-money]')?.dataset.money);
        return { extension, price: Number.isFinite(amount) && amount >= 0 ? amount : null };
      })
      .filter(entry => /^\.[a-z]{2,10}(?:\.[a-z]{2,10})?$/.test(entry.extension));
    const unique = Array.from(new Map(extensions.map(entry => [entry.extension, entry])).values());
    // The price cards are rendered from the active extensions of the existing site.
    if (!unique.length) return;

    const panel = document.createElement('div');
    panel.id = 'hxDomainSuggestions';
    panel.className = 'hx-domain-suggestions';
    panel.setAttribute('role', 'listbox');
    panel.setAttribute('aria-label', 'Tên miền gợi ý');
    panel.hidden = true;
    searchField.insertAdjacentElement('afterend', panel);
    form.classList.add('hx-domain-form-enhanced');
    field.setAttribute('aria-controls', panel.id);
    field.setAttribute('aria-autocomplete', 'list');
    field.setAttribute('aria-expanded', 'false');

    const feedback = document.createElement('p');
    feedback.className = 'hx-domain-cooldown';
    feedback.setAttribute('role', 'status');
    feedback.setAttribute('aria-live', 'polite');
    feedback.hidden = true;
    form.appendChild(feedback);

    let suggestions = [];
    let highlighted = -1;
    let nextAllowedAt = 0;
    let countdownTimer = null;

    function locale() {
      try { return localStorage.getItem('hexagon-language') === 'en' ? 'en' : 'vi'; }
      catch (_) { return document.documentElement.lang === 'en' ? 'en' : 'vi'; }
    }
    const translations = {
      vi: { title: 'Gợi ý tên miền', hint: 'Chọn một tên miền để điền vào ô tìm kiếm',
        check: 'Chọn để kiểm tra', price: 'Đăng ký', year: '/năm',
        cooldown: seconds => `Bạn có thể kiểm tra tiếp sau ${seconds} giây.`,
        aria: 'Tên miền gợi ý' },
      en: { title: 'Suggested domains', hint: 'Choose a domain to fill the search box',
        check: 'Select to check', price: 'Registration', year: '/year',
        cooldown: seconds => `You can search again in ${seconds} seconds.`,
        aria: 'Suggested domains' }
    };
    function dictionary() { return translations[locale()]; }
    function formatPrice(value) {
      return new Intl.NumberFormat(locale() === 'en' ? 'en-US' : 'vi-VN').format(value)
        + (locale() === 'en' ? ' VND' : 'đ');
    }
    function hide() {
      panel.hidden = true;
      highlighted = -1;
      field.setAttribute('aria-expanded', 'false');
      field.removeAttribute('aria-activedescendant');
    }
    function updatePlacement() {
      panel.style.top = `${searchField.offsetTop + searchField.offsetHeight + 5}px`;
    }
    function prefixFromInput(raw) {
      const value = String(raw || '').trim().toLowerCase()
        .replace(/^https?:\/\//, '').replace(/^www\./, '').split('/')[0];
      const ordered = unique.map(row => row.extension).sort((a, b) => b.length - a.length);
      const existing = ordered.find(ext => value.endsWith(ext));
      let prefix = existing ? value.slice(0, -existing.length) : value.split('.')[0];
      prefix = prefix.trim();
      return /^[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?$/.test(prefix) ? prefix : '';
    }
    function setHighlighted(index) {
      const options = Array.from(panel.querySelectorAll('[role="option"]'));
      if (!options.length) return;
      highlighted = (index + options.length) % options.length;
      options.forEach((option, idx) => {
        option.classList.toggle('is-highlighted', idx === highlighted);
        option.setAttribute('aria-selected', String(idx === highlighted));
      });
      field.setAttribute('aria-activedescendant', options[highlighted].id);
    }
    function select(value) {
      field.value = value;
      field.focus();
      hide();
      // Deliberately do not trigger the API: user clicks Search once ready.
    }
    function render() {
      const prefix = prefixFromInput(field.value);
      panel.replaceChildren();
      if (!prefix) { hide(); return; }

      const currentValue = field.value.trim().toLowerCase();
      suggestions = unique
        .map(row => ({ ...row, domain: prefix + row.extension }))
        .sort((a, b) => {
          const aExact = a.domain === currentValue ? 0 : 1;
          const bExact = b.domain === currentValue ? 0 : 1;
          return aExact - bExact;
        }).slice(0, 6);
      if (!suggestions.length) { hide(); return; }

      panel.setAttribute('aria-label', dictionary().aria);
      const header = document.createElement('div');
      header.className = 'hx-domain-suggest-head';
      const heading = document.createElement('strong');
      heading.textContent = dictionary().title;
      const hint = document.createElement('span');
      hint.textContent = dictionary().hint;
      header.append(heading, hint);
      panel.append(header);

      suggestions.forEach((entry, index) => {
        const option = document.createElement('button');
        option.type = 'button';
        option.id = `hx-domain-option-${index}`;
        option.className = 'hx-domain-suggest-option';
        option.setAttribute('role', 'option');
        option.setAttribute('aria-selected', 'false');
        const main = document.createElement('span');
        main.className = 'hx-domain-suggest-main';
        const domain = document.createElement('strong');
        domain.textContent = entry.domain;
        const subtitle = document.createElement('small');
        subtitle.textContent = entry.price === null
          ? dictionary().check
          : `${dictionary().price}: ${formatPrice(entry.price)}${dictionary().year}`;
        main.append(domain, subtitle);
        const icon = document.createElement('span');
        icon.className = 'hx-domain-suggest-arrow';
        icon.setAttribute('aria-hidden', 'true');
        icon.textContent = '↗';
        option.append(main, icon);
        option.addEventListener('click', () => select(entry.domain));
        panel.append(option);
      });
      updatePlacement();
      highlighted = -1;
      panel.hidden = false;
      field.setAttribute('aria-expanded', 'true');
      field.removeAttribute('aria-activedescendant');
    }

    function updateCooldown() {
      const seconds = Math.ceil((nextAllowedAt - Date.now()) / 1000);
      if (seconds > 0) {
        feedback.textContent = dictionary().cooldown(seconds);
        feedback.hidden = false;
      } else {
        feedback.hidden = true;
        if (countdownTimer !== null) { clearInterval(countdownTimer); countdownTimer = null; }
      }
    }

    field.addEventListener('input', render);
    field.addEventListener('focus', render);
    field.addEventListener('keydown', event => {
      if (panel.hidden) return;
      if (event.key === 'ArrowDown') { event.preventDefault(); setHighlighted(highlighted + 1); }
      if (event.key === 'ArrowUp') { event.preventDefault(); setHighlighted(highlighted - 1); }
      if (event.key === 'Escape') { event.preventDefault(); hide(); }
      if (event.key === 'Enter' && highlighted >= 0) {
        event.preventDefault();
        select(suggestions[highlighted].domain);
      }
    });
    document.addEventListener('pointerdown', event => {
      if (!form.contains(event.target)) hide();
    });
    document.getElementById('langSwitch')?.addEventListener('click', () => {
      if (!panel.hidden) render();
      if (!feedback.hidden) updateCooldown();
    });
    window.addEventListener('resize', () => { if (!panel.hidden) updatePlacement(); });
    document.querySelectorAll('.hx-price-card[data-extension]').forEach(card => {
      card.addEventListener('click', () => setTimeout(render, 0));
    });

    // Capture BEFORE the existing homepage handler, without changing its API / cart logic.
    form.addEventListener('submit', event => {
      hide();
      // Let existing homepage validation show its normal error for malformed domain names.
      const domain = String(field.value || '').trim().toLowerCase();
      if (!domain || !domain.includes('.') || domain.includes(' ') || domain.length > 190) return;
      if (submitButton.disabled) return; // Existing request is still running.
      if (Date.now() < nextAllowedAt) {
        event.preventDefault();
        event.stopImmediatePropagation();
        updateCooldown();
        return;
      }
      nextAllowedAt = Date.now() + 3000;
      updateCooldown();
      if (countdownTimer === null) countdownTimer = setInterval(updateCooldown, 250);
    }, true);
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})();
