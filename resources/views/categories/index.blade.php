@extends('layouts.app')
@section('title','Kategori')
@section('content')
<div class="top"><h2>Kategori</h2><a class="btn" href="{{ route('categories.create') }}">+ Tambah</a></div>
<div class="card scroll"><table><tr><th>Nama</th><th>Jumlah produk</th><th></th></tr>
@foreach($categories as $c)
<tr><td>{{ $c->name }}</td><td>{{ $c->products_count }}</td>
<td class="actions"><a class="btn ghost sm" href="{{ route('categories.edit',$c) }}">Ubah</a>
<form method="POST" action="{{ route('categories.destroy',$c) }}" onsubmit="return confirm('Hapus kategori ini?')">@csrf @method('DELETE')<button class="btn red sm">Hapus</button></form></td></tr>
@endforeach</table>{{ $categories->links() }}</div>
@endsection
