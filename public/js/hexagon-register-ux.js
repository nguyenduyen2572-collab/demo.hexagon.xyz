/* HEXAGON registration V1: inline client-side validation. Server validation is authoritative. */
(() => {
  'use strict';
  const root = document.querySelector('[data-hexagon-registration]');
  if (!root) return;
  const form = document.getElementById('hxRegistrationForm');
  const langField = document.getElementById('hxRegistrationLanguage');
  const dictionary = {
    vi: {
      requiredName: 'Vui lòng nhập họ và tên.',
      invalidName: 'Họ và tên chỉ được dùng chữ cái và dấu cách, không được nhập số hoặc ký tự đặc biệt.',
      nameLength: 'Họ và tên phải có ít nhất 2 ký tự.',
      requiredEmail: 'Vui lòng nhập email.',
      emailFormat: 'Email không đúng định dạng.',
      requiredPassword: 'Vui lòng nhập mật khẩu.',
      passwordLength: 'Mật khẩu phải có ít nhất 8 ký tự.',
      passwordComplex: 'Mật khẩu phải có chữ hoa, chữ thường, số và ký tự đặc biệt.',
      requiredConfirmation: 'Vui lòng nhập lại mật khẩu.',
      mismatch: 'Không trùng với mật khẩu.'
    },
    en: {
      requiredName: 'Please enter your full name.',
      invalidName: 'Use letters and spaces only; numbers and special characters are not allowed.',
      nameLength: 'The name must contain at least 2 characters.',
      requiredEmail: 'Please enter your email.',
      emailFormat: 'Please enter a valid email address.',
      requiredPassword: 'Please enter a password.',
      passwordLength: 'The password must contain at least 8 characters.',
      passwordComplex: 'Use uppercase, lowercase, a number and a special character.',
      requiredConfirmation: 'Please confirm your password.',
      mismatch: 'Passwords do not match.'
    }
  };
  function language() {
    return document.documentElement.lang.toLowerCase().startsWith('en') ? 'en' : 'vi';
  }
  function text(key) { return dictionary[language()][key]; }
  const fields = form ? {
    name: [form.elements.namedItem('name'), document.getElementById('hxRegNameError')],
    email: [form.elements.namedItem('email'), document.getElementById('hxRegEmailError')],
    password: [form.elements.namedItem('password'), document.getElementById('hxRegPasswordError')],
    password_confirmation: [form.elements.namedItem('password_confirmation'), document.getElementById('hxRegConfirmError')]
  } : {};
  const touched = new Set();
  const letters = /^\p{L}[\p{L}\p{M}]*(?: +\p{L}[\p{L}\p{M}]*)*$/u;
  // Browser-side friendly check for common email addresses; the Laravel RFC
  // validator remains authoritative when the form is submitted.
  function hexagonEmailValid(raw) {
    const email = raw.trim();
    if (email.length > 190 || /\s|\.\./.test(email)) return false;
    const at = email.indexOf('@');
    if (at < 1 || at !== email.lastIndexOf('@')) return false;
    const local = email.slice(0, at);
    const domain = email.slice(at + 1);
    if (local.length > 64 || local.startsWith('.') || local.endsWith('.')) return false;
    if (!/^[A-Za-z0-9!#$%&'*+/=?^_`{|}~.-]+$/.test(local)) return false;
    const labels = domain.split('.');
    if (labels.length < 2) return false;
    if (labels.some(label => !/^[A-Za-z0-9](?:[A-Za-z0-9-]{0,61}[A-Za-z0-9])?$/.test(label))) return false;
    const tld = labels[labels.length - 1];
    return /^(?:[A-Za-z]{2,63}|xn--[A-Za-z0-9-]{2,59})$/.test(tld);
  }
  function check(key) {
    if (!form) return null;
    const value = fields[key][0].value;
    if (key === 'name') {
      if (!value.trim()) return text('requiredName');
      if (value.trim().length < 2) return text('nameLength');
      if (!letters.test(value.trim().replace(/\s+/gu,' '))) return text('invalidName');
    }
    if (key === 'email') {
      if (!value.trim()) return text('requiredEmail');
      if (!hexagonEmailValid(value)) return text('emailFormat');
    }
    if (key === 'password') {
      if (!value) return text('requiredPassword');
      if (Array.from(value).length < 8) return text('passwordLength');
      if (!/[A-Z]/.test(value) || !/[a-z]/.test(value) || !/[0-9]/.test(value) || !/[^a-zA-Z0-9]/.test(value)) return text('passwordComplex');
    }
    if (key === 'password_confirmation') {
      if (!value) return text('requiredConfirmation');
      if (value !== fields.password[0].value) return text('mismatch');
    }
    return null;
  }
  function render(key, error) {
    const [input, output] = fields[key];
    output.textContent = error || '';
    input.setAttribute('aria-invalid', error ? 'true' : 'false');
    output.dataset.serverError = '0';
  }
  function refreshTranslations() {
    if (langField) langField.value = language();
    root.querySelectorAll('[data-hx-reg-vi]').forEach(el => {
      el.textContent = el.getAttribute('data-hx-reg-' + language());
    });
    if (!form) return;
    Object.keys(fields).forEach(key => {
      if (touched.has(key)) render(key, check(key));
    });
  }
  if (form) {
    Object.entries(fields).forEach(([key, field]) => {
      const [input, output] = field;
      if (!input || !output) return;
      input.addEventListener('blur', () => { touched.add(key); render(key, check(key)); });
      input.addEventListener('input', () => {
        if (key === 'email') touched.add(key);
        if (output.dataset.serverError === '1') { render(key, null); }
        if (touched.has(key)) render(key, check(key));
        if (key === 'password' && touched.has('password_confirmation')) {
          render('password_confirmation', check('password_confirmation'));
        }
      });
    });
    form.addEventListener('submit', (event) => {
      if (langField) langField.value = language();
      const keys = Object.keys(fields);
      let firstInvalid = null;
      keys.forEach(key => {
        touched.add(key);
        const err = check(key);
        render(key, err);
        if (err && !firstInvalid) firstInvalid = fields[key][0];
      });
      if (firstInvalid) { event.preventDefault(); firstInvalid.focus(); }
    });
  }
  refreshTranslations();
  // Existing HEXAGON language toggle updates <html lang="...">.
  const languageObserver = new MutationObserver(refreshTranslations);
  languageObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['lang'] });
})();
