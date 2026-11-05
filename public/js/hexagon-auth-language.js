/* HEXAGON VI/EN: shares localStorage('hexagon-language') with the homepage. */
(() => {
    'use strict';
    const translations = {
        vi: {
            home: 'Trang chủ', footer: 'Môi trường thử nghiệm Local',
            registerTitle: 'Tạo tài khoản', registerLead: 'Bắt đầu hành trình tên miền của bạn với HEXAGON.',
            fullName: 'Họ và tên', email: 'Email', password: 'Mật khẩu',
            passwordHint: 'Ít nhất 12 ký tự.', confirmPassword: 'Nhập lại mật khẩu',
            registerButton: 'Đăng ký tài khoản', haveAccount: 'Đã có tài khoản?', login: 'Đăng nhập',
            forgotTitle: 'Quên mật khẩu?', forgotLead: 'Nhập email để nhận liên kết đặt lại mật khẩu.',
            forgotButton: 'Gửi liên kết đặt lại mật khẩu', backLogin: '← Quay lại đăng nhập',
            localEmailNote: 'Chế độ Local: liên kết thử nghiệm nằm trong storage/logs/laravel.log.',
            resetTitle: 'Đặt lại mật khẩu', resetLead: 'Tạo mật khẩu mới tối thiểu 12 ký tự.',
            newPassword: 'Mật khẩu mới', confirmNewPassword: 'Nhập lại mật khẩu mới', resetButton: 'Lưu mật khẩu mới',
            verifyTitle: 'Xác minh địa chỉ email', verifyLead: 'Vui lòng xác minh email trước khi sử dụng khu vực tài khoản.',
            verifyButton: 'Gửi lại liên kết xác minh', backHome: '← Về trang chủ',
            localVerifyNote: 'Trong WAMP Local, liên kết xác minh nằm trong storage/logs/laravel.log. Không chia sẻ liên kết xác minh.'
        },
        en: {
            home: 'Home', footer: 'Local testing environment',
            registerTitle: 'Create an account', registerLead: 'Start your domain journey with HEXAGON.',
            fullName: 'Full name', email: 'Email', password: 'Password',
            passwordHint: 'Use at least 12 characters.', confirmPassword: 'Confirm password',
            registerButton: 'Create account', haveAccount: 'Already have an account?', login: 'Log in',
            forgotTitle: 'Forgot password?', forgotLead: 'Enter your email address to receive a password reset link.',
            forgotButton: 'Send reset link', backLogin: '← Back to login',
            localEmailNote: 'Local mode: the test link is written to storage/logs/laravel.log.',
            resetTitle: 'Reset password', resetLead: 'Set a new password with at least 12 characters.',
            newPassword: 'New password', confirmNewPassword: 'Confirm new password', resetButton: 'Save new password',
            verifyTitle: 'Verify your email', verifyLead: 'Please verify your email before accessing your account.',
            verifyButton: 'Resend verification link', backHome: '← Back to homepage',
            localVerifyNote: 'On WAMP Local, the verification link is written to storage/logs/laravel.log. Do not share it.'
        }
    };
    const titles = {
        register: {vi: 'Đăng ký | HEXAGON', en: 'Register | HEXAGON'},
        forgot: {vi: 'Quên mật khẩu | HEXAGON', en: 'Forgot password | HEXAGON'},
        reset: {vi: 'Đặt lại mật khẩu | HEXAGON', en: 'Reset password | HEXAGON'},
        verify: {vi: 'Xác minh email | HEXAGON', en: 'Verify email | HEXAGON'}
    };
    const body = document.body;
    const toggle = document.getElementById('hxLangToggle');
    const flag = document.getElementById('hxLangFlag');
    if (!body || !toggle || !flag) return;

    let lang = 'vi';
    try { lang = localStorage.getItem('hexagon-language') === 'en' ? 'en' : 'vi'; } catch (_) {}

    function applyLanguage() {
        const strings = translations[lang];
        document.documentElement.lang = lang;
        const page = body.dataset.authPage || 'register';
        document.title = titles[page]?.[lang] || 'HEXAGON';
        document.querySelectorAll('[data-i18n]').forEach((el) => {
            const key = el.getAttribute('data-i18n');
            if (Object.prototype.hasOwnProperty.call(strings, key)) el.textContent = strings[key];
        });
        flag.src = lang === 'vi' ? body.dataset.flagGb : body.dataset.flagVn;
        const label = lang === 'vi' ? 'Switch to English' : 'Chuyển sang tiếng Việt';
        toggle.setAttribute('aria-label', label);
        toggle.title = label;
    }

    toggle.addEventListener('click', () => {
        lang = lang === 'vi' ? 'en' : 'vi';
        try { localStorage.setItem('hexagon-language', lang); } catch (_) {}
        applyLanguage();
    });
    applyLanguage();
})();
