@extends('layouts.app')
@section('title','Pembelian Baru')
@section('content')
<div class="top"><h2>Pembelian Baru (Stok Masuk)</h2></div>
<form class="card narrow" method="POST" action="{{ route('purchases.store') }}">
@csrf
<label>Pemasok</label><select name="supplier_id" required><option value="">Pilih pemasok</option>
@foreach($suppliers as $s)<option value="{{ $s->id }}" @selected(old('supplier_id')==$s->id)>{{ $s->name }}</option>@endforeach</select>
<label>Item pembelian</label>
<div id="rows"></div>
<button type="button" id="add" class="btn ghost sm">+ Tambah item</button>
<template id="tpl"><div class="row" style="flex-wrap:wrap">
  <select name="items[__i__][product_id]" class="ps" required><option value="">Pilih produk</option>
  @foreach($products as $p)<option value="{{ $p->id }}" data-cost="{{ $p->purchase_price }}">{{ $p->name }} (stok {{ $p->stock }})</option>@endforeach</select>
  <input type="number" name="items[__i__][quantity]" min="1" value="1" title="Jumlah" required>
  <input type="number" step="0.01" min="0" name="items[__i__][unit_cost]" class="cost" placeholder="Harga beli" required>
  <button type="button" class="btn red sm rm">✕</button></div></template>
<p><button class="btn">Simpan pembelian</button> <a class="btn ghost" href="{{ route('purchases.index') }}">Batal</a></p>
</form>
<script>
let n=0;const rows=document.getElementById('rows'),tpl=document.getElementById('tpl').innerHTML;
function add(pid='',qty=1,cost=''){rows.insertAdjacentHTML('beforeend',tpl.replaceAll('__i__',n++));const r=rows.lastElementChild;
 r.querySelector('select').value=pid;r.querySelector('input[type=number]').value=qty;r.querySelector('.cost').value=cost;}
document.getElementById('add').onclick=()=>add();
rows.onclick=e=>{if(e.target.classList.contains('rm'))e.target.closest('.row').remove();};
rows.onchange=e=>{if(e.target.classList.contains('ps')){const o=e.target.selectedOptions[0];e.target.closest('.row').querySelector('.cost').value=o.dataset.cost||'';}};
@foreach(old('items',[['product_id'=>'','quantity'=>1,'unit_cost'=>'']]) as $o)add(@json($o['product_id'] ?? ''),@json($o['quantity'] ?? 1),@json($o['unit_cost'] ?? ''));@endforeach
</script>
@endsection
