@extends('layouts.app')
@section('title','Laporan Penjualan')
@section('content')
<div class="top"><h2>Laporan Penjualan</h2><button class="btn ghost noprint" onclick="window.print()">Cetak</button></div>
<form class="filters noprint" method="GET">
  <input type="date" name="from" value="{{ $from }}" title="Dari tanggal">
  <input type="date" name="to" value="{{ $to }}" title="Sampai tanggal">
  <button class="btn">Tampilkan</button>
</form>
<p style="font-size:13px;color:#7b6d94">Periode {{ \Carbon\Carbon::parse($from)->format('d M Y') }} sampai {{ \Carbon\Carbon::parse($to)->format('d M Y') }}. Transaksi yang dibatalkan tidak dihitung.</p>
<div class="grid">
  <div class="card stat"><small>Total penjualan</small><b>Rp {{ number_format($revenue,0,',','.') }}</b></div>
  <div class="card stat"><small>Jumlah transaksi</small><b>{{ $count }}</b></div>
  <div class="card stat"><small>Item terjual</small><b>{{ $itemsSold }}</b></div>
</div>
<div class="card scroll"><h3 style="margin-top:0">Per hari</h3>
<table><tr><th>Tanggal</th><th>Transaksi</th><th class="right">Total</th></tr>
@forelse($perDay as $d)
<tr><td>{{ \Carbon\Carbon::parse($d->day)->format('d M Y') }}</td><td>{{ $d->trx }}</td><td class="right">Rp {{ number_format($d->revenue,0,',','.') }}</td></tr>
@empty <tr><td colspan="3">Belum ada penjualan di periode ini.</td></tr>@endforelse</table></div>
<div class="card scroll"><h3 style="margin-top:0">Per produk (terlaris di atas)</h3>
<table><tr><th>SKU</th><th>Produk</th><th>Terjual</th><th class="right">Total</th></tr>
@forelse($perProduct as $p)
<tr><td>{{ $p->sku }}</td><td>{{ $p->name }}</td><td>{{ $p->qty }}</td><td class="right">Rp {{ number_format($p->revenue,0,',','.') }}</td></tr>
@empty <tr><td colspan="4">Belum ada data.</td></tr>@endforelse</table></div>
<p class="noprint"><a class="btn ghost" href="{{ route('sales.index',['from'=>$from,'to'=>$to]) }}">Lihat daftar transaksi penjualan</a></p>
@endsection
