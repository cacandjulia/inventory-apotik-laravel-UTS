@extends('layouts.app')
@section('title','Form Produk')
@section('content')
<div class="top"><h2>{{ $product->exists ? 'Ubah' : 'Tambah' }} Produk</h2></div>
<form class="card narrow" method="POST" action="{{ $product->exists ? route('products.update',$product) : route('products.store') }}">
@csrf @if($product->exists) @method('PUT') @endif
<label>SKU</label><input name="sku" value="{{ old('sku',$product->sku) }}" required>
<label>Nama produk</label><input name="name" value="{{ old('name',$product->name) }}" required>
<label>Kategori</label><select name="category_id" required><option value="">Pilih kategori</option>
@foreach($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id',$product->category_id)==$c->id)>{{ $c->name }}</option>@endforeach</select>
<label>Harga beli (Rp)</label><input type="number" step="0.01" min="0" name="purchase_price" value="{{ old('purchase_price',$product->purchase_price) }}" required>
<label>Harga jual (Rp) - tidak boleh di bawah harga beli</label><input type="number" step="0.01" min="0" name="selling_price" value="{{ old('selling_price',$product->selling_price) }}" required>
<label>Stok minimum</label><input type="number" min="0" name="minimum_stock" value="{{ old('minimum_stock',$product->minimum_stock ?? 0) }}" required>
@if($product->exists)<p style="font-size:13px;color:#7b6d94">Stok saat ini: <b>{{ $product->stock }}</b> (berubah otomatis lewat transaksi pembelian/penjualan)</p>
@else<label>Stok awal</label><input type="number" min="0" name="stock" value="{{ old('stock',0) }}" required>@endif
<p><button class="btn">Simpan</button> <a class="btn ghost" href="{{ route('products.index') }}">Batal</a></p>
</form>
@endsection
