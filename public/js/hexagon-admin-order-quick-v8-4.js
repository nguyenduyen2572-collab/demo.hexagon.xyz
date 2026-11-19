/* HEXAGON V8.4 - Sao chép mã đơn trên trang chi tiết; không phụ thuộc thư viện. */
(() => {
  'use strict';
  const buttons = document.querySelectorAll('[data-hx-order-copy]');
  if (!buttons.length) return;

  async function copyText(value) {
    if (navigator.clipboard && window.isSecureContext) {
      await navigator.clipboard.writeText(value);
      return true;
    }
    // Hỗ trợ HTTP nội bộ/các trình duyệt chưa có Clipboard API.
    const input = document.createElement('textarea');
    input.value = value;
    input.setAttribute('readonly', '');
    input.style.position = 'fixed';
    input.style.opacity = '0';
    document.body.appendChild(input);
    input.focus();
    input.select();
    let copied = false;
    try { copied = document.execCommand('copy'); }
    finally { input.remove(); }
    return copied;
  }

  buttons.forEach((button) => {
    button.addEventListener('click', async () => {
      const value = button.dataset.hxOrderCopy;
      const feedback = button.parentElement?.querySelector('[data-hx-order-copy-status]');
      if (!value) return;
      try {
        const ok = await copyText(value);
        if (feedback) feedback.textContent = ok ? 'Đã sao chép mã đơn' : 'Không sao chép được; hãy chọn mã đơn để sao chép.';
      } catch (_) {
        if (feedback) feedback.textContent = 'Không sao chép được; hãy chọn mã đơn để sao chép.';
      }
    });
  });
})();
