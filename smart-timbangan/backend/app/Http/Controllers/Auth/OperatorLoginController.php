<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperatorLoginController extends Controller
{
    /**
     * Tampilkan halaman landing (pilih login admin atau operator)
     */
    public function index()
    {
        // Jika sudah login, redirect sesuai role
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isAdmin()) {
                return redirect('/admin');
            }
            return redirect()->route('operator.dashboard');
        }

        return view('welcome');
    }

    /**
     * Proses login operator
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->isAdmin()) {
                return redirect('/admin');
            }

            return redirect()->route('operator.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Halaman dashboard operator setelah login
     */
    public function dashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('login.operator');
        }

        $user = Auth::user();

        // Jika admin nyasar ke sini, arahkan ke /admin
        if ($user->isAdmin()) {
            return redirect('/admin');
        }

        return view('operator.dashboard', compact('user'));
    }

    /**
     * Logout operator
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
