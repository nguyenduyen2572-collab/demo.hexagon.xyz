(() => {
  'use strict';
  const form = document.getElementById('saas-admin-editor');
  if (!form) return;
  const name = form.querySelector('[name="name_vi"]');
  const slug = form.querySelector('[name="slug"]');
  const price = form.querySelector('[name="price_monthly"]');
  const priceError = document.getElementById('saas-price-error');
  const status = document.getElementById('saas-translation-status');
  const csrf = form.querySelector('[name="_token"]')?.value || '';
  const url = form.dataset.translateUrl;
  const slugify = (s) => String(s || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/đ/gi, 'd')
    .toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '').slice(0, 180).replace(/-$/g, '');

  let slugManaged = !slug.value || slug.value === slugify(name.value);
  let generatedSlug = slug.value;
  name.addEventListener('input', () => {
    if (!slugManaged) return;
    generatedSlug = slugify(name.value);
    slug.value = generatedSlug;
  });
  slug.addEventListener('input', () => { slugManaged = slug.value === generatedSlug || slug.value === ''; });
  slug.addEventListener('blur', () => { slug.value = slug.value.trim().toLowerCase(); });

  // Giá VND: hiển thị theo nhóm nghìn, cấm nhập dấu chấm thập phân và dấu âm.
  const formatVnd = (raw) => {
    const digits = String(raw || '').replace(/\D/g, '');
    if (!digits) return '';
    return digits.replace(/^0+(?=\d)/, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  };
  const validPrice = (raw) => raw === '' || /^(?:\d+|\d{1,3}(?:\.\d{3})+)$/.test(raw);
  const showPriceError = (message) => {
    priceError.textContent = message;
    priceError.hidden = !message;
    price.setCustomValidity(message || '');
  };
  if (validPrice(price.value.trim())) price.value = formatVnd(price.value);
  price.addEventListener('keydown', (event) => {
    if (event.ctrlKey || event.metaKey || event.altKey || event.key.length !== 1) return;
    if (!/^[0-9]$/.test(event.key)) event.preventDefault();
  });
  price.addEventListener('paste', (event) => {
    const pasted = event.clipboardData?.getData('text')?.trim() || '';
    if (!validPrice(pasted)) {
      event.preventDefault();
      showPriceError('Chỉ nhập số nguyên VND, ví dụ 199000 hoặc 199.000.');
    }
  });
  price.addEventListener('input', () => {
    const raw = price.value;
    if (!validPrice(raw)) {
      showPriceError('Giá phải là số nguyên VND; không dùng dấu phẩy hoặc số thập phân.');
      return;
    }
    showPriceError('');
    const start = price.selectionStart ?? raw.length;
    const digitsBeforeCursor = (raw.slice(0, start).match(/\d/g) || []).length;
    price.value = formatVnd(raw);
    let cursor = 0, count = 0;
    while (cursor < price.value.length && count < digitsBeforeCursor) {
      if (/\d/.test(price.value[cursor])) count++;
      cursor++;
    }
    price.setSelectionRange(cursor, cursor);
  });
  price.addEventListener('blur', () => { if (validPrice(price.value)) price.value = formatVnd(price.value); });
  form.addEventListener('submit', (event) => {
    if (!validPrice(price.value.trim())) {
      event.preventDefault();
      showPriceError('Giá tháng không hợp lệ. Hãy nhập 199000 hoặc 199.000.');
      price.focus();
    }
  });

  const viFields = [
    { source: 'name_vi', id: 'saas-name-vi', delay: 1350 },
    { source: 'summary_vi', id: 'saas-summary-vi', delay: 2000 },
    { source: 'description_vi', id: 'saas-description-vi', delay: 2500 },
  ];
  viFields.forEach(({ source, id, delay }) => {
    const input = document.getElementById(id);
    const output = form.querySelector(`[data-translation-result="${source}"]`);
    if (!input || !output) return;
    let timeout = null, sequence = 0, controller = null, lastText = input.value.trim();
    const translate = async () => {
      const text = input.value.trim();
      if (text === lastText) return;
      lastText = text;
      const thisRequest = ++sequence;
      if (controller) controller.abort();
      if (!text) { output.textContent = 'Chưa có nội dung'; return; }
      controller = new AbortController();
      output.textContent = 'Đang tạo bản dịch...';
      if (status) status.textContent = 'Đang đồng bộ bản dịch tiếng Anh...';
      try {
        const response = await fetch(url, {
          method: 'POST', credentials: 'same-origin',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
          body: JSON.stringify({ field: source, text }), signal: controller.signal,
        });
        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'Không thể dịch lúc này.');
        if (thisRequest !== sequence) return;
        output.textContent = data.translation || 'Chưa có nội dung';
        if (status) status.textContent = 'Đã cập nhật bản xem trước. Bản dịch sẽ được đồng bộ khi lưu.';
      } catch (error) {
        if (error.name === 'AbortError' || thisRequest !== sequence) return;
        output.textContent = 'Chưa dịch được. Hệ thống sẽ thử lại khi lưu.';
        if (status) status.textContent = error.message || 'Dịch vụ dịch đang tạm thời không phản hồi.';
      }
    };
    input.addEventListener('input', () => {
      clearTimeout(timeout);
      if (input.value.trim() !== lastText) {
        output.textContent = 'Nội dung đã thay đổi, đang chờ dịch...';
        timeout = setTimeout(translate, delay);
      }
    });
    input.addEventListener('blur', () => { clearTimeout(timeout); void translate(); });
  });
})();
