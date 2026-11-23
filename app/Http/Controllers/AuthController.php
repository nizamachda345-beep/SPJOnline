<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

// Method: showLogin
public function showLogin()
{
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->isActualSuperAdmin()) {
            return $user->accessMode() === 'super_admin'
                ? redirect()->route('admin.dashboard')
                : redirect()->route($user->accessMode() === 'bpk' ? 'bpk.dashboard' : 'dashboard');
        }

        if ($user->hasRole('bpk')) {
            return redirect()->route('bpk.dashboard');
        }

        return redirect()->route('dashboard');
    }
    return view('auth.login');
}

   // Method: login
   public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->isActualSuperAdmin()) {
            $request->session()->put('master_access_mode', 'super_admin');
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('bpk')) {
            return redirect()->route('bpk.dashboard');
        }

        return redirect()->route('dashboard');
    }

    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ])->onlyInput('email');
}

    // Method: logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

}
