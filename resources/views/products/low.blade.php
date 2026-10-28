@extends('layouts.app')
@section('title','Stok Rendah')
@section('content')
<div class="top"><h2>Stok Rendah</h2></div>
<div class="card scroll"><table><tr><th>SKU</th><th>Produk</th><th>Kategori</th><th>Stok</th><th>Minimum</th></tr>
@forelse($products as $p)
<tr><td>{{ $p->sku }}</td><td><a href="{{ route('products.show',$p) }}">{{ $p->name }}</a></td><td>{{ $p->category->name }}</td><td><span class="badge bad">{{ $p->stock }}</span></td><td>{{ $p->minimum_stock }}</td></tr>
@empty <tr><td colspan="5">Semua stok aman </td></tr>@endforelse</table>{{ $products->links() }}</div>
@endsection
