<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Msg;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index() { return view('users.index', ['users' => User::orderBy('name')->paginate(10)]); }

    public function create() { return view('users.form', ['user' => new User]); }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules() + ['password' => ['required', 'string', 'min:8']], Msg::id(), $this->names());
        User::create($data);
        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user) { return view('users.form', compact('user')); }

    public function update(Request $request, User $user)
    {
        $data = $request->validate($this->rules($user) + ['password' => ['nullable', 'string', 'min:8']], Msg::id(), $this->names());
        if (empty($data['password'])) unset($data['password']); // kosong = tidak ganti password
        $user->update($data);
        return redirect()->route('users.index')->with('success', 'Pengguna berhasil diubah.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'Kamu tidak bisa menghapus akun yang sedang dipakai.']);
        }
        if ($user->sales()->exists() || $user->purchases()->exists()) {
            return back()->withErrors(['user' => 'Pengguna sudah punya transaksi, tidak bisa dihapus.']);
        }
        $user->delete();
        return back()->with('success', 'Pengguna dihapus.');
    }

    private function rules(?User $user = null): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email' . ($user ? ',' . $user->id : '')],
            'role' => ['required', Rule::in(['admin', 'staff', 'pembeli'])],
        ];
    }

    private function names(): array
    {
        return ['name' => 'Nama', 'email' => 'Email', 'role' => 'Role', 'password' => 'Kata sandi'];
    }
}
