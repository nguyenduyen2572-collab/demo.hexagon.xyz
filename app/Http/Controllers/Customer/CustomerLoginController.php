<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerLoginController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->to(Auth::guard('web')->user()->is_admin ? route('admin.dashboard') : '/');
        }

        return view('customer.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Customer accounts only; never authenticate Admin via the customer form.
        if (Auth::guard('web')->attempt([
            'email' => strtolower(trim($credentials['email'])),
            'password' => $credentials['password'],
            'is_admin' => false,
        ])) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không chính xác.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        // The customer logout endpoint must not log out an Admin account.
        abort_unless(Auth::guard('web')->check() && !Auth::guard('web')->user()->is_admin, 403);
        Auth::guard('web')->logout();
        $request->session()->regenerate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
