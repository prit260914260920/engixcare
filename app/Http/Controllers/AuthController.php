<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $request->session()->forget('url.intended');
            return redirect()->route('home');
        }

        return back()->withErrors(['email' => 'These credentials do not match our records.'])->onlyInput('email');
    }

    // ── Admin Auth ────────────────────────────────────────────────────────────

    public function showAdminLogin()
    {
        if (Auth::check() && Auth::user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            if (Auth::user()->is_admin) {
                $request->session()->regenerate();

                // Only use the intended URL if it points to an admin path.
                // A stale url.intended from the public-facing auth middleware
                // (e.g. /user/has-used-coupon) must not hijack the admin redirect.
                $intended = $request->session()->pull('url.intended');
                $target = ($intended && str_starts_with(
                    parse_url($intended, PHP_URL_PATH) ?? '', '/admin'
                ))
                    ? $intended
                    : route('admin.dashboard');

                return redirect($target);
            }

            // Logged in but not an admin — log them back out
            Auth::logout();
        }

        return back()
            ->withErrors(['email' => 'These credentials do not match our records or you are not authorised.'])
            ->onlyInput('email');
    }

    public function adminLogout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
