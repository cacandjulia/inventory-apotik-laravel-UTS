<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Support\Msg;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['category_id' => ['nullable', 'integer']]);

        $products = Product::with('category')            // eager loading: hindari N+1
            ->search($request->q)
            ->when($request->category_id, fn ($q, $id) => $q->where('category_id', $id))
            ->latest()->paginate(10)->withQueryString();  // filter tetap terbawa saat pindah halaman

        return view('products.index', ['products' => $products, 'categories' => Category::orderBy('name')->get()]);
    }

    public function lowStock()
    {
        $products = Product::with('category')->lowStock()->orderBy('stock')->paginate(10);
        return view('products.low', compact('products'));
    }

    public function show(Product $product)
    {
        return view('products.show', ['product' => $product->load('category')]);
    }

    public function create()
    {
        return view('products.form', ['product' => new Product, 'categories' => Category::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules() + ['stock' => ['required', 'integer', 'min:0']], $this->messages(), $this->names());
        Product::create($data);
        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        return view('products.form', ['product' => $product, 'categories' => Category::orderBy('name')->get()]);
    }

    public function update(Request $request, Product $product)
    {
        // Stok tidak diubah lewat form: stok hanya berubah lewat transaksi
        $data = $request->validate($this->rules($product), $this->messages(), $this->names());
        $product->update($data);
        return redirect()->route('products.show', $product)->with('success', 'Produk berhasil diubah.');
    }

    public function destroy(Product $product)
    {
        if ($product->saleItems()->exists() || $product->purchaseItems()->exists()) {
            return back()->withErrors(['product' => 'Produk sudah dipakai di transaksi, tidak boleh dihapus.']);
        }
        $product->delete(); // soft delete
        return redirect()->route('products.index')->with('success', 'Produk dihapus.');
    }

    private function rules(?Product $product = null): array
    {
        return [
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku' . ($product ? ',' . $product->id : '')],
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'exists:categories,id'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0', 'gte:purchase_price'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
        ];
    }

    private function messages(): array
    {
        return Msg::id() + ['selling_price.gte' => 'Harga jual tidak boleh lebih rendah dari harga beli (bisa rugi).'];
    }

    private function names(): array
    {
        return ['sku' => 'SKU', 'name' => 'Nama produk', 'category_id' => 'Kategori', 'purchase_price' => 'Harga beli',
            'selling_price' => 'Harga jual', 'minimum_stock' => 'Stok minimum', 'stock' => 'Stok awal'];
    }
}
