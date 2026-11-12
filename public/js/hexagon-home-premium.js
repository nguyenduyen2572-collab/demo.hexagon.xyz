/* HEXAGON homepage V2 — progressive enhancement of existing Laravel domain/cart APIs. */
(() => {
  'use strict';
  const messages = {
    vi: {
      title: 'HEXAGON — Tên miền & giải pháp số cho tương lai',
      description: 'HEXAGON — Tìm kiếm tên miền và khám phá giải pháp dịch vụ số cho doanh nghiệp.',
      skip: 'Bỏ qua điều hướng', navigation: 'Điều hướng chính',
      announcement: 'HEXAGON đang trong giai đoạn trải nghiệm và hoàn thiện hệ thống', announcementCta: 'Khám phá nền tảng →',
      navDomain: 'Tên miền', navSaas: 'Giải pháp SaaS', navPricing: 'Bảng giá', navGuide: 'Cách hoạt động', navSupport: 'Hỗ trợ',
      cart: 'Giỏ hàng', cartLabel: 'Giỏ hàng', login: 'Đăng nhập', register: 'Đăng ký', admin: 'Trang quản trị',
      myAccount: 'Tài khoản của tôi', myOrders: 'Đơn hàng của tôi', logout: 'Đăng xuất', accountMenu: 'Mở menu tài khoản', openMenu: 'Mở menu', closeMenu: 'Đóng menu',
      eyebrow: 'NỀN TẢNG DỊCH VỤ SỐ HEXAGON', heroLine1: 'Ý tưởng lớn bắt đầu', heroLine2: 'từ một tên miền.',
      heroDescription: 'Tìm tên miền cho thương hiệu của bạn, quản lý đơn hàng dễ dàng và sẵn sàng khám phá hệ sinh thái SaaS đang được phát triển tại HEXAGON.',
      domainSearchLabel: 'Tìm tên miền của bạn', domainPlaceholder: 'vd: thuonghieu.com', domainAria: 'Nhập tên miền cần kiểm tra',
      search: 'Kiểm tra', searching: 'Đang kiểm tra...', demoDisclaimer: 'Kết quả tra cứu mang tính tham khảo; chưa phát sinh đăng ký tên miền.',
      resultFor: 'Kết quả tra cứu', registrationPrice: 'Giá đăng ký', renewalPrice: 'Giá gia hạn',
      addToCart: 'Thêm vào giỏ', adding: 'Đang thêm...', added: 'Đã thêm vào giỏ', viewCart: 'Xem giỏ hàng',
      checking: 'Đang kiểm tra dữ liệu tên miền...', available: 'Còn trống', unavailable: 'Đã đăng ký',
      point1: 'Tra cứu nhanh', point2: 'Bảng giá rõ ràng', point3: 'Đặt hàng thuận tiện',
      pricingEyebrow: 'BẢNG GIÁ TÊN MIỀN', pricingTitle: 'Chọn đuôi tên miền.', pricingTitle2: 'Tỏa sáng theo cách của bạn.',
      pricingDescription: 'Mức giá được cập nhật từ hệ thống quản trị HEXAGON. Chọn một đuôi tên miền để bắt đầu tìm kiếm.',
      pricingCta: 'Tìm tên miền ngay', priceLabel: 'Tên miền', firstYear: 'Đăng ký năm đầu', renewal: 'Gia hạn', perYear: '/năm',
      emptyPrices: 'Hiện chưa có bảng giá tên miền được công bố.',
      solutionsEyebrow: 'HỆ SINH THÁI HEXAGON', solutionsTitle: 'Một điểm khởi đầu.', solutionsTitle2: 'Nhiều cơ hội phát triển.',
      solutionsDescription: 'Bắt đầu với tên miền và khám phá danh mục giải pháp SaaS đang được mở rộng.',
      solutionDomainTitle: 'Tên miền là bước đầu tiên của thương hiệu.', solutionDomainDesc: 'Tra cứu, so sánh giá và thêm tên miền vào giỏ hàng chỉ trong vài thao tác.',
      solutionDomainCta: 'Khám phá tên miền', solutionSaasTitle: 'Công cụ số cho hành trình tiếp theo.',
      solutionSaasDesc: 'Tìm hiểu các dịch vụ SaaS trong danh mục đang được HEXAGON phát triển.',
      solutionSaasCta: 'Xem danh mục SaaS', solutionSaasSoon: 'Danh mục đang phát triển',
      howEyebrow: 'TRẢI NGHIỆM ĐƠN GIẢN', howTitle: 'Từ ý tưởng đến giỏ hàng, chỉ 3 bước.',
      step1Title: 'Tìm kiếm tên miền', step1Desc: 'Nhập tên miền bạn quan tâm để kiểm tra trong hệ thống thử nghiệm.',
      step2Title: 'Xem giá và lựa chọn', step2Desc: 'Xem trạng thái, giá đăng ký và giá gia hạn trước khi quyết định.',
      step3Title: 'Thêm vào giỏ hàng', step3Desc: 'Tiếp tục quy trình đặt hàng demo và quản lý đơn trong tài khoản.',
      supportEyebrow: 'BẠN CẦN BIẾT', supportTitle: 'Mọi thứ rõ ràng ngay từ đầu.',
      supportDescription: 'Một vài thông tin quan trọng về phiên bản HEXAGON đang trong quá trình thử nghiệm.',
      faq1Title: 'Tên miền tra cứu có phải dữ liệu thật?', faq1Desc: 'Chưa. Kết quả hiện tại sử dụng dữ liệu mẫu. Khi tích hợp API nhà cung cấp, HEXAGON mới có thể cung cấp dữ liệu tra cứu thực tế.',
      faq2Title: 'Đặt hàng có phát sinh thanh toán không?', faq2Desc: 'Không. Quy trình đặt hàng hiện chỉ dùng để thử nghiệm, chưa thu tiền hoặc đăng ký tên miền thật.',
      faq3Title: 'Tôi có thể xem lại đơn hàng ở đâu?', faq3Desc: 'Sau khi đăng nhập và xác minh email, bạn có thể xem các đơn hàng gắn với tài khoản trong mục Đơn hàng của tôi.',
      faq4Title: 'Dịch vụ SaaS đã sẵn sàng sử dụng chưa?', faq4Desc: 'Danh mục SaaS đang được phát triển. Các dịch vụ và chức năng sẽ được cập nhật theo tiến độ của HEXAGON.',
      ctaTitle: 'Tên miền tốt, khởi đầu tốt.', ctaDesc: 'Bắt đầu tìm tên miền phù hợp với thương hiệu của bạn ngay hôm nay.', ctaButton: 'Bắt đầu tìm kiếm',
      footerAbout: 'Nền tảng tên miền và giải pháp dịch vụ số đang phát triển bởi Công ty Cổ phần Lục Giác.',
      footerExplore: 'Khám phá', company: 'Công ty Cổ phần Lục Giác', footerDemo: 'Môi trường thử nghiệm · Không thanh toán hoặc đăng ký tên miền thật.',
      noJson: 'Máy chủ chưa trả về dữ liệu JSON. Vui lòng thử lại.', checkError: 'Không thể kiểm tra tên miền.',
      cartError: 'Không thể thêm vào giỏ hàng.', networkError: 'Không thể kết nối tới máy chủ.', invalidDomain: 'Hãy nhập tên miền hợp lệ, ví dụ thuonghieu.com.',
      rateLimit: 'Bạn tìm kiếm quá nhanh. Vui lòng thử lại sau ít phút.', addedMessage: 'Đã thêm thành công. Bạn có thể xem giỏ hàng.',
      noMockData: 'Chưa có dữ liệu thử nghiệm cho tên miền này.', unsupportedTld: 'Đuôi tên miền chưa được hỗ trợ.', pausedTld: 'Đuôi tên miền đang tạm ngừng kinh doanh.'
    },
    en: {
      title: 'HEXAGON — Domain names & digital solutions for tomorrow',
      description: 'HEXAGON — Find your domain name and discover digital business solutions.',
      skip: 'Skip navigation', navigation: 'Main navigation',
      announcement: 'HEXAGON is currently in demo and development mode', announcementCta: 'Explore the platform →',
      navDomain: 'Domains', navSaas: 'SaaS solutions', navPricing: 'Pricing', navGuide: 'How it works', navSupport: 'Support',
      cart: 'Cart', cartLabel: 'Cart', login: 'Log in', register: 'Sign up', admin: 'Admin dashboard',
      myAccount: 'My account', myOrders: 'My orders', logout: 'Log out', accountMenu: 'Open account menu', openMenu: 'Open menu', closeMenu: 'Close menu',
      eyebrow: 'HEXAGON DIGITAL SERVICES PLATFORM', heroLine1: 'Big ideas start', heroLine2: 'with one domain.',
      heroDescription: 'Find a domain for your brand, manage demo orders with ease, and discover the growing SaaS ecosystem at HEXAGON.',
      domainSearchLabel: 'Find your domain', domainPlaceholder: 'e.g. yourbrand.com', domainAria: 'Enter a domain name to check',
      search: 'Search', searching: 'Checking...', demoDisclaimer: 'Search results are indicative; no domain registration is completed.',
      resultFor: 'Search result', registrationPrice: 'Registration', renewalPrice: 'Renewal',
      addToCart: 'Add to cart', adding: 'Adding...', added: 'Added to cart', viewCart: 'View cart',
      checking: 'Checking domain availability...', available: 'Available', unavailable: 'Registered',
      point1: 'Fast lookup', point2: 'Clear pricing', point3: 'Easy checkout',
      pricingEyebrow: 'DOMAIN PRICING', pricingTitle: 'Choose your extension.', pricingTitle2: 'Make it yours.',
      pricingDescription: 'Prices are loaded directly from the HEXAGON admin system. Pick an extension to start a search.',
      pricingCta: 'Search a domain', priceLabel: 'Domain', firstYear: 'First year', renewal: 'Renewal', perYear: '/year',
      emptyPrices: 'No domain prices are currently published.',
      solutionsEyebrow: 'HEXAGON ECOSYSTEM', solutionsTitle: 'One starting point.', solutionsTitle2: 'More possibilities.',
      solutionsDescription: 'Start with your domain and explore our growing catalog of SaaS solutions.',
      solutionDomainTitle: 'Your domain is your brand’s first step.', solutionDomainDesc: 'Search, compare pricing and add your domain to the cart in just a few steps.',
      solutionDomainCta: 'Explore domains', solutionSaasTitle: 'Digital tools for what comes next.',
      solutionSaasDesc: 'Explore the SaaS catalog currently under development at HEXAGON.',
      solutionSaasCta: 'Browse SaaS catalog', solutionSaasSoon: 'Catalog in development',
      howEyebrow: 'SIMPLE EXPERIENCE', howTitle: 'From idea to cart in three steps.',
      step1Title: 'Search a domain', step1Desc: 'Enter the domain you want to check against our demo data.',
      step2Title: 'Review availability & pricing', step2Desc: 'Review the availability status and registration and renewal pricing.',
      step3Title: 'Add to cart', step3Desc: 'Continue through demo checkout and view orders in your account.',
      supportEyebrow: 'GOOD TO KNOW', supportTitle: 'Everything clear from day one.',
      supportDescription: 'A few important details about the HEXAGON experience while it is in development.',
      faq1Title: 'Is domain availability based on live data?', faq1Desc: 'Not yet. Availability is currently based on sample data. Live provider information will be available after API integration.',
      faq2Title: 'Will checkout charge me anything?', faq2Desc: 'No. Checkout is a demo flow only. No real payment or domain registration is processed.',
      faq3Title: 'Where can I view my orders?', faq3Desc: 'After you log in and verify your email, orders associated with your account appear under My orders.',
      faq4Title: 'Are SaaS services ready for use?', faq4Desc: 'The SaaS catalog is in development. Services and features will be added as HEXAGON evolves.',
      ctaTitle: 'Great domain. Great start.', ctaDesc: 'Start searching for the right domain name for your brand today.', ctaButton: 'Find a domain',
      footerAbout: 'An evolving domain and digital services platform by Luc Giac Joint Stock Company.',
      footerExplore: 'Explore', company: 'Luc Giac Joint Stock Company', footerDemo: 'Demo environment · No live payments or domain registrations.',
      noJson: 'The server did not return JSON. Please try again.', checkError: 'Could not check this domain.',
      cartError: 'Could not add this domain to the cart.', networkError: 'Could not connect to the server.', invalidDomain: 'Enter a valid domain name, such as yourbrand.com.',
      rateLimit: 'Too many searches. Please try again in a few minutes.', addedMessage: 'Added successfully. You can now view your cart.',
      noMockData: 'No sample availability data exists for this domain.', unsupportedTld: 'This domain extension is not supported.', pausedTld: 'This domain extension is temporarily unavailable.'
    }
  };

  const $ = (id) => document.getElementById(id);
  const form = $('domainForm');
  if (!form) return;
  const input = $('domainInput');
  const button = $('searchButton');
  const buttonText = button.querySelector('span');
  const result = $('result');
  const resultDomain = $('resultDomain');
  const resultStatus = $('resultStatus');
  const resultStatusText = $('resultStatusText');
  const resultMessage = $('resultMessage');
  const resultDetails = $('resultDetails');
  const resultActions = $('resultActions');
  const addButton = $('addToCartBtn');
  const addButtonText = addButton.querySelector('span');
  const viewCartLink = $('viewCartLink');
  const cartMessage = $('cartMessage');
  const cartCount = $('cartCount');
  const langSwitch = $('langSwitch');
  const langFlag = $('langFlag');
  const mobileMenu = $('mainNav');
  const menuButton = $('mobileMenuButton');
  const apiCheckUrl = document.body.dataset.checkUrl;
  const addCartUrl = document.body.dataset.addCartUrl;
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
  let language = 'vi';
  try { language = localStorage.getItem('hexagon-language') === 'en' ? 'en' : 'vi'; } catch (_) { /* Storage may be blocked. */ }
  let busy = false;
  let currentDomain = '';
  let currentData = null;
  let added = false;
  let lastErrorKey = '';
  let errorString = '';
  let cartFailure = '';

  const t = key => messages[language][key] || key;
  const money = value => new Intl.NumberFormat(language === 'en' ? 'en-US' : 'vi-VN').format(Number(value)) + (language === 'en' ? ' VND' : 'đ');
  const normalized = value => String(value ?? '').trim().toLowerCase();
  function applyLanguage() {
    document.documentElement.lang = language;
    document.title = t('title');
    const description = document.querySelector('meta[name="description"]');
    if (description) description.content = t('description');
    document.querySelectorAll('[data-i18n]').forEach(el => { el.textContent = t(el.dataset.i18n); });
    document.querySelectorAll('[data-i18n-aria]').forEach(el => el.setAttribute('aria-label', t(el.dataset.i18nAria)));
    document.querySelectorAll('[data-i18n-placeholder]').forEach(el => { el.placeholder = t(el.dataset.i18nPlaceholder); });
    document.querySelectorAll('[data-money]').forEach(el => { el.textContent = money(el.dataset.money); });
    buttonText.textContent = busy ? t('searching') : t('search');
    if (currentData) {
      resultStatusText.textContent = t(currentData.available ? 'available' : 'unavailable');
      $('resultRegisterPrice').textContent = money(currentData.registration_price);
      $('resultRenewPrice').textContent = money(currentData.renewal_price);
    }
    if (lastErrorKey || errorString) {
      resultMessage.textContent = lastErrorKey ? t(lastErrorKey) : errorString;
    } else if (busy) { resultMessage.textContent = t('checking'); }
    addButtonText.textContent = added ? t('added') : (addButton.disabled ? t('adding') : t('addToCart'));
    if (added) cartMessage.textContent = t('addedMessage');
    else if (cartFailure) cartMessage.textContent = cartFailure;
    langFlag.src = language === 'vi' ? langFlag.dataset.gb : langFlag.dataset.vn;
    const languageLabel = language === 'vi' ? 'Switch to English' : 'Chuyển sang tiếng Việt';
    langSwitch.title = languageLabel;
    langSwitch.setAttribute('aria-label', languageLabel);
  }
  langSwitch?.addEventListener('click', () => {
    language = language === 'vi' ? 'en' : 'vi';
    try { localStorage.setItem('hexagon-language', language); } catch (_) { /* Session only */ }
    applyLanguage();
  });
  menuButton?.addEventListener('click', () => {
    const isOpen = mobileMenu.classList.toggle('is-open');
    menuButton.setAttribute('aria-expanded', String(isOpen));
    menuButton.setAttribute('aria-label', t(isOpen ? 'closeMenu' : 'openMenu'));
  });
  mobileMenu?.querySelectorAll('a').forEach(a => a.addEventListener('click', () => {
    mobileMenu.classList.remove('is-open');
    menuButton?.setAttribute('aria-expanded', 'false');
    menuButton?.setAttribute('aria-label', t('openMenu'));
  }));
  const account = $('accountDropdown');
  if (account) {
    document.addEventListener('click', event => { if (!account.contains(event.target)) account.open = false; });
    document.addEventListener('keydown', event => { if (event.key === 'Escape' && account.open) { account.open = false; account.querySelector('summary')?.focus(); } });
  }
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && mobileMenu?.classList.contains('is-open')) {
      mobileMenu.classList.remove('is-open');
      menuButton?.setAttribute('aria-expanded', 'false');
      menuButton?.focus();
    }
  });

  document.querySelectorAll('[data-extension]').forEach(card => {
    card.addEventListener('click', () => {
      const extension = card.dataset.extension;
      const currentPrefix = input.value.trim().replace(/^https?:\/\//i, '').split('.')[0].replace(/[^a-z0-9-]/gi, '');
      input.value = (currentPrefix || 'thuonghieu') + extension;
      input.focus();
      document.getElementById('domain-search')?.scrollIntoView({ behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth', block: 'start' });
    });
  });

  function showMessage(key, raw = '') {
    result.hidden = false;
    resultDomain.textContent = currentDomain;
    resultStatus.hidden = true;
    resultStatus.className = 'hx-status-chip';
    resultDetails.hidden = true;
    resultActions.hidden = true;
    currentData = null;
    lastErrorKey = key;
    errorString = raw;
    resultMessage.hidden = false;
    resultMessage.classList.toggle('is-error', key !== 'checking');
    resultMessage.textContent = key ? t(key) : raw;
  }
  async function readJson(response) {
    const contentType = response.headers.get('content-type') || '';
    if (!contentType.includes('application/json')) throw new Error(t('noJson'));
    return response.json();
  }
  function apiError(json, fallback) {
    const raw = String(json?.message ?? '');
    if (/Chưa có dữ liệu/i.test(raw)) return t('noMockData');
    if (/chưa được hỗ trợ/i.test(raw)) return t('unsupportedTld');
    if (/tạm ngừng/i.test(raw)) return t('pausedTld');
    if (language === 'vi' && raw) return raw;
    return t(fallback);
  }
  function showResult(data) {
    if (!data || typeof data.domain !== 'string') throw new Error(t('checkError'));
    currentData = data;
    lastErrorKey = '';
    errorString = '';
    added = false;
    cartFailure = '';
    result.hidden = false;
    resultDomain.textContent = data.domain;
    resultStatus.hidden = false;
    resultStatus.className = 'hx-status-chip' + (data.available ? '' : ' unavailable');
    resultStatusText.textContent = t(data.available ? 'available' : 'unavailable');
    resultMessage.hidden = true;
    resultDetails.hidden = false;
    $('resultRegisterPrice').textContent = money(data.registration_price);
    $('resultRenewPrice').textContent = money(data.renewal_price);
    resultActions.hidden = !data.available;
    viewCartLink.hidden = true;
    cartMessage.textContent = '';
    cartMessage.classList.remove('is-error');
    addButton.disabled = false;
    addButtonText.textContent = t('addToCart');
  }
  addButton.addEventListener('click', async () => {
    if (!currentData?.available || added || addButton.disabled) return;
    addButton.disabled = true;
    addButtonText.textContent = t('adding');
    cartFailure = '';
    cartMessage.textContent = '';
    cartMessage.classList.remove('is-error');
    try {
      const response = await fetch(addCartUrl, {
        method: 'POST', credentials: 'same-origin',
        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ domain: currentData.domain })
      });
      const json = await readJson(response);
      if (!response.ok || !json.success) throw new Error(apiError(json, 'cartError'));
      added = true;
      addButtonText.textContent = t('added');
      cartMessage.textContent = t('addedMessage');
      viewCartLink.hidden = false;
      if (typeof json.cart_count === 'number' && cartCount) {
        cartCount.textContent = String(json.cart_count);
        cartCount.hidden = json.cart_count <= 0;
      }
    } catch (error) {
      cartFailure = error.message || t('cartError');
      cartMessage.classList.add('is-error');
      cartMessage.textContent = cartFailure;
      addButton.disabled = false;
      addButtonText.textContent = t('addToCart');
    }
  });
  form.addEventListener('submit', async event => {
    event.preventDefault();
    if (busy) return;
    currentDomain = normalized(input.value);
    if (!currentDomain || !currentDomain.includes('.') || currentDomain.includes(' ') || currentDomain.length > 190) {
      showMessage('invalidDomain');
      input.focus();
      return;
    }
    busy = true;
    button.disabled = true;
    buttonText.textContent = t('searching');
    showMessage('checking');
    try {
      const response = await fetch(apiCheckUrl + '?domain=' + encodeURIComponent(currentDomain), {
        credentials: 'same-origin', headers: { 'Accept': 'application/json' }
      });
      const json = await readJson(response);
      if (response.status === 429) throw new Error(t('rateLimit'));
      if (!response.ok || !json.success) throw new Error(apiError(json, 'checkError'));
      showResult(json.data);
    } catch (error) {
      showMessage('', error.message || t('networkError'));
    } finally {
      busy = false;
      button.disabled = false;
      buttonText.textContent = t('search');
    }
  });
  applyLanguage();
})();
