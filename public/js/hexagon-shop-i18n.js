/* HEXAGON Stage 12: chia sẻ ngôn ngữ với home.blade.php qua hexagon-language. */
(() => {
    'use strict';

    const messages = {
        vi: {
            home: 'Trang chủ', cart: 'Giỏ hàng', login: 'Đăng nhập', admin: 'Trang quản trị',
            myAccount: 'Tài khoản của tôi', myOrders: 'Đơn hàng của tôi', logout: 'Đăng xuất',
            accountMenu: 'Mở menu tài khoản', backHome: '← Về trang chủ', backCart: '← Quay lại giỏ hàng',
            cartTitle: 'Giỏ hàng của bạn', cartSubtitle: 'Kiểm tra tên miền trước khi đặt hàng thử nghiệm.',
            cartDemo: 'Giỏ hàng demo. Không thanh toán hay đăng ký tên miền thật.',
            itemOneYear: 'Đăng ký 1 năm', remove: 'Xóa khỏi giỏ hàng', emptyCart: 'Giỏ hàng của bạn đang trống.',
            subtotal: 'Tạm tính', continueShopping: 'Tiếp tục tìm tên miền', proceedCheckout: 'Tiếp tục đặt hàng',
            checkoutTitle: 'Thông tin đặt hàng', checkoutSubtitle: 'Điền thông tin liên hệ để tạo đơn hàng thử nghiệm.',
            customerInfo: 'Thông tin khách hàng', fullName: 'Họ và tên *', emailLabel: 'Email *', phoneLabel: 'Số điện thoại *',
            namePlaceholder: 'Nhập họ và tên', emailPlaceholder: 'Nhập email', phonePlaceholder: 'Nhập số điện thoại',
            submitOrder: 'Xác nhận đặt hàng', summaryTitle: 'Đơn hàng của bạn',
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
            home: 'Home', cart: 'Cart', login: 'Log in', admin: 'Admin dashboard',
            myAccount: 'My account', myOrders: 'My orders', logout: 'Log out',
            accountMenu: 'Open account menu', backHome: '← Back to home', backCart: '← Back to cart',
            cartTitle: 'Your cart', cartSubtitle: 'Review your domain names before placing a demo order.',
            cartDemo: 'Demo cart. No actual payment or domain registration takes place.',
            itemOneYear: '1-year registration', remove: 'Remove from cart', emptyCart: 'Your cart is empty.',
            subtotal: 'Subtotal', continueShopping: 'Search more domains', proceedCheckout: 'Continue to checkout',
            checkoutTitle: 'Checkout', checkoutSubtitle: 'Enter your contact details to create a demo order.',
            customerInfo: 'Contact information', fullName: 'Full name *', emailLabel: 'Email *', phoneLabel: 'Phone number *',
            namePlaceholder: 'Enter your full name', emailPlaceholder: 'Enter your email', phonePlaceholder: 'Enter your phone number',
            submitOrder: 'Place demo order', summaryTitle: 'Order summary',
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
