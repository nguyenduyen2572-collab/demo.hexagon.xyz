// Chỉ hỗ trợ hiển thị; việc bắt buộc nhập lý do hủy được kiểm tra ở server.
(() => {
  'use strict';
  const init = () => {
    const form = document.querySelector('[data-hx-order-status-form]');
    if (!form) return;
    const status = form.querySelector('[name="status"]');
    const note = form.querySelector('[name="status_note"]');
    const label = form.querySelector('[data-hx-order-note-label]');
    const warning = form.querySelector('[data-hx-order-cancel-warning]');
    if (!status || !note || !label || !warning) return;
    const sync = () => {
      const cancelling = status.value === 'cancelled_demo';
      note.required = cancelling;
      label.textContent = cancelling ? 'Lý do hủy đơn hàng (bắt buộc)' : 'Ghi chú xử lý (không bắt buộc)';
      warning.hidden = !cancelling;
    };
    status.addEventListener('change', sync);
    sync();
    form.addEventListener('submit', (event) => {
      if (status.value === 'cancelled_demo' && !window.confirm('Bạn chắc chắn muốn hủy đơn hàng này? Thao tác này không thể hoàn tác.')) {
        event.preventDefault();
      }
    });
  };
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})();
