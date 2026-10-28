@extends('layouts.app')
@section('title','Pengguna')
@section('content')
<div class="top"><h2>Pengguna & Role</h2><a class="btn" href="{{ route('users.create') }}">+ Tambah</a></div>
<div class="card scroll"><table><tr><th>Nama</th><th>Email</th><th>Role</th><th></th></tr>
@foreach($users as $u)
<tr><td>{{ $u->name }}</td><td>{{ $u->email }}</td><td><span class="badge {{ $u->role=='admin' ? 'warn' : '' }}">{{ $u->role }}</span></td>
<td class="actions"><a class="btn ghost sm" href="{{ route('users.edit',$u) }}">Ubah</a>
<form method="POST" action="{{ route('users.destroy',$u) }}" onsubmit="return confirm('Hapus pengguna ini?')">@csrf @method('DELETE')<button class="btn red sm">Hapus</button></form></td></tr>
@endforeach</table>{{ $users->links() }}</div>
@endsection
