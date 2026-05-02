<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\CaptchaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        $captcha = CaptchaService::generate();
        CaptchaService::store($captcha);
        return view('auth.login', compact('captcha'));
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'captcha' => ['required', 'string', 'size:8'],
        ]);

        if (!CaptchaService::validate($request->input('captcha'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['captcha' => 'Invalid captcha. Please try again.']);
        }

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'These credentials do not match our records.']);
        }

        $user = Auth::user();
        if (!$user->is_active) {
            Auth::logout();
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Your account has been deactivated.']);
        }

        $request->session()->regenerate();
        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function refreshCaptcha(): \Illuminate\Http\JsonResponse
    {
        $captcha = CaptchaService::generate();
        CaptchaService::store($captcha);
        return response()->json(['captcha' => $captcha]);
    }
}
