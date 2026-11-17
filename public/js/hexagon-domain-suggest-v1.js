/* HEXAGON domain ideas – optimized typing debounce and independent rate limit. HXG_SUGGEST_FAST_V1 */
(() => {
  'use strict';
  function init() {
    const form = document.getElementById('domainForm');
    const field = document.getElementById('domainInput');
    const searchField = form?.querySelector('.hx-search-field');
    const submitButton = document.getElementById('searchButton');
    if (!form || !field || !searchField || !submitButton) return;
    if (document.getElementById('hxDomainSuggestions')) return;

    const panel = document.createElement('div');
    panel.id = 'hxDomainSuggestions';
    panel.className = 'hx-domain-suggestions';
    panel.setAttribute('role', 'listbox');
    panel.setAttribute('aria-label', 'Gợi ý tên miền');
    panel.hidden = true;
    searchField.insertAdjacentElement('afterend', panel);
    form.classList.add('hx-domain-form-enhanced');
    field.setAttribute('aria-controls', panel.id);
    field.setAttribute('aria-autocomplete', 'list');
    field.setAttribute('aria-expanded', 'false');

    let suggestions = [];
    let highlighted = -1;
    let nextAllowedAt = 0;
    let debounceTimer = null;
    let activeRequest = null;
    let currentSearch = '';
    let generation = 0;
    let suppressOnce = false;
    // Never persist suggestions across browser sessions; availability is not a promise.
    const ideaCache = new Map();
    const CACHE_MS = 5 * 60 * 1000;
    const DEBOUNCE_MS = 320;
    const MIN_INTERVAL_MS = 1150;
    function keyOf(query) { return query.toLocaleLowerCase(); }
    function getCached(query) {
      const key = keyOf(query);
      const entry = ideaCache.get(key);
      if (!entry) return null;
      if (entry.expires <= Date.now()) { ideaCache.delete(key); return null; }
      return entry.items;
    }
    function cacheIdeas(query, items) {
      const key = keyOf(query);
      if (ideaCache.has(key)) ideaCache.delete(key);
      ideaCache.set(key, { items, expires: Date.now() + CACHE_MS });
      if (ideaCache.size > 30) ideaCache.delete(ideaCache.keys().next().value);
    }

    const translations = {
      vi: {
        title: 'Ý tưởng tên miền cho bạn', hint: 'Kết quả gợi ý từ GoDaddy',
        candidate: 'GoDaddy đánh giá có thể đăng ký · Cần xác nhận khi mua',
        loading: 'Đang tìm tên miền phù hợp...', empty: 'Chưa tìm thấy gợi ý phù hợp.',
        error: 'Không thể tải gợi ý lúc này. Hãy thử lại.',
        cooldown: seconds => `Vui lòng thử tìm kiếm tiếp sau ${seconds} giây.`,
        aria: 'Tên miền gợi ý'
      },
      en: {
        title: 'Domain name ideas', hint: 'Suggestions from GoDaddy',
        candidate: 'Suggested as available · Recheck before purchase',
        loading: 'Finding suitable domains...', empty: 'No matching suggestions found.',
        error: 'Suggestions are temporarily unavailable. Try again.',
        cooldown: seconds => `Try another search in ${seconds} seconds.`,
        aria: 'Suggested domains'
      }
    };
    function locale() {
      try { return localStorage.getItem('hexagon-language') === 'en' ? 'en' : 'vi'; }
      catch (_) { return document.documentElement.lang === 'en' ? 'en' : 'vi'; }
    }
    function words() { return translations[locale()]; }
    function cleanQuery(value) {
      return String(value || '').trim().replace(/\s+/g, ' ').slice(0, 50);
    }
    function eligible(q) {
      return q.length >= 2 && /^[\p{L}\p{N}\s-]+$/u.test(q);
    }
    function hide() {
      panel.hidden = true;
      highlighted = -1;
      field.setAttribute('aria-expanded', 'false');
      field.removeAttribute('aria-activedescendant');
    }
    function position() { panel.style.top = `${searchField.offsetTop + searchField.offsetHeight + 5}px`; }
    function show() {
      position();
      panel.hidden = false;
      field.setAttribute('aria-expanded', 'true');
    }
    function header() {
      panel.replaceChildren();
      panel.setAttribute('aria-label', words().aria);
      const head = document.createElement('div');
      head.className = 'hx-domain-suggest-head';
      const title = document.createElement('strong');
      title.textContent = words().title;
      const hint = document.createElement('span');
      hint.textContent = words().hint;
      head.append(title, hint);
      panel.append(head);
    }
    function showMessage(message) {
      suggestions = [];
      highlighted = -1;
      header();
      const line = document.createElement('p');
      line.className = 'hx-domain-suggest-message';
      line.style.cssText = 'padding:10px 12px;margin:0;font-size:13px;color:#65758f';
      line.textContent = message;
      panel.append(line);
      show();
    }
    function render(items) {
      suggestions = items.filter(item =>
        item && typeof item.domain === 'string' &&
        /^[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.(?:com|net)$/.test(item.domain)
      ).slice(0, 8);
      if (!suggestions.length) { showMessage(words().empty); return; }
      header();
      suggestions.forEach((item, index) => {
        const option = document.createElement('button');
        option.type = 'button';
        option.id = `hx-domain-option-${index}`;
        option.className = 'hx-domain-suggest-option';
        option.setAttribute('role', 'option');
        option.setAttribute('aria-selected', 'false');
        const body = document.createElement('span');
        body.className = 'hx-domain-suggest-main';
        const domain = document.createElement('strong');
        domain.textContent = item.domain;
        const detail = document.createElement('small');
        detail.textContent = words().candidate;
        body.append(domain, detail);
        const arrow = document.createElement('span');
        arrow.className = 'hx-domain-suggest-arrow';
        arrow.textContent = '↗';
        arrow.setAttribute('aria-hidden', 'true');
        option.append(body, arrow);
        option.addEventListener('click', () => select(item.domain));
        panel.append(option);
      });
      show();
    }
    function select(domain) {
      suppressOnce = true;
      field.value = domain;
      field.focus();
      hide();
      // Do not submit automatically. Existing checkout and pricing remain unchanged.
    }
    function setHighlighted(index) {
      const options = Array.from(panel.querySelectorAll('[role="option"]'));
      if (!options.length) return;
      highlighted = (index + options.length) % options.length;
      options.forEach((option, i) => {
        option.classList.toggle('is-highlighted', i === highlighted);
        option.setAttribute('aria-selected', String(i === highlighted));
      });
      field.setAttribute('aria-activedescendant', options[highlighted].id);
    }
    function schedule() {
      if (debounceTimer !== null) clearTimeout(debounceTimer);
      if (activeRequest !== null) { activeRequest.abort(); activeRequest = null; }
      generation += 1;
      const version = generation;
      const q = cleanQuery(field.value);
      currentSearch = q;
      suggestions = [];
      if (!eligible(q)) { hide(); return; }
      const cached = getCached(q);
      if (cached !== null) { render(cached); return; }
      showMessage(words().loading);
      // Wait for typing to stop; the provider rate limit is enforced separately.
      debounceTimer = setTimeout(() => runSuggestions(q, version), DEBOUNCE_MS);
    }
    async function runSuggestions(q, version) {
      if (version !== generation || cleanQuery(field.value) !== q) return;
      const remaining = nextAllowedAt - Date.now();
      if (remaining > 0) {
        debounceTimer = setTimeout(() => runSuggestions(q, version), remaining + 50);
        return;
      }
      const controller = new AbortController();
      activeRequest = controller;
      nextAllowedAt = Date.now() + MIN_INTERVAL_MS;
      try {
        const response = await fetch(`/api/v1/domains/ideas?q=${encodeURIComponent(q)}`, {
          headers: { Accept: 'application/json' },
          credentials: 'same-origin', signal: controller.signal
        });
        const data = await response.json();
        if (version !== generation || cleanQuery(field.value) !== q) return;
        if (response.status === 429) {
          const retry = Math.min(30, Math.max(1, Number(data.retry_after) || 1));
          nextAllowedAt = Math.max(nextAllowedAt, Date.now() + retry * 1000);
          showMessage(words().loading);
          debounceTimer = setTimeout(() => runSuggestions(q, version), nextAllowedAt - Date.now() + 75);
          return;
        }
        if (!response.ok || !data.success) {
          showMessage(data.message || words().error);
          return;
        }
        const items = Array.isArray(data.items) ? data.items : [];
        cacheIdeas(q, items);
        render(items);
      } catch (error) {
        if (error.name !== 'AbortError' && version === generation) showMessage(words().error);
      } finally {
        if (activeRequest === controller) activeRequest = null;
      }
    }

    field.addEventListener('input', () => { suppressOnce = false; schedule(); });
    field.addEventListener('focus', () => {
      if (suppressOnce) { suppressOnce = false; return; }
      if (eligible(cleanQuery(field.value)) && panel.hidden) schedule();
    });
    field.addEventListener('keydown', event => {
      if (panel.hidden) return;
      if (event.key === 'ArrowDown' && suggestions.length) { event.preventDefault(); setHighlighted(highlighted + 1); }
      if (event.key === 'ArrowUp' && suggestions.length) { event.preventDefault(); setHighlighted(highlighted - 1); }
      if (event.key === 'Escape') { event.preventDefault(); hide(); }
      if (event.key === 'Enter' && highlighted >= 0 && suggestions[highlighted]) {
        event.preventDefault(); select(suggestions[highlighted].domain);
      }
    });
    document.addEventListener('pointerdown', event => { if (!form.contains(event.target)) hide(); });
    window.addEventListener('resize', () => { if (!panel.hidden) position(); });
    document.getElementById('langSwitch')?.addEventListener('click', () => {
      if (!panel.hidden) render(suggestions);
    });

    // Suggestions never delay the explicit domain check action.
    // The existing 3-second check limiter remains enforced on its own API route.
    form.addEventListener('submit', () => {
      hide();
      generation += 1;
      if (debounceTimer !== null) clearTimeout(debounceTimer);
      if (activeRequest !== null) { activeRequest.abort(); activeRequest = null; }
    }, true);
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})();
