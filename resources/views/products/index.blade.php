@extends('layouts.app')
@section('title','Produk')
@section('content')
<div class="top"><h2>Produk</h2>@can('admin')<a class="btn" href="{{ route('products.create') }}">+ Tambah</a>@endcan</div>
<form class="filters" method="GET">
  <input name="q" value="{{ request('q') }}" placeholder="Cari SKU / nama produk...">
  <select name="category_id"><option value="">Semua kategori</option>
    @foreach($categories as $c)<option value="{{ $c->id }}" @selected(request('category_id')==$c->id)>{{ $c->name }}</option>@endforeach</select>
  <button class="btn">Cari</button>
</form>
<div class="card scroll"><table><tr><th>SKU</th><th>Produk</th><th>Kategori</th><th class="right">Harga jual</th><th>Stok</th><th></th></tr>
@forelse($products as $p)
<tr><td>{{ $p->sku }}</td><td><a href="{{ route('products.show',$p) }}">{{ $p->name }}</a></td><td>{{ $p->category->name }}</td>
<td class="right">Rp {{ number_format($p->selling_price,0,',','.') }}</td>
<td><span class="badge {{ $p->isLowStock() ? 'bad' : '' }}">{{ $p->stock }}</span></td>
<td class="actions">@can('admin')<a class="btn ghost sm" href="{{ route('products.edit',$p) }}">Ubah</a>
<form method="POST" action="{{ route('products.destroy',$p) }}" onsubmit="return confirm('Hapus produk ini?')">@csrf @method('DELETE')<button class="btn red sm">Hapus</button></form>@endcan</td></tr>
@empty <tr><td colspan="6">Produk tidak ditemukan.</td></tr>@endforelse</table>{{ $products->links() }}</div>
@endsection
