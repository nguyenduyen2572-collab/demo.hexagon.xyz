<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#071a37">
    <title>Đăng nhập | HEXAGON</title>
    <style>
        :root {
            color-scheme: dark;
            --blue: #247af0;
            --blue-light: #58a6ff;
            --ink: #10223d;
            --muted: #64748b;
        }
        *, *::before, *::after { box-sizing: border-box; }
        html { min-height: 100%; }
        body {
            margin: 0;
            min-height: 100vh;
            min-height: 100dvh;
            color: #ffffff;
            font-family: Inter, system-ui, -apple-system, "Segoe UI", Arial, sans-serif;
            background: #06142e;
            overflow-x: hidden;
        }
        .scene {
            position: fixed;
            z-index: -1;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
            background:
                radial-gradient(ellipse 60% 65% at 12% 14%, rgba(32,100,228,.34), transparent 70%),
                radial-gradient(ellipse 54% 56% at 88% 80%, rgba(4,157,216,.24), transparent 70%),
                linear-gradient(128deg, #061329 0%, #0b2550 49%, #071730 100%);
        }
        .scene::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(142,190,255,.072) 1px, transparent 1px),
                linear-gradient(90deg, rgba(142,190,255,.072) 1px, transparent 1px);
            background-size: 54px 54px;
            mask-image: linear-gradient(to bottom, rgba(0,0,0,.75), transparent 95%);
        }
        .scene::after {
            content: "";
            position: absolute;
            width: min(80vw, 850px);
            aspect-ratio: 1;
            border: 1px solid rgba(111,179,255,.12);
            border-radius: 50%;
            left: -22%;
            bottom: -75%;
            box-shadow: 0 0 0 95px rgba(105,165,255,.035), 0 0 0 190px rgba(105,165,255,.026);
        }
        .orb {
            position: absolute;
            width: 330px;
            height: 330px;
            right: -125px;
            top: 17%;
            border-radius: 50%;
            filter: blur(70px);
            background: rgba(24,127,241,.25);
        }
        .topbar {
            width: min(1180px, calc(100% - 40px));
            margin: 0 auto;
            min-height: 90px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }
        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #ffffff;
            font-size: 24px;
            font-weight: 850;
            letter-spacing: .75px;
        }
        .brand-mark {
            display: inline-grid;
            place-items: center;
            width: 34px;
            height: 34px;
            border: 2px solid #55a6ff;
            clip-path: polygon(25% 4%,75% 4%,100% 50%,75% 96%,25% 96%,0 50%);
            background: rgba(34,122,239,.23);
            color: #b7dcff;
            font-size: 15px;
        }
        .top-actions { display: flex; align-items: center; gap: 18px; }
        .home-link {
            color: #dceaff;
            text-decoration: none;
            font-size: 13px;
            font-weight: 650;
            transition: color .2s ease;
        }
        .home-link:hover { color: #ffffff; }
        .language-button {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            height: 42px;
            width: 42px;
            padding: 6px;
            border: 1px solid rgba(185,218,255,.24);
            border-radius: 50%;
            background: rgba(255,255,255,.075);
            cursor: pointer;
            transition: background .2s ease, transform .2s ease;
        }
        .language-button:hover { background: rgba(255,255,255,.15); transform: translateY(-1px); }
        .language-button img { height: 27px; width: 27px; border-radius: 50%; display: block; object-fit: cover; }
        .page {
            min-height: calc(100dvh - 90px);
            display: grid;
            place-items: center;
            padding: 38px 18px 85px;
        }
        .login-card {
            width: min(100%, 450px);
            position: relative;
            padding: clamp(28px, 5vw, 43px);
            border-radius: 22px;
            color: var(--ink);
            background: rgba(255,255,255,.97);
            border: 1px solid rgba(255,255,255,.8);
            box-shadow: 0 24px 70px rgba(1,10,28,.45), 0 0 0 1px rgba(95,173,255,.14);
            animation: rise-in .55s ease both;
        }
        .login-card::before {
            content: "";
            position: absolute;
            height: 4px;
            left: 32px;
            right: 32px;
            top: 0;
            background: linear-gradient(90deg, transparent 0%, #3179f5 30%, #61c5f9 70%, transparent 100%);
            border-radius: 0 0 12px 12px;
        }
        .form-icon {
            width: 56px;
            height: 56px;
            display: grid;
            place-items: center;
            border-radius: 16px;
            background: linear-gradient(145deg,#e4f0ff,#f2f7ff);
            color: #1663d3;
            margin: 0 auto 16px;
            box-shadow: inset 0 0 0 1px #cfe1ff;
        }
        .form-icon svg { width: 28px; height: 28px; }
        h1 { text-align: center; font-size: clamp(26px, 4vw, 30px); letter-spacing: -.8px; margin: 0 0 24px; color: #10294e; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 14px; font-weight: 700; margin-bottom: 9px; color: #243d5c; }
        .field {
            display: block;
            width: 100%;
            height: 51px;
            color: #132e51;
            background: #f8fbff;
            border: 1px solid #cddbef;
            border-radius: 11px;
            outline: none;
            padding: 0 16px;
            font: inherit;
            font-size: 15px;
            transition: box-shadow .2s ease, border-color .2s ease, background .2s ease;
        }
        .field:hover { border-color: #9dbfea; }
        .field:focus { border-color: #2c7beb; box-shadow: 0 0 0 4px rgba(44,123,235,.13); background: #ffffff; }
        .submit {
            width: 100%;
            min-height: 53px;
            margin-top: 5px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 9px;
            background: linear-gradient(115deg, #0c59cb, #1978ef 55%, #1686ef);
            color: #fff;
            border: 0;
            border-radius: 11px;
            box-shadow: 0 8px 20px rgba(18,101,219,.24);
            font-size: 15px;
            font-weight: 750;
            cursor: pointer;
            transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
        }
        .submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 30px rgba(15,94,225,.39);
            filter: brightness(1.09);
        }
        .submit:active { transform: translateY(0); box-shadow: 0 5px 15px rgba(15,94,225,.22); }
        .submit:focus-visible, a:focus-visible, button:focus-visible { outline: 3px solid #74b9ff; outline-offset: 3px; }
        .forgot {
            margin: 11px 0 0;
            text-align: center;
        }
        .forgot a { color: #1164d8; font-size: 14px; font-weight: 700; text-decoration: none; }
        .forgot a:hover { text-decoration: underline; color: #083f91; }
        .sep { display: flex; align-items: center; gap: 12px; color: #9cabc1; margin: 13px 0 12px; }
        .sep::before, .sep::after { content: ""; height: 1px; flex: 1; background: #e4ebf4; }
        .sep span { font-size: 12px; }
        .register { text-align: center; color: #667a93; font-size: 14px; margin: 0; }
        .register a { text-decoration: none; color: #1465d9; font-weight: 800; }
        .register a:hover { text-decoration: underline; }
        .notice { border-radius: 10px; padding: 13px 14px; margin-bottom: 19px; font-size: 13px; line-height: 1.6; }
        .notice-error { background: #fff1f2; border: 1px solid #fecdd3; color: #9f1239; }
        .notice-success { background: #edfdf4; border: 1px solid #bbf7d0; color: #166534; }
        @keyframes rise-in { from {opacity:0; transform:translateY(15px)} to {opacity:1;transform:translateY(0)} }
        @media (max-width: 520px) {
            .topbar { min-height: 73px; }
            .brand { font-size: 20px; }
            .page { min-height: calc(100dvh - 73px); padding: 12px 15px 45px; }
            .login-card { border-radius: 17px; }
            .top-actions { gap: 9px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/hexagon-password-toggle.css') }}?v=1">
    <script src="{{ asset('js/hexagon-password-toggle.js') }}?v=1" defer></script>
    <link rel="stylesheet" href="{{ asset('css/hexagon-auth-cursor-state.css') }}?v=1">
    <script src="{{ asset('js/hexagon-auth-login-state.js') }}?v=1" defer></script>
    <link rel="stylesheet" href="{{ asset('css/hexagon-web-polish.css') }}?v=4">
    <script src="{{ asset('js/hexagon-web-polish.js') }}?v=4" defer></script>
</head>
<body>
<div class="scene" aria-hidden="true"><div class="orb"></div></div>

<header class="topbar">
    <a href="/" class="brand"><span class="brand-mark" aria-hidden="true">H</span> HEXAGON</a>
    <div class="top-actions">
        <button type="button" id="langSwitch" class="language-button" aria-label="Switch to English" title="Switch to English">
            <img id="langFlag" src="{{ asset('images/flags/gb.svg') }}" alt="" aria-hidden="true" width="27" height="27">
        </button>
    </div>
</header>

<main class="page">
    <section class="login-card" aria-labelledby="login-title">
        <h1 id="login-title" data-i18n="title">Đăng nhập</h1>

        @if(session('status'))
            <div class="notice notice-success" role="status">{{ session('status') }}</div>
        @endif
        @if($errors->any())
            <div class="notice notice-error" role="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('customer.login.store') }}">
            @csrf
            <div class="form-group">
                <label for="email" data-i18n="email">Email</label>
                <input class="field" type="email" name="email" id="email" value="{{ old('email') }}" autocomplete="username" maxlength="190" required autofocus>
            </div>
            <div class="form-group">
                <label for="password" data-i18n="password">Mật khẩu</label>
        <div class="pw-field">
            <input class="field" type="password" name="password" id="password" autocomplete="current-password" required>
            <button class="pw-toggle" type="button" data-password-toggle data-target="password"
                    aria-controls="password" aria-pressed="false" aria-label="Hiển thị mật khẩu" title="Hiển thị mật khẩu">
                <svg class="pw-icon pw-eye" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M2.2 12s3.5-6 9.8-6 9.8 6 9.8 6-3.5 6-9.8 6-9.8-6-9.8-6z" />
                    <circle cx="12" cy="12" r="3" />
                </svg>
                <svg class="pw-icon pw-eye-off" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 3l18 18M10.6 6.1A11.5 11.5 0 0 1 12 6c6.3 0 9.8 6 9.8 6a15 15 0 0 1-3.1 3.6M6.7 6.9C3.8 8.7 2.2 12 2.2 12s3.5 6 9.8 6c1.5 0 2.9-.3 4.1-.9" />
                    <path d="M10 10a3 3 0 0 0 4 4" />
                </svg>
            </button>
        </div>
            </div>
            <button class="submit" type="submit">
                <span data-i18n="submit">Đăng nhập</span>
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M5 12h14M13 6l6 6-6 6"/>
                </svg>
            </button>
        </form>

        <p class="forgot">
            <a href="{{ route('password.request') }}" data-i18n="forgot">Quên mật khẩu?</a>
        </p>
        <div class="sep" aria-hidden="true"><span>✦</span></div>
        <p class="register">
            <span data-i18n="noAccount">Chưa có tài khoản?</span>
            <a href="{{ route('customer.register') }}" data-i18n="register">Đăng ký</a>
        </p>
    </section>
</main>

<script>
(() => {
    const dictionary = {
        vi: {
            page: 'Đăng nhập | HEXAGON', home: 'Trang chủ', title: 'Đăng nhập',
            email: 'Email', password: 'Mật khẩu', submit: 'Đăng nhập',
            forgot: 'Quên mật khẩu?', noAccount: 'Chưa có tài khoản?', register: 'Đăng ký'
        },
        en: {
            page: 'Log in | HEXAGON', home: 'Home', title: 'Log in',
            email: 'Email', password: 'Password', submit: 'Log in',
            forgot: 'Forgot password?', noAccount: 'New to HEXAGON?', register: 'Create an account'
        }
    };

    let locale = 'vi';
    try { locale = localStorage.getItem('hexagon-language') === 'en' ? 'en' : 'vi'; } catch (_) {}

    const languageButton = document.getElementById('langSwitch');
    const flag = document.getElementById('langFlag');

    function applyLanguage() {
        const copy = dictionary[locale];
        document.documentElement.lang = locale;
        document.title = copy.page;
        document.querySelectorAll('[data-i18n]').forEach(el => {
            const key = el.getAttribute('data-i18n');
            if (Object.prototype.hasOwnProperty.call(copy, key)) el.textContent = copy[key];
        });
        flag.src = locale === 'vi'
            ? "{{ asset('images/flags/gb.svg') }}"
            : "{{ asset('images/flags/vn.svg') }}";
        languageButton.title = locale === 'vi' ? 'Switch to English' : 'Chuyển sang tiếng Việt';
        languageButton.setAttribute('aria-label', languageButton.title);
    }

    languageButton.addEventListener('click', () => {
        locale = locale === 'vi' ? 'en' : 'vi';
        try { localStorage.setItem('hexagon-language', locale); } catch (_) {}
        applyLanguage();
    });
    applyLanguage();
})();
</script>
</body>
</html>
