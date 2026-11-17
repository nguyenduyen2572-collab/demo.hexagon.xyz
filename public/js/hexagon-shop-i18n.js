/* HEXAGON Stage 12: chia sẻ ngôn ngữ với home.blade.php qua hexagon-language. */
(() => {
    'use strict';

    const messages = {
        vi: {
            home: 'Trang chủ', cart: 'Giỏ hàng', login: 'Đăng nhập', register: 'Đăng ký', accountShort: 'Tài khoản', admin: 'Trang quản trị',
            myAccount: 'Tài khoản của tôi', myOrders: 'Đơn hàng của tôi', logout: 'Đăng xuất',
            accountMenu: 'Mở menu tài khoản', backHome: '← Về trang chủ', backCart: '← Quay lại giỏ hàng',
            cartTitle: 'Giỏ hàng của bạn', cartSubtitle: 'Xem lại tên miền và chi phí trước khi tiếp tục.',
            orderStepsAria: 'Các bước đặt hàng', stepFind: 'Tìm tên miền', stepCart: 'Giỏ hàng', stepCheckout: 'Đặt hàng',
            cartEyebrow: 'BƯỚC 2 / 3', checkoutEyebrow: 'BƯỚC 3 / 3', cartListTitle: 'Tên miền đã chọn', domainCount: 'tên miền',
            summaryDesc: 'Kiểm tra lại chi phí trước khi gửi yêu cầu.', loginNeeded: 'Đăng nhập để đặt hàng; giỏ hàng vẫn được giữ lại.',
            emptyTitle: 'Chưa có tên miền trong giỏ', emptyCartHelp: 'Hãy tìm kiếm tên miền bạn muốn và nhấn “Thêm vào giỏ”.',
            contactHint: 'Những trường có dấu * là bắt buộc.',
            cartDemo: 'Giỏ hàng demo. Không thanh toán hay đăng ký tên miền thật.',
            itemOneYear: 'Đăng ký 1 năm', remove: 'Xóa khỏi giỏ hàng', emptyCart: 'Giỏ hàng của bạn đang trống.',
            subtotal: 'Tạm tính', continueShopping: 'Tiếp tục tìm tên miền', proceedCheckout: 'Tiếp tục đặt hàng',
            checkoutTitle: 'Thông tin đặt hàng', checkoutSubtitle: 'Điền thông tin liên hệ để lưu cùng đơn hàng của bạn.',
            customerInfo: 'Thông tin liên hệ', fullName: 'Họ và tên *', emailLabel: 'Email *', phoneLabel: 'Số điện thoại *',
            namePlaceholder: 'Nhập họ và tên', emailPlaceholder: 'Nhập email', phonePlaceholder: 'Nhập số điện thoại',
            submitOrder: 'Gửi yêu cầu đặt hàng', summaryTitle: 'Tóm tắt đơn hàng',
            amountMayChange: 'Giá được Backend kiểm tra lại tại thời điểm đặt hàng.',
            checkoutDemo: 'Hệ thống chỉ tạo đơn demo, không xử lý thanh toán hay mua tên miền thật.',
            successTitle: 'Đặt hàng thành công!', successDesc: 'Đơn hàng demo đã được lưu vào hệ thống HEXAGON.',
            orderCode: 'Mã đơn hàng:', status: 'Trạng thái', orderItems: 'Tên miền đã đặt',
            total: 'Tổng giá trị đơn hàng', goHome: 'Về trang chủ',
            demoNote: 'HEXAGON · Môi trường thử nghiệm Local — Chưa thanh toán hoặc đăng ký tên miền thật.',
            pending_demo: 'Chờ xử lý', processing_demo: 'Đang xử lý', completed_demo: 'Đã xử lý', cancelled_demo: 'Đã hủy',
            pageCart: 'Giỏ hàng - HEXAGON', pageCheckout: 'Đặt hàng - HEXAGON', pageSuccess: 'Xác nhận đơn hàng - HEXAGON'
        },
        en: {
            home: 'Home', cart: 'Cart', login: 'Log in', register: 'Sign up', accountShort: 'Account', admin: 'Admin dashboard',
            myAccount: 'My account', myOrders: 'My orders', logout: 'Log out',
            accountMenu: 'Open account menu', backHome: '← Back to home', backCart: '← Back to cart',
            cartTitle: 'Your cart', cartSubtitle: 'Review your domains and costs before continuing.',
            orderStepsAria: 'Order steps', stepFind: 'Find a domain', stepCart: 'Cart', stepCheckout: 'Checkout',
            cartEyebrow: 'STEP 2 OF 3', checkoutEyebrow: 'STEP 3 OF 3', cartListTitle: 'Selected domains', domainCount: 'domains',
            summaryDesc: 'Review costs before submitting your request.', loginNeeded: 'Sign in to place your order; your cart will be saved.',
            emptyTitle: 'Your cart is empty', emptyCartHelp: 'Search for a domain and select “Add to cart” to get started.',
            contactHint: 'Fields marked * are required.',
            cartDemo: 'Demo cart. No actual payment or domain registration takes place.',
            itemOneYear: '1-year registration', remove: 'Remove from cart', emptyCart: 'Your cart is empty.',
            subtotal: 'Subtotal', continueShopping: 'Search more domains', proceedCheckout: 'Continue to checkout',
            checkoutTitle: 'Checkout', checkoutSubtitle: 'Enter the contact details to be saved with your order.',
            customerInfo: 'Contact information', fullName: 'Full name *', emailLabel: 'Email *', phoneLabel: 'Phone number *',
            namePlaceholder: 'Enter your full name', emailPlaceholder: 'Enter your email', phonePlaceholder: 'Enter your phone number',
            submitOrder: 'Submit order request', summaryTitle: 'Order summary',
            amountMayChange: 'The backend checks prices again when you place your order.',
            checkoutDemo: 'Demo orders only. No payment or actual domain registration occurs.',
            successTitle: 'Order placed!', successDesc: 'Your demo order has been saved in HEXAGON.',
            orderCode: 'Order reference:', status: 'Status', orderItems: 'Domain names',
            total: 'Order total', goHome: 'Back to home',
            demoNote: 'HEXAGON · Local demo environment — No actual payment or domain registration.',
            pending_demo: 'Pending', processing_demo: 'Processing', completed_demo: 'Processed', cancelled_demo: 'Cancelled',
            pageCart: 'Cart - HEXAGON', pageCheckout: 'Checkout - HEXAGON', pageSuccess: 'Order confirmation - HEXAGON'
        }
    };

    const storageKey = 'hexagon-language';
    let language = 'vi';
    try { language = localStorage.getItem(storageKey) === 'en' ? 'en' : 'vi'; } catch (_) { /* storage disabled */ }

    const t = (key) => messages[language][key] ?? key;
    const money = (value) => {
        const amount = Number(value);
        if (!Number.isFinite(amount)) return '';
        return new Intl.NumberFormat(language === 'en' ? 'en-US' : 'vi-VN', { maximumFractionDigits: 0 }).format(amount) + (language === 'en' ? ' VND' : 'đ');
    };

    const switcher = document.getElementById('hexagonLangToggle');
    const flag = document.getElementById('hexagonLangFlag');
    const accountMenu = document.getElementById('hexagonAccountMenu');

    function update() {
        document.documentElement.lang = language;
        const titleKey = document.body.dataset.pageTitle;
        if (titleKey) document.title = t(titleKey);
        document.querySelectorAll('[data-i18n]').forEach((element) => {
            element.textContent = t(element.dataset.i18n);
        });
        document.querySelectorAll('[data-i18n-placeholder]').forEach((element) => {
            element.placeholder = t(element.dataset.i18nPlaceholder);
        });
        document.querySelectorAll('[data-i18n-aria]').forEach((element) => {
            element.setAttribute('aria-label', t(element.dataset.i18nAria));
        });
        document.querySelectorAll('[data-money]').forEach((element) => {
            element.textContent = money(element.dataset.money);
        });
        document.querySelectorAll('[data-order-status]').forEach((element) => {
            const statusKey = element.dataset.orderStatus;
            element.textContent = messages[language][statusKey] ?? statusKey;
        });
        if (switcher && flag) {
            flag.src = language === 'vi' ? switcher.dataset.gb : switcher.dataset.vn;
            const label = language === 'vi' ? 'Switch to English' : 'Chuyển sang tiếng Việt';
            switcher.setAttribute('aria-label', label);
            switcher.title = label;
        }
    }

    switcher?.addEventListener('click', () => {
        language = language === 'vi' ? 'en' : 'vi';
        try { localStorage.setItem(storageKey, language); } catch (_) { /* no persistence */ }
        update();
    });

    const guestMenu = document.getElementById('hexagonGuestMenu');
    if (guestMenu) {
        document.addEventListener('click', (event) => {
            if (!guestMenu.contains(event.target)) guestMenu.open = false;
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && guestMenu.open) {
                guestMenu.open = false;
                guestMenu.querySelector('summary')?.focus();
            }
        });
    }

    if (accountMenu) {
        document.addEventListener('click', (event) => {
            if (!accountMenu.contains(event.target)) accountMenu.open = false;
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') accountMenu.open = false;
        });
    }

    update();
})();
