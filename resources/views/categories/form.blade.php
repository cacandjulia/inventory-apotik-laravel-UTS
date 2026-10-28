@extends('layouts.app')
@section('title','Form Kategori')
@section('content')
<div class="top"><h2>{{ $category->exists ? 'Ubah' : 'Tambah' }} Kategori</h2></div>
<form class="card narrow" method="POST" action="{{ $category->exists ? route('categories.update',$category) : route('categories.store') }}">
@csrf @if($category->exists) @method('PUT') @endif
<label>Nama kategori</label><input name="name" value="{{ old('name',$category->name) }}" required>
<p><button class="btn">Simpan</button> <a class="btn ghost" href="{{ route('categories.index') }}">Batal</a></p>
</form>
@endsection
