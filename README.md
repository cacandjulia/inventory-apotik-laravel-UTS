# My Apotik — Inventory & Sales Management (Laravel)

## 1. Project overview
Aplikasi web Laravel + MySQL untuk mengelola kategori, produk, pemasok, pembelian (stok masuk),
penjualan (stok keluar), dashboard, dan hak akses Admin/Staff. Stok berubah otomatis dan konsisten
(memakai `DB::transaction`).

## 2. Requirements
PHP 8.2+, Composer, MySQL/MariaDB (XAMPP/Laragon boleh), Node.js **tidak wajib** (CSS ada di layout).

## 3. Installation
```bash
composer create-project laravel/laravel apotik-lilac
cd apotik-lilac
# salin folder app, database, resources, routes, tests dari zip ini ke dalam proyek (timpa jika diminta)
```
Hapus `resources/views/welcome.blade.php` jika mau (tidak dipakai).

## 4. Environment setup
Buat database kosong `apotik-laravel` di MySQL (phpMyAdmin), lalu edit `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=apotik_laravel
DB_USERNAME=root
DB_PASSWORD=
```
```bash
php artisan key:generate
```

## 5. Migration & seeding
```bash
php artisan migrate --seed
php artisan serve        # buka http://127.0.0.1:8000
```

## 6. Demo account
| Role  | Email               | Kata sandi     |
|-------|---------------------|----------------|
| Admin |myapotik@gmail.com   | cacabaikhati   |
| Staff |staffapotik@gmail.com | sukasukacaca   |
| Pembeli | acu@gmail.com  | pembeli12345 |

Cara akun ini masuk ke sistem: ditulis di `database/seeders/DatabaseSeeder.php` (baris `User::create`).
Kata sandi otomatis di-hash oleh cast `'password' => 'hashed'` di `app/Models/User.php`, jadi di database
tersimpan terenkripsi. Untuk mengganti: ubah teks di seeder lalu jalankan `php artisan migrate:fresh --seed`.
Untuk menambah akun baru: login sebagai Admin → menu **Pengguna** → **+ Tambah**.

## 7. Features
- Login/logout, password hashing, protected routes, CSRF
- Role: **Admin** (kelola kategori, produk, pemasok, pengguna, batalkan transaksi) dan **Staff** (lihat produk, buat pembelian & penjualan, lihat transaksi miliknya). Dijaga server-side lewat `Gate 'admin'` + middleware `can:admin`
- Pembelian & penjualan multi-item, stok otomatis, total dihitung server, stok tidak cukup ditolak, rollback atomik
- Pembatalan transaksi (Admin) memulihkan stok; produk bertransaksi tidak bisa dihapus (soft delete)
- Dashboard dari query agregat, daftar stok rendah, pencarian, filter kategori/tanggal/status, pagination

## 8. Testing
```bash
php artisan test
```
Mencakup: guest ditolak, password salah, SKU duplikat/harga negatif, pembelian & penjualan mengubah stok,
stok tidak cukup, rollback, pembatalan, staff dilarang akses admin, search & pagination.

## ERD (ringkas)
users 1—N sales · users 1—N purchases · categories 1—N products · suppliers 1—N purchases ·
purchases 1—N purchase_items N—1 products · sales 1—N sale_items N—1 products.
Foreign key ada di tabel "banyak" (detail) karena satu baris detail hanya menunjuk satu induk.

## Known limitations
Tidak ada payment gateway, multi-gudang, atau laporan PDF 

## Role Pembeli & Laporan
- **Pembeli**: melihat produk, belanja sendiri (penjualan), dan melihat riwayat belanjanya. Tidak bisa membuka pembelian stok, stok rendah, atau menu admin.
- **Laporan Penjualan** (Admin): total, jumlah transaksi, item terjual, per hari, dan per produk dengan filter tanggal. Transaksi dibatalkan tidak dihitung.
- ERD ada di `docs/ERD.md` (Mermaid).
