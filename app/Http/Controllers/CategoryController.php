<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Support\Msg;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return view('categories.index', ['categories' => Category::withCount('products')->orderBy('name')->paginate(10)]);
    }

    public function create() { return view('categories.form', ['category' => new Category]); }

    public function store(Request $request)
    {
        Category::create($this->validated($request));
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category) { return view('categories.form', compact('category')); }

    public function update(Request $request, Category $category)
    {
        $category->update($this->validated($request, $category));
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diubah.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->withErrors(['category' => 'Kategori masih dipakai produk, tidak bisa dihapus.']);
        }
        $category->delete();
        return back()->with('success', 'Kategori dihapus.');
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name' . ($category ? ',' . $category->id : '')],
        ], Msg::id(), ['name' => 'Nama kategori']);
    }
}
