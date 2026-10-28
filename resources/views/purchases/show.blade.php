@extends('layouts.app')
@section('title','Detail Pembelian')
@section('content')
<div class="top"><h2>{{ $purchase->purchase_number }}</h2><a class="btn ghost" href="{{ route('purchases.index') }}">← Kembali</a></div>
<div class="card">
<p>Pemasok: <b>{{ $purchase->supplier->name }}</b> · Petugas: <b>{{ $purchase->user->name }}</b> · {{ $purchase->purchased_at->format('d M Y H:i') }}
· <span class="badge {{ $purchase->status=='cancelled' ? 'bad' : '' }}">{{ $purchase->status=='cancelled' ? 'Dibatalkan' : 'Selesai' }}</span></p>
<div class="scroll"><table><tr><th>Produk</th><th>Jumlah</th><th class="right">Harga beli</th><th class="right">Subtotal</th></tr>
@foreach($purchase->items as $i)<tr><td>{{ $i->product->name }}</td><td>{{ $i->quantity }}</td><td class="right">Rp {{ number_format($i->unit_cost,0,',','.') }}</td><td class="right">Rp {{ number_format($i->subtotal,0,',','.') }}</td></tr>@endforeach
<tr><th colspan="3" class="right">Total</th><th class="right">Rp {{ number_format($purchase->total,0,',','.') }}</th></tr></table></div>
@can('admin') @if($purchase->status=='completed')
<form method="POST" action="{{ route('purchases.cancel',$purchase) }}" onsubmit="return confirm('Batalkan pembelian ini? Stok akan dikurangi kembali.')">@csrf<button class="btn red">Batalkan transaksi</button></form>
@endif @endcan
</div>
@endsection
