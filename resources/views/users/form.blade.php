@extends('layouts.app')
@section('title','Form Pengguna')
@section('content')
<div class="top"><h2>{{ $user->exists ? 'Ubah' : 'Tambah' }} Pengguna</h2></div>
<form class="card narrow" method="POST" action="{{ $user->exists ? route('users.update',$user) : route('users.store') }}">
@csrf @if($user->exists) @method('PUT') @endif
<label>Nama</label><input name="name" value="{{ old('name',$user->name) }}" required>
<label>Email</label><input type="email" name="email" value="{{ old('email',$user->email) }}" required>
<label>Role</label><select name="role">@foreach(['pembeli','staff','admin'] as $r)<option value="{{ $r }}" @selected(old('role',$user->role ?? 'staff')==$r)>{{ $r }}</option>@endforeach</select>
<label>Kata sandi {{ $user->exists ? '(kosongkan jika tidak diganti)' : '' }}</label><input type="password" name="password" {{ $user->exists ? '' : 'required' }}>
<p><button class="btn">Simpan</button> <a class="btn ghost" href="{{ route('users.index') }}">Batal</a></p>
</form>
@endsection
