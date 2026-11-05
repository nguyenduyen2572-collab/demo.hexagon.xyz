<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#071a37">
    <title>@yield('title', 'HEXAGON')</title>
    <link rel="stylesheet" href="{{ asset('css/hexagon-auth.css') }}?v=1">
    <script src="{{ asset('js/hexagon-auth-language.js') }}?v=1" defer></script>
    <link rel="stylesheet" href="{{ asset('css/hexagon-password-toggle.css') }}?v=1">
    <script src="{{ asset('js/hexagon-password-toggle.js') }}?v=1" defer></script>
    <link rel="stylesheet" href="{{ asset('css/hexagon-auth-cursor-state.css') }}?v=1">
    <script src="{{ asset('js/hexagon-auth-login-state.js') }}?v=1" defer></script>
</head>
<body data-auth-page="@yield('auth_page', 'register')"
      data-flag-gb="{{ asset('images/flags/gb.svg') }}"
      data-flag-vn="{{ asset('images/flags/vn.svg') }}">
    <div class="hx-scene" aria-hidden="true"></div>
    <header class="hx-topbar">
        <a class="hx-brand" href="{{ url('/') }}">
            <span class="hx-brand-icon" aria-hidden="true">H</span>
            HEXAGON
        </a>
        <nav class="hx-top-actions" aria-label="Điều hướng">
            <button id="hxLangToggle" class="hx-language" type="button"
                    aria-label="Switch to English" title="Switch to English">
                <img id="hxLangFlag" src="{{ asset('images/flags/gb.svg') }}"
                     width="27" height="27" alt="" aria-hidden="true">
            </button>
        </nav>
    </header>
    <main class="hx-main">
        <section class="hx-card" aria-labelledby="hxPageTitle">
            @yield('content')
        </section>
        <p class="hx-copyright">HEXAGON · <span data-i18n="footer">Môi trường thử nghiệm Local</span></p>
    </main>
</body>
</html>
