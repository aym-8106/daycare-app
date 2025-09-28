<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // ログイン監査ログ
            AuditLog::log(
                $user->office_id,
                $user->id,
                'users',
                $user->id,
                'login',
                ['ip' => $request->ip(), 'user_agent' => $request->userAgent()]
            );

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'メールアドレスまたはパスワードが正しくありません。',
        ])->onlyInput('email');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // ログアウト監査ログ
        if ($user) {
            AuditLog::log(
                $user->office_id,
                $user->id,
                'users',
                $user->id,
                'logout',
                ['ip' => $request->ip()]
            );
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}