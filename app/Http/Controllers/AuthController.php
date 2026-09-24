<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //menampilkan form login
    public function showLogin()
    {
        return view('auth.login');
    }

    //memproses login
    public function login(Request $request)
    {
         // 1. Validasi input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Coba login pake Auth::attempt()
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();      // Regenerate session (keamanan)
            
            $user = Auth::user();

            //redirect user berdasarkan role
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'petugas') {
                return redirect()->route('petugas.dashboard');
            } elseif ($user->role === 'peminjam') {
                return redirect()->route('peminjam.dashboard');
     
            }

            Auth::logout();
            return redirect()->route('login')->with('error', 'Role pengguna tidak valid.');
        }

        // 4. Kalo gagal
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    //proses logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil logout.');
    }
}
