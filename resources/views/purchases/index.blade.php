@extends('layouts.app')
@section('title','Pembelian')
@section('content')
<div class="top"><h2>Pembelian</h2><a class="btn" href="{{ route('purchases.create') }}">+ Pembelian Baru</a></div>
<form class="filters" method="GET">
  <input name="q" value="{{ request('q') }}" placeholder="Nomor transaksi...">
  <input type="date" name="from" value="{{ request('from') }}" title="Dari tanggal">
  <input type="date" name="to" value="{{ request('to') }}" title="Sampai tanggal">
  <select name="status"><option value="">Semua status</option><option value="completed" @selected(request('status')=='completed')>Selesai</option><option value="cancelled" @selected(request('status')=='cancelled')>Dibatalkan</option></select>
  <button class="btn">Filter</button>
</form>
<div class="card scroll"><table><tr><th>Nomor</th><th>Tanggal</th><th>Pemasok</th><th>Petugas</th><th class="right">Total</th><th>Status</th><th></th></tr>
@forelse($purchases as $t)
<tr><td>{{ $t->purchase_number }}</td><td>{{ $t->purchased_at->format('d M Y H:i') }}</td><td>{{ $t->supplier->name }}</td><td>{{ $t->user->name }}</td>
<td class="right">Rp {{ number_format($t->total,0,',','.') }}</td>
<td><span class="badge {{ $t->status=='cancelled' ? 'bad' : '' }}">{{ $t->status=='cancelled' ? 'Dibatalkan' : 'Selesai' }}</span></td>
<td class="actions"><a class="btn ghost sm" href="{{ route('purchases.show',$t) }}">Detail</a></td></tr>
@empty <tr><td colspan="7">Belum ada transaksi.</td></tr>@endforelse</table>{{ $purchases->links() }}</div>
@endsection
