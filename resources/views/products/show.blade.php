@extends('layouts.app')
@section('title',$product->name)
@section('content')
<div class="top"><h2>{{ $product->name }}</h2><a class="btn ghost" href="{{ route('products.index') }}">← Kembali</a></div>
<div class="card narrow"><table>
<tr><th>SKU</th><td>{{ $product->sku }}</td></tr>
<tr><th>Kategori</th><td>{{ $product->category->name }}</td></tr>
@can('manage-stock')<tr><th>Harga beli</th><td>Rp {{ number_format($product->purchase_price,0,',','.') }}</td></tr>@endcan
<tr><th>Harga jual</th><td>Rp {{ number_format($product->selling_price,0,',','.') }}</td></tr>
<tr><th>Stok</th><td><span class="badge {{ $product->isLowStock() ? 'bad' : '' }}">{{ $product->stock }}</span> (minimum {{ $product->minimum_stock }})</td></tr>
</table>@can('admin')<p><a class="btn" href="{{ route('products.edit',$product) }}">Ubah produk</a></p>@endcan</div>
@endsection
