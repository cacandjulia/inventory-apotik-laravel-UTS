<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PembeliTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        return User::create(['name' => ucfirst($role), 'email' => "$role@t.test", 'password' => 'rahasia123', 'role' => $role]);
    }

    private function product(int $stock): Product
    {
        $cat = Category::firstOrCreate(['name' => 'Vitamin']);
        return Product::create(['category_id' => $cat->id, 'sku' => 'VIT-9', 'name' => 'Vitamin Uji',
            'purchase_price' => 1000, 'selling_price' => 2000, 'stock' => $stock, 'minimum_stock' => 1]);
    }

    public function test_pembeli_purchase_appears_in_admin_report(): void
    {
        $p = $this->product(10);

        $this->actingAs($this->user('pembeli'))
            ->post('/sales', ['items' => [['product_id' => $p->id, 'quantity' => 2]]])->assertRedirect();

        $this->assertSame(8, $p->fresh()->stock);

        $this->actingAs($this->user('admin'))->get('/reports/sales')
            ->assertOk()->assertSee('Vitamin Uji');
    }

    public function test_sold_out_product_is_rejected(): void
    {
        $p = $this->product(0);

        $this->actingAs($this->user('pembeli'))
            ->post('/sales', ['items' => [['product_id' => $p->id, 'quantity' => 1]]])
            ->assertSessionHasErrors('items');

        $this->assertSame(0, Sale::count());
        $this->assertSame(0, $p->fresh()->stock);
    }

    public function test_pembeli_and_staff_cannot_open_report_or_pembeli_cannot_open_purchases(): void
    {
        $this->actingAs($this->user('pembeli'));
        $this->get('/purchases')->assertForbidden();
        $this->get('/reports/sales')->assertForbidden();
        $this->get('/categories')->assertForbidden();

        $this->actingAs($this->user('staff'))->get('/reports/sales')->assertForbidden();
    }
}
