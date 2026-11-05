
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - HEXAGON</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: #f1f5f9;
            font-family: Arial, sans-serif;
            color: #172c49;
            padding: 20px;
        }
        .login {
            width: 100%;
            max-width: 420px;
            background: white;
            border-radius: 18px;
            padding: 36px;
            box-shadow: 0 12px 35px rgba(0,0,0,.06);
        }
        .logo {
            font-size: 30px;
            font-weight: 800;
            color: #1465d9;
            text-align: center;
        }
        h2 {
            text-align: center;
            font-size: 20px;
        }
        p { color: #64748b; text-align: center; }
        label {
            display: block;
            margin: 22px 0 8px;
            font-weight: 600;
        }
        input {
            width: 100%;
            padding: 14px;
            border-radius: 9px;
            border: 1px solid #cbd5e1;
            font-size: 15px;
        }
        button {
            width: 100%;
            margin-top: 26px;
            padding: 15px;
            border: none;
            border-radius: 9px;
            background: #1465d9;
            color: white;
            font-weight: 700;
            cursor: pointer;
        }
        .error {
            padding: 12px;
            border-radius: 8px;
            background: #fff1f2;
            color: #be123c;
            margin-top: 14px;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/hexagon-auth-cursor-state.css') }}?v=1">
    <script src="{{ asset('js/hexagon-auth-login-state.js') }}?v=1" defer></script>
    <link rel="stylesheet" href="{{ asset('css/hexagon-web-polish.css') }}?v=4">
    <script src="{{ asset('js/hexagon-web-polish.js') }}?v=4" defer></script>
</head>
<body>
    <div class="login">
        <div class="logo">HEXAGON</div>
        <h2>Đăng nhập quản trị</h2>

        @if($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf

            <label>Email Admin</label>
            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                autocomplete="username"
                required
            >

            <label>Mật khẩu</label>
            <input
                type="password"
                name="password"
                autocomplete="current-password"
                required
            >

            <button type="submit">Đăng nhập</button>
        </form>
    </div>
</body>
</html>
