<!DOCTYPE html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Masuk - My Apotik</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box}body{margin:0;min-height:100vh;display:grid;place-items:center;font-family:'Poppins',sans-serif;background:linear-gradient(135deg,#E9DDFF,#B79CED 60%,#FF9EDB);padding:20px 0}
.box{background:#fff;padding:34px;border-radius:28px;width:min(400px,92vw);box-shadow:0 20px 50px rgba(80,50,140,.25)}
h1{margin:0 0 4px;color:#2D2340}p{margin:0 0 18px;color:#7b6d94;font-size:14px}
label{display:block;font-weight:600;font-size:13px;margin:12px 0 6px}
input[type=text],input[type=email],input[type=password]{width:100%;padding:12px 16px;border:1.5px solid #E6DBF8;border-radius:16px;font:14px 'Poppins'}
button{margin-top:20px;width:100%;padding:12px;border:0;border-radius:999px;background:#7C5CBF;color:#fff;font:600 15px 'Poppins';cursor:pointer}
.err{background:#FFD6DE;color:#a3243b;padding:10px 14px;border-radius:14px;font-size:13px;margin-bottom:8px}
.alt{text-align:center;margin:16px 0 0;font-size:13px}.alt a{color:#7C5CBF;font-weight:600;text-decoration:none}
</style></head><body>
<form class="box" method="POST" action="{{ route('login.store') }}">
  @csrf
  <h1>My Apotik</h1><p>Masuk untuk mengelola stok dan penjualan.</p>
  @foreach($errors->all() as $e)<div class="err">{{ $e }}</div>@endforeach
  <label>Email</label><input type="email" name="email" value="{{ old('email') }}" required autofocus>
  <label>Kata sandi</label><input type="password" name="password" required>
  <label style="font-weight:400"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
  <button>Masuk</button>
  <p class="alt">Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a></p>
</form></body></html>
