@extends('layouts.app')
@section('title','Penjualan')
@section('content')
<div class="top"><h2>Penjualan</h2><a class="btn" href="{{ route('sales.create') }}">+ Penjualan Baru</a></div>
<form class="filters" method="GET">
  <input name="q" value="{{ request('q') }}" placeholder="Nomor transaksi...">
  <input type="date" name="from" value="{{ request('from') }}" title="Dari tanggal">
  <input type="date" name="to" value="{{ request('to') }}" title="Sampai tanggal">
  <select name="status"><option value="">Semua status</option><option value="completed" @selected(request('status')=='completed')>Selesai</option><option value="cancelled" @selected(request('status')=='cancelled')>Dibatalkan</option></select>
  <button class="btn">Filter</button>
</form>
<div class="card scroll"><table><tr><th>Nomor</th><th>Tanggal</th><th>Pembeli / Petugas</th><th class="right">Total</th><th>Status</th><th></th></tr>
@forelse($sales as $t)
<tr><td>{{ $t->sale_number }}</td><td>{{ $t->sold_at->format('d M Y H:i') }}</td><td>{{ $t->user->name }} @if($t->user->role=='pembeli')<span class="badge warn">pembeli</span>@endif</td>
<td class="right">Rp {{ number_format($t->total,0,',','.') }}</td>
<td><span class="badge {{ $t->status=='cancelled' ? 'bad' : '' }}">{{ $t->status=='cancelled' ? 'Dibatalkan' : 'Selesai' }}</span></td>
<td class="actions"><a class="btn ghost sm" href="{{ route('sales.show',$t) }}">Detail</a></td></tr>
@empty <tr><td colspan="6">Belum ada transaksi.</td></tr>@endforelse</table>{{ $sales->links() }}</div>
@endsection
