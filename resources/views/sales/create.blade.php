@extends('layouts.app')
@section('title','Penjualan Baru')
@section('content')
<div class="top"><h2>Penjualan Baru</h2></div>
<form class="card narrow" method="POST" action="{{ route('sales.store') }}">
@csrf
<label>Item penjualan</label>
<div id="rows"></div>
<button type="button" id="add" class="btn ghost sm">+ Tambah item</button>
<template id="tpl"><div class="row">
  <select name="items[__i__][product_id]" required><option value="">Pilih produk</option>
  @foreach($products as $p)<option value="{{ $p->id }}" data-stock="{{ $p->stock }}">{{ $p->name }} (stok {{ $p->stock }}) – Rp {{ number_format($p->selling_price,0,',','.') }}</option>@endforeach</select>
  <input type="number" name="items[__i__][quantity]" min="1" value="1" required>
  <button type="button" class="btn red sm rm">✕</button></div></template>
<p style="font-size:12px;color:#7b6d94">Harga & total dihitung otomatis oleh server dari data produk.</p>
<p><button class="btn">Simpan penjualan</button> <a class="btn ghost" href="{{ route('sales.index') }}">Batal</a></p>
</form>
<script>
let n=0;const rows=document.getElementById('rows'),tpl=document.getElementById('tpl').innerHTML;
function add(pid='',qty=1){rows.insertAdjacentHTML('beforeend',tpl.replaceAll('__i__',n++));const r=rows.lastElementChild;r.querySelector('select').value=pid;r.querySelector('input').value=qty;r.querySelector('select').dispatchEvent(new Event('change',{bubbles:true}));}
document.getElementById('add').onclick=()=>add();
rows.onclick=e=>{if(e.target.classList.contains('rm'))e.target.closest('.row').remove();};
// Batasi jumlah maksimal sesuai stok (server tetap yang memutuskan)
rows.onchange=e=>{if(e.target.tagName==='SELECT'){const o=e.target.selectedOptions[0],q=e.target.closest('.row').querySelector('input');q.max=o.dataset.stock||'';if(q.max&&+q.value>+q.max)q.value=q.max;}};
@foreach(old('items',[['product_id'=>'','quantity'=>1]]) as $o)add(@json($o['product_id'] ?? ''),@json($o['quantity'] ?? 1));@endforeach
</script>
@endsection
