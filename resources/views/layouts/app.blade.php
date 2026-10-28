<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'Apotik') · My Apotik</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{--lilac:#B79CED;--soft:#F3ECFF;--deep:#7C5CBF;--ink:#2D2340;--pink:#FF9EDB;--mint:#A8F0D1;--danger:#E5586E;--bg:#FAF7FF;--line:#E6DBF8}
*{box-sizing:border-box}
body{margin:0;font-family:'Poppins',system-ui,sans-serif;background:var(--bg);color:var(--ink);display:flex;min-height:100vh}
aside{width:230px;background:linear-gradient(180deg,var(--lilac),var(--deep));color:#fff;padding:22px 16px;position:sticky;top:0;height:100vh;flex-shrink:0}
aside h1{font-size:20px;margin:0 0 24px}
aside a{display:block;color:#fff;text-decoration:none;padding:10px 14px;border-radius:14px;margin-bottom:4px;font-weight:500;font-size:14px}
aside a:hover,aside a.on{background:rgba(255,255,255,.22)}
aside .sec{font-size:11px;opacity:.7;margin:16px 14px 6px;letter-spacing:1px}
main{flex:1;padding:28px;min-width:0}
.top{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;gap:12px;flex-wrap:wrap}
.top h2{margin:0;font-size:24px}
.card{background:#fff;border:1px solid var(--line);border-radius:20px;padding:20px;box-shadow:0 6px 18px rgba(124,92,191,.08);margin-bottom:18px}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:18px}
.stat small{color:#7b6d94}.stat b{display:block;font-size:22px;margin-top:4px}
table{width:100%;border-collapse:collapse;font-size:14px}
th{text-align:left;color:var(--deep);font-size:12px;text-transform:uppercase;letter-spacing:.5px}
th,td{padding:10px 8px;border-bottom:1px solid var(--line)}
.scroll{overflow-x:auto}
.btn{display:inline-block;background:var(--deep);color:#fff;border:0;padding:9px 18px;border-radius:999px;font:600 13px 'Poppins';cursor:pointer;text-decoration:none}
.btn:hover{opacity:.88}.btn.ghost{background:var(--soft);color:var(--deep)}.btn.red{background:var(--danger)}.btn.sm{padding:5px 12px;font-size:12px}
input,select{width:100%;padding:10px 14px;border:1.5px solid var(--line);border-radius:14px;font:14px 'Poppins';background:#fff}
input:focus,select:focus{outline:2px solid var(--lilac)}
label{display:block;font-weight:600;font-size:13px;margin:12px 0 6px}
.filters{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px}.filters>*{flex:1;min-width:130px}.filters .btn{flex:0 0 auto}
.badge{padding:3px 12px;border-radius:999px;font-size:12px;font-weight:600;background:var(--mint);color:#1d6b4b}
.badge.bad{background:#FFD6DE;color:#a3243b}.badge.warn{background:#FFE9A8;color:#8a6200}
.flash{padding:12px 16px;border-radius:14px;margin-bottom:16px;font-size:14px}
.flash.ok{background:var(--mint);color:#1d6b4b}.flash.err{background:#FFD6DE;color:#a3243b}
.flash ul{margin:0;padding-left:18px}
.pager{display:flex;gap:6px;margin-top:14px;flex-wrap:wrap}
.pager a,.pager span{padding:6px 12px;border-radius:12px;background:var(--soft);color:var(--deep);text-decoration:none;font-size:13px}
.pager .active{background:var(--deep);color:#fff}
.row{display:flex;gap:8px;margin-bottom:8px}.row select{flex:3}.row input{flex:1}
.right{text-align:right}.actions{display:flex;gap:6px;justify-content:flex-end}
.narrow{max-width:560px}
@media(max-width:800px){body{flex-direction:column}aside{width:100%;height:auto;position:static;display:flex;flex-wrap:wrap;gap:4px}aside h1,aside .sec{width:100%;margin:6px}main{padding:16px}}
@media print{aside,.noprint{display:none!important}main{padding:0}body{display:block}}
</style>
</head>
<body>
<aside>
  <h1>My Apotik</h1>
  @can('manage-stock')
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'on' : '' }}">Dashboard</a>
  @endcan
  <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.index','products.show') ? 'on' : '' }}">Produk</a>
  @can('manage-stock')
    <a href="{{ route('products.low') }}" class="{{ request()->routeIs('products.low') ? 'on' : '' }}">Stok Rendah</a>
    <a href="{{ route('purchases.index') }}" class="{{ request()->routeIs('purchases.*') ? 'on' : '' }}">Pembelian</a>
  @endcan
  <a href="{{ route('sales.index') }}" class="{{ request()->routeIs('sales.*') ? 'on' : '' }}">{{ auth()->user()->role === 'pembeli' ? 'Belanja Saya' : 'Penjualan' }}</a>
  @can('admin')
    <div class="sec">ADMIN</div>
    <a href="{{ route('reports.sales') }}" class="{{ request()->routeIs('reports.*') ? 'on' : '' }}">Laporan Penjualan</a>
    <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'on' : '' }}">Kategori</a>
    <a href="{{ route('suppliers.index') }}" class="{{ request()->routeIs('suppliers.*') ? 'on' : '' }}">Pemasok</a>
    <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'on' : '' }}">Pengguna</a>
  @endcan
  <div class="sec">{{ auth()->user()->name }} ({{ auth()->user()->role }})</div>
  <form method="POST" action="{{ route('logout') }}" style="padding:0 14px">@csrf<button class="btn ghost sm">Keluar</button></form>
</aside>
<main>
  @if(session('success'))<div class="flash ok">{{ session('success') }}</div>@endif
  @if($errors->any())<div class="flash err"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
  @yield('content')
</main>
</body>
</html>
