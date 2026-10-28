<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom role ke tabel users bawaan Laravel (admin / staff)
        Schema::table('users', function (Blueprint $t) {
            $t->string('role')->default('staff')->after('password');
        });

        Schema::create('categories', function (Blueprint $t) {
            $t->id();
            $t->string('name')->unique();
            $t->timestamps();
        });

        Schema::create('suppliers', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('phone')->nullable();
            $t->string('email')->nullable();
            $t->timestamps();
        });

        Schema::create('products', function (Blueprint $t) {
            $t->id();
            $t->foreignId('category_id')->constrained()->restrictOnDelete();
            $t->string('sku', 50)->unique();          // SKU unik juga dijaga di database
            $t->string('name', 150)->index();
            $t->decimal('purchase_price', 12, 2);
            $t->decimal('selling_price', 12, 2);
            $t->unsignedInteger('stock')->default(0);  // unsigned: stok tidak bisa negatif
            $t->unsignedInteger('minimum_stock')->default(0);
            $t->softDeletes();                         // produk bertransaksi tidak di-hard delete
            $t->timestamps();
        });

        Schema::create('purchases', function (Blueprint $t) {
            $t->id();
            $t->foreignId('supplier_id')->constrained()->restrictOnDelete();
            $t->foreignId('user_id')->constrained()->restrictOnDelete();
            $t->string('purchase_number')->unique();
            $t->decimal('total', 14, 2)->default(0);
            $t->string('status')->default('completed')->index();
            $t->timestamp('purchased_at');
            $t->timestamps();
        });

        Schema::create('purchase_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_id')->constrained()->restrictOnDelete();
            $t->unsignedInteger('quantity');
            $t->decimal('unit_cost', 12, 2);
            $t->decimal('subtotal', 14, 2);
            $t->timestamps();
        });

        Schema::create('sales', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->restrictOnDelete();
            $t->string('sale_number')->unique();
            $t->decimal('total', 14, 2)->default(0);
            $t->string('status')->default('completed')->index();
            $t->timestamp('sold_at');
            $t->timestamps();
        });

        Schema::create('sale_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_id')->constrained()->restrictOnDelete();
            $t->unsignedInteger('quantity');
            $t->decimal('unit_price', 12, 2);
            $t->decimal('subtotal', 14, 2);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('products');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('categories');
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn('role'));
    }
};
