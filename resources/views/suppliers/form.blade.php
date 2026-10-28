@extends('layouts.app')
@section('title','Form Pemasok')
@section('content')
<div class="top"><h2>{{ $supplier->exists ? 'Ubah' : 'Tambah' }} Pemasok</h2></div>
<form class="card narrow" method="POST" action="{{ $supplier->exists ? route('suppliers.update',$supplier) : route('suppliers.store') }}">
@csrf @if($supplier->exists) @method('PUT') @endif
<label>Nama</label><input name="name" value="{{ old('name',$supplier->name) }}" required>
<label>Telepon</label><input name="phone" value="{{ old('phone',$supplier->phone) }}">
<label>Email</label><input name="email" value="{{ old('email',$supplier->email) }}">
<p><button class="btn">Simpan</button> <a class="btn ghost" href="{{ route('suppliers.index') }}">Batal</a></p>
</form>
@endsection
