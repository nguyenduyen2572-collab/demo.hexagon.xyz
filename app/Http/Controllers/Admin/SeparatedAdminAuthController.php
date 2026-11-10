<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Admin authentication belongs to the admin session guard only.
 * The existing admin.login Blade view and named routes remain unchanged.
 */
final class SeparatedAdminAuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:190'],
            'password' => ['required', 'string'],
        ]);

        // Never allow a regular customer to obtain an Admin session.
        $credentials = [
            'email' => strtolower(trim((string) $data['email'])),
            'password' => $data['password'],
            'is_admin' => true,
        ];

        if (! Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Email hoặc mật khẩu Admin không chính xác.',
            ])->onlyInput('email');
        }

        // Clear only a legacy web-guard Admin session after a successful
        // dedicated Admin login. Never remove a genuine Customer web session.
        $webUser = Auth::guard('web')->user();
        if ($webUser !== null && (bool) $webUser->is_admin) {
            Auth::guard('web')->logout();
        }

        // Keep cart and any genuine Customer authentication intact.
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        // Do not invalidate the entire session: doing so would log out Customer too.
        $request->session()->regenerate();

        return redirect()->route('login');
    }
}
