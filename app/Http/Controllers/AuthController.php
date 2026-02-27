<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Menampilkan Form Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Memproses Login
    public function login(Request $request)
    {
        // 1. Validasi Input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Coba Login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // 3. Cek Role User & Arahkan ke Dashboard yang sesuai
            $role = Auth::user()->role;

            switch ($role) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'owner':
                    return redirect()->route('owner.dashboard');
                case 'mandor':
                    return redirect()->route('mandor.dashboard');
                default:
                    return redirect('/'); // Jika role tidak dikenal
            }
        }

        // 4. Jika Gagal Login
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // Memproses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}