<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApotikTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User { return User::create(['name' => 'A', 'email' => 'a@t.test', 'password' => 'rahasia123', 'role' => 'admin']); }
    private function staff(): User { return User::create(['name' => 'S', 'email' => 's@t.test', 'password' => 'rahasia123', 'role' => 'staff']); }

    private function product(string $sku = 'VIT-1', int $stock = 10, string $name = 'Vitamin C'): Product
    {
        $cat = Category::firstOrCreate(['name' => 'Vitamin']);
        return Product::create(['category_id' => $cat->id, 'sku' => $sku, 'name' => $name,
            'purchase_price' => 1000, 'selling_price' => 2000, 'stock' => $stock, 'minimum_stock' => 2]);
    }

    // ----- Auth -----
    public function test_guest_cannot_open_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_wrong_password_is_rejected(): void
    {
        $u = $this->admin();
        $this->post('/login', ['email' => $u->email, 'password' => 'salah'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    // ----- Product -----
    public function test_duplicate_sku_and_negative_price_rejected(): void
    {
        $this->product('DUP-1');
        $cat = Category::first();
        $base = ['category_id' => $cat->id, 'name' => 'X', 'purchase_price' => 1, 'selling_price' => 2, 'minimum_stock' => 1, 'stock' => 1];

        $this->actingAs($this->admin())->post('/products', $base + ['sku' => 'DUP-1'])->assertSessionHasErrors('sku');
        $this->post('/products', array_merge($base, ['sku' => 'NEW-1', 'selling_price' => -5]))->assertSessionHasErrors('selling_price');
    }

    // ----- Purchase / Sale -----
    public function test_purchase_adds_stock(): void
    {
        $p = $this->product(stock: 10);
        $sup = Supplier::create(['name' => 'Sup']);

        $this->actingAs($this->staff())->post('/purchases', [
            'supplier_id' => $sup->id,
            'items' => [['product_id' => $p->id, 'quantity' => 5, 'unit_cost' => 1000]],
        ])->assertRedirect();

        $this->assertSame(15, $p->fresh()->stock);
    }

    public function test_sale_reduces_stock_and_total_is_computed_by_server(): void
    {
        $p = $this->product(stock: 10);

        $this->actingAs($this->staff())->post('/sales', [
            'items' => [['product_id' => $p->id, 'quantity' => 3]],
            'total' => 1, // input palsu dari browser harus diabaikan
        ])->assertRedirect();

        $this->assertSame(7, $p->fresh()->stock);
        $this->assertEquals(6000, Sale::first()->total);
    }

    public function test_insufficient_stock_is_rejected_without_changes(): void
    {
        $p = $this->product(stock: 4);

        $this->actingAs($this->staff())->post('/sales', ['items' => [['product_id' => $p->id, 'quantity' => 6]]])
            ->assertSessionHasErrors('items');

        $this->assertSame(4, $p->fresh()->stock);
        $this->assertSame(0, Sale::count());
    }

    public function test_one_failing_item_rolls_back_the_whole_sale(): void
    {
        $ok = $this->product('A-1', 10, 'Obat A');
        $low = $this->product('B-1', 1, 'Obat B');

        $this->actingAs($this->staff())->post('/sales', ['items' => [
            ['product_id' => $ok->id, 'quantity' => 2],
            ['product_id' => $low->id, 'quantity' => 5],
        ]])->assertSessionHasErrors('items');

        $this->assertSame(10, $ok->fresh()->stock); // item pertama ikut di-rollback
        $this->assertSame(0, Sale::count());
    }

    public function test_cancel_sale_restores_stock(): void
    {
        $p = $this->product(stock: 10);
        $this->actingAs($this->staff())->post('/sales', ['items' => [['product_id' => $p->id, 'quantity' => 4]]]);
        $sale = Sale::first();

        $this->actingAs($this->admin())->post("/sales/{$sale->id}/cancel");

        $this->assertSame(10, $p->fresh()->stock);
        $this->assertSame('cancelled', $sale->fresh()->status);
    }

    // ----- Authorization -----
    public function test_staff_cannot_access_admin_functions(): void
    {
        $this->actingAs($this->staff());
        $this->get('/categories')->assertForbidden();
        $this->get('/users')->assertForbidden();
        $this->get('/products/create')->assertForbidden();
        $this->post('/categories', ['name' => 'Hack'])->assertForbidden();
    }

    // ----- Search & pagination -----
    public function test_search_and_pagination(): void
    {
        foreach (range(1, 12) as $i) $this->product("SKU-$i", 10, "Produk $i");
        $this->product('HRB-9', 10, 'Jahe Merah Unik');

        $this->actingAs($this->staff());
        $this->get('/products?q=Jahe')->assertSee('Jahe Merah Unik')->assertDontSee('Produk 1');
        $this->get('/products')->assertSee('page=2'); // ada 13 produk, 10 per halaman
    }
}
