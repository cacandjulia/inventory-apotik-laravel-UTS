@extends('layouts.app')
@section('title','Pemasok')
@section('content')
<div class="top"><h2>Pemasok</h2><a class="btn" href="{{ route('suppliers.create') }}">+ Tambah</a></div>
<form class="filters" method="GET"><input name="q" value="{{ request('q') }}" placeholder="Cari nama pemasok..."><button class="btn">Cari</button></form>
<div class="card scroll"><table><tr><th>Nama</th><th>Telepon</th><th>Email</th><th></th></tr>
@forelse($suppliers as $s)
<tr><td>{{ $s->name }}</td><td>{{ $s->phone }}</td><td>{{ $s->email }}</td>
<td class="actions"><a class="btn ghost sm" href="{{ route('suppliers.edit',$s) }}">Ubah</a>
<form method="POST" action="{{ route('suppliers.destroy',$s) }}" onsubmit="return confirm('Hapus pemasok ini?')">@csrf @method('DELETE')<button class="btn red sm">Hapus</button></form></td></tr>
@empty <tr><td colspan="4">Belum ada pemasok.</td></tr>@endforelse</table>{{ $suppliers->links() }}</div>
@endsection
