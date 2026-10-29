<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ===== AKUN DEMO (password otomatis di-hash oleh cast 'hashed' di model User) =====
        User::create(['name' => 'Admin Apotik', 'email' => 'myapotik@gmail.com', 'password' => 'cacabaikhati', 'role' => 'admin']);
        User::create(['name' => 'Staff Apotik', 'email' => 'staffapotik@gmail.com', 'password' => 'sukasukacaca', 'role' => 'staff']);

        $cat = collect(['Vitamin', 'P3K', 'Obat Herbal'])->mapWithKeys(fn ($n) => [$n => Category::create(['name' => $n])->id]);

        Supplier::create(['name' => 'PT Sehat Sentosa', 'phone' => '085296738802', 'email' => 'sehatsentosa@gmail.com']);
        Supplier::create(['name' => 'CV Herbal Nusantara', 'phone' => '081273409588', 'email' => 'orderHerbalN@gmail.com']);

        // [kategori, sku, nama, harga beli, harga jual, stok, stok minimum]
        $products = [
            ['Vitamin', 'VIT-001', 'Vitamin C 500 mg (30 tablet)', 22000, 30000, 50, 10],
            ['Vitamin', 'VIT-002', 'Vitamin D3 1000 IU (30 kapsul)', 45000, 60000, 30, 8],
            ['Vitamin', 'VIT-003', 'Multivitamin Remaja (60 tablet)', 55000, 75000, 4, 10],
            ['P3K', 'P3K-001', 'Plester Luka (isi 10)', 3000, 5000, 100, 20],
            ['P3K', 'P3K-002', 'Antiseptik Povidone Iodine 60 ml', 9000, 14000, 40, 10],
            ['P3K', 'P3K-003', 'Perban Gulung 5 cm', 4000, 7000, 8, 10],
            ['Obat Herbal', 'HRB-001', 'Jahe Merah Instan (10 sachet)', 12000, 18000, 35, 10],
            ['Obat Herbal', 'HRB-002', 'Kunyit Asam Sachet (10 sachet)', 10000, 16000, 25, 10],
            ['Obat Herbal', 'HRB-003', 'Temulawak Kapsul (60 kapsul)', 30000, 42000, 15, 5],
        ];
        foreach ($products as [$c, $sku, $name, $buy, $sell, $stock, $min]) {
            Product::create([
                'category_id' => $cat[$c], 'sku' => $sku, 'name' => $name,
                'purchase_price' => $buy, 'selling_price' => $sell, 'stock' => $stock, 'minimum_stock' => $min,
            ]);
        }

        $this->call(PembeliSeeder::class);
    }
}
