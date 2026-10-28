<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterAndRulesTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role, ?string $name = null): User
    {
        return User::create(['name' => $name ?? ucfirst($role), 'email' => "$role@t.test", 'password' => 'rahasia123', 'role' => $role]);
    }

    private function product(): Product
    {
        $cat = Category::firstOrCreate(['name' => 'Vitamin']);
        return Product::create(['category_id' => $cat->id, 'sku' => 'V-1', 'name' => 'Vit Uji',
            'purchase_price' => 1000, 'selling_price' => 2000, 'stock' => 10, 'minimum_stock' => 1]);
    }

    public function test_visitor_can_register_and_is_always_pembeli(): void
    {
        $this->post('/register', [
            'name' => 'Budi', 'email' => 'budi@t.test',
            'password' => 'passwordku1', 'password_confirmation' => 'passwordku1',
            'role' => 'admin', // percobaan curang: harus diabaikan
        ])->assertRedirect(route('products.index'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'budi@t.test', 'role' => 'pembeli']);
    }

    public function test_register_validation(): void
    {
        $this->user('staff');

        $this->post('/register', ['name' => 'X', 'email' => 'staff@t.test', 'password' => 'pendek', 'password_confirmation' => 'beda'])
            ->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
    }

    public function test_staff_can_see_sales_made_by_pembeli(): void
    {
        $p = $this->product();

        $this->actingAs($this->user('pembeli', 'Budi Pembeli'))
            ->post('/sales', ['items' => [['product_id' => $p->id, 'quantity' => 1]]]);
        $sale = Sale::first();

        $this->actingAs($this->user('staff'))->get('/sales')->assertSee('Budi Pembeli');
        $this->get("/sales/{$sale->id}")->assertOk();
    }

    public function test_pembeli_cannot_see_other_buyers_sale(): void
    {
        $p = $this->product();
        $a = User::create(['name' => 'A', 'email' => 'a@t.test', 'password' => 'rahasia123', 'role' => 'pembeli']);
        $b = User::create(['name' => 'B', 'email' => 'b@t.test', 'password' => 'rahasia123', 'role' => 'pembeli']);

        $this->actingAs($a)->post('/sales', ['items' => [['product_id' => $p->id, 'quantity' => 1]]]);
        $sale = Sale::first();

        $this->actingAs($b)->get("/sales/{$sale->id}")->assertForbidden();
    }

    public function test_selling_price_below_purchase_price_is_rejected(): void
    {
        $cat = Category::create(['name' => 'Vitamin']);
        $base = ['category_id' => $cat->id, 'name' => 'Obat', 'minimum_stock' => 1, 'stock' => 1];

        $this->actingAs($this->user('admin'));
        $this->post('/products', $base + ['sku' => 'R-1', 'purchase_price' => 5000, 'selling_price' => 3000])
            ->assertSessionHasErrors('selling_price');
        $this->assertDatabaseMissing('products', ['sku' => 'R-1']);

        $this->post('/products', $base + ['sku' => 'R-2', 'purchase_price' => 3000, 'selling_price' => 3000])
            ->assertSessionDoesntHaveErrors();
        $this->assertDatabaseHas('products', ['sku' => 'R-2']);
    }
}
