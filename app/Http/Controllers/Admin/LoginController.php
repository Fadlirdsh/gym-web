<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // 1️⃣ VALIDASI FORMAT INPUT
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // 2️⃣ CEK EMAIL ADA ATAU TIDAK
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()
                ->withErrors([
                    'email' => 'Email tidak terdaftar.',
                ])
                ->withInput();
        }

        // 3️⃣ EMAIL ADA, TAPI PASSWORD SALAH
        if (! Auth::guard('web')->attempt($request->only('email', 'password'))) {
            return back()
                ->withErrors([
                    'password' => 'Password salah.',
                ])
                ->withInput();
        }

        // 4️⃣ LOGIN SUKSES
        $request->session()->regenerate();

        return redirect()
            ->route('admin.login')
            ->with('login_success', true);
    }

    public function whboard()
    {
        return view('admin.home');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
