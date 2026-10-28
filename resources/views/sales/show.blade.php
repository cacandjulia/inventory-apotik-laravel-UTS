@extends('layouts.app')
@section('title','Detail Penjualan')
@section('content')
<div class="top"><h2>{{ $sale->sale_number }}</h2><a class="btn ghost" href="{{ route('sales.index') }}">← Kembali</a></div>
<div class="card">
<p>Pembeli / Petugas: <b>{{ $sale->user->name }}</b> · {{ $sale->sold_at->format('d M Y H:i') }}
· <span class="badge {{ $sale->status=='cancelled' ? 'bad' : '' }}">{{ $sale->status=='cancelled' ? 'Dibatalkan' : 'Selesai' }}</span></p>
<div class="scroll"><table><tr><th>Produk</th><th>Jumlah</th><th class="right">Harga</th><th class="right">Subtotal</th></tr>
@foreach($sale->items as $i)<tr><td>{{ $i->product->name }}</td><td>{{ $i->quantity }}</td><td class="right">Rp {{ number_format($i->unit_price,0,',','.') }}</td><td class="right">Rp {{ number_format($i->subtotal,0,',','.') }}</td></tr>@endforeach
<tr><th colspan="3" class="right">Total</th><th class="right">Rp {{ number_format($sale->total,0,',','.') }}</th></tr></table></div>
@can('admin') @if($sale->status=='completed')
<form method="POST" action="{{ route('sales.cancel',$sale) }}" onsubmit="return confirm('Batalkan penjualan ini? Stok akan dikembalikan.')">@csrf<button class="btn red">Batalkan transaksi</button></form>
@endif @endcan
</div>
@endsection
