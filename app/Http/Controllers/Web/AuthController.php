<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(protected SettingsService $settingsService)
    {
    }

    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        $settings = $this->settingsService->all();
        return view('admin.auth.login', compact('settings'));
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'))->with('success', 'Welcome back, ' . Auth::user()->name);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our administrator records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Logged out successfully.');
    }
}
