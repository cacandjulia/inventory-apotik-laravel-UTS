@extends('layouts.app')
@section('title','Dashboard')
@section('content')
<div class="top"><h2>Halo, {{ auth()->user()->name }} </h2></div>
<div class="grid">
  <div class="card stat"><small>Total produk</small><b>{{ $totalProducts }}</b></div>
  <div class="card stat"><small>Stok rendah</small><b>{{ $lowStockCount }}</b></div>
  <div class="card stat"><small>Penjualan hari ini</small><b>Rp {{ number_format($salesToday,0,',','.') }}</b></div>
  <div class="card stat"><small>Penjualan bulan ini</small><b>Rp {{ number_format($salesMonth,0,',','.') }}</b></div>
  <div class="card stat"><small>Transaksi penjualan</small><b>{{ $salesCount }}</b></div>
  <div class="card stat"><small>Transaksi pembelian</small><b>{{ $purchaseCount }}</b></div>
</div>
<div class="card">
  <h3 style="margin-top:0">Produk hampir habis</h3>
  <div class="scroll"><table><tr><th>SKU</th><th>Produk</th><th>Kategori</th><th>Stok</th><th>Min</th></tr>
  @forelse($lowStock as $p)
    <tr><td>{{ $p->sku }}</td><td><a href="{{ route('products.show',$p) }}">{{ $p->name }}</a></td><td>{{ $p->category->name }}</td><td><span class="badge bad">{{ $p->stock }}</span></td><td>{{ $p->minimum_stock }}</td></tr>
  @empty <tr><td colspan="5">Semua stok aman </td></tr>
  @endforelse</table></div>
</div>
@endsection
