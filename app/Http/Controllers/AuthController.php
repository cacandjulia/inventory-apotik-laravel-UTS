<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Msg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin() { return view('auth.login'); }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], ['required' => ':attribute wajib diisi.', 'email' => 'Format email tidak valid.']);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['email' => 'Email atau kata sandi salah.'])->onlyInput('email');
    }

    public function showRegister() { return view('auth.register'); }

    // Pendaftaran akun baru: SELALU menjadi role "pembeli"
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], Msg::id() + ['confirmed' => 'Konfirmasi kata sandi tidak sama.'],
           ['name' => 'Nama', 'email' => 'Email', 'password' => 'Kata sandi']);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],   // di-hash otomatis oleh model User
            'role' => 'pembeli',               // dipaksa di server, tidak bisa diubah dari form
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('products.index')->with('success', 'Akun berhasil dibuat. Selamat berbelanja!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
