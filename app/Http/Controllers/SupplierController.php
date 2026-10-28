<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Support\Msg;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::query()
            ->when($request->q, fn ($q, $term) => $q->where('name', 'like', "%{$term}%"))
            ->orderBy('name')->paginate(10)->withQueryString();

        return view('suppliers.index', compact('suppliers'));
    }

    public function create() { return view('suppliers.form', ['supplier' => new Supplier]); }

    public function store(Request $request)
    {
        Supplier::create($this->validated($request));
        return redirect()->route('suppliers.index')->with('success', 'Pemasok berhasil ditambahkan.');
    }

    public function edit(Supplier $supplier) { return view('suppliers.form', compact('supplier')); }

    public function update(Request $request, Supplier $supplier)
    {
        $supplier->update($this->validated($request));
        return redirect()->route('suppliers.index')->with('success', 'Pemasok berhasil diubah.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->purchases()->exists()) {
            return back()->withErrors(['supplier' => 'Pemasok sudah punya transaksi, tidak bisa dihapus.']);
        }
        $supplier->delete();
        return back()->with('success', 'Pemasok dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
        ], Msg::id(), ['name' => 'Nama pemasok', 'phone' => 'Telepon', 'email' => 'Email']);
    }
}
