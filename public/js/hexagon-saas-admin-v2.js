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

  // Giá VND: KHÔNG định dạng trong sự kiện input. Tránh lệch con trỏ / chèn sai số.
  // Chấp nhận 199000 hoặc 199.000; chỉ format khi rời trường hoặc khi gửi form.
  const validPrice = (raw) => {
    if (raw === '') return true; // Để trống = liên hệ báo giá.
    if (!/^(?:[0-9]+|[0-9]{1,3}(?:\.[0-9]{3})+)$/.test(raw)) return false;
    const digits = raw.replace(/\./g, '').replace(/^0+(?=\d)/, '');
    // Trùng giới hạn Laravel: max:999999999999.
    return digits.length <= 12;
  };
  const formatVnd = (raw) => {
    const digits = raw.replace(/\./g, '').replace(/^0+(?=\d)/, '');
    return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  };
  const showPriceError = (message) => {
    if (priceError) {
      priceError.textContent = message;
      priceError.hidden = !message;
    }
    price.setCustomValidity(message || '');
    if (message) price.setAttribute('aria-invalid', 'true');
    else price.removeAttribute('aria-invalid');
  };
  const validateAndFormat = () => {
    const raw = price.value.trim();
    if (!validPrice(raw)) {
      showPriceError('Giá không hợp lệ. Nhập 199000 hoặc 199.000; không dùng số thập phân, dấu phẩy hay số âm.');
      return false;
    }
    showPriceError('');
    // Chỉ sửa phần hiển thị sau khi nhập xong; không tạo thêm chữ số nào.
    price.value = formatVnd(raw);
    return true;
  };

  // Giữ nguyên giá trị khi gõ, kể cả các bước đang nhập '199.' -> '199.000'.
  // Không dùng keydown/paste để ngăn người dùng gõ từng bước hợp lệ.
  price.addEventListener('input', () => showPriceError(''));
  price.addEventListener('blur', validateAndFormat);
  if (validPrice(price.value.trim())) price.value = formatVnd(price.value.trim());
  form.addEventListener('submit', (event) => {
    if (validateAndFormat()) return;
    event.preventDefault();
    price.focus();
    price.reportValidity();
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
