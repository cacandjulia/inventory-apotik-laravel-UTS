<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Support\Msg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['from' => ['nullable', 'date'], 'to' => ['nullable', 'date'], 'status' => ['nullable', Rule::in(['completed', 'cancelled'])]]);

        $purchases = Purchase::with(['supplier', 'user'])
            ->when(! auth()->user()->isAdmin(), fn ($q) => $q->where('user_id', auth()->id()))
            ->when($request->q, fn ($q, $t) => $q->where('purchase_number', 'like', "%{$t}%"))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->from, fn ($q, $d) => $q->whereDate('purchased_at', '>=', $d))
            ->when($request->to, fn ($q, $d) => $q->whereDate('purchased_at', '<=', $d))
            ->latest('purchased_at')->paginate(10)->withQueryString();

        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        return view('purchases.create', ['suppliers' => Supplier::orderBy('name')->get(), 'products' => Product::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', Rule::exists('products', 'id')->whereNull('deleted_at')],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
        ], Msg::id(), ['supplier_id' => 'Pemasok', 'items.*.product_id' => 'Produk', 'items.*.quantity' => 'Jumlah', 'items.*.unit_cost' => 'Harga beli']);

        // Semua langkah di bawah dalam SATU transaksi database: gagal di tengah = rollback semua
        $purchase = DB::transaction(function () use ($data) {
            $purchase = Purchase::create([
                'supplier_id' => $data['supplier_id'],
                'user_id' => auth()->id(),
                'purchase_number' => 'PB-' . now()->format('YmdHis') . random_int(100, 999),
                'total' => 0,
                'status' => 'completed',
                'purchased_at' => now(),
            ]);

            $total = 0;
            foreach ($data['items'] as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                $qty = (int) $item['quantity'];
                $subtotal = $qty * $item['unit_cost'];   // subtotal dihitung server

                $purchase->items()->create([
                    'product_id' => $product->id, 'quantity' => $qty,
                    'unit_cost' => $item['unit_cost'], 'subtotal' => $subtotal,
                ]);
                $product->increment('stock', $qty);      // stok bertambah
                $total += $subtotal;
            }
            $purchase->update(['total' => $total]);
            return $purchase;
        });

        return redirect()->route('purchases.show', $purchase)->with('success', 'Pembelian berhasil, stok sudah bertambah.');
    }

    public function show(Purchase $purchase)
    {
        $this->authorizeView($purchase);
        return view('purchases.show', ['purchase' => $purchase->load(['supplier', 'user', 'items.product'])]);
    }

    // Hanya admin (dijaga middleware can:admin di route)
    public function cancel(Purchase $purchase)
    {
        DB::transaction(function () use ($purchase) {
            $purchase = Purchase::lockForUpdate()->findOrFail($purchase->id);
            if ($purchase->status !== 'completed') {
                throw ValidationException::withMessages(['cancel' => 'Transaksi ini sudah dibatalkan.']);
            }
            foreach ($purchase->items as $item) {
                $product = Product::withTrashed()->lockForUpdate()->find($item->product_id);
                if ($product->stock < $item->quantity) {
                    throw ValidationException::withMessages(['cancel' => "Tidak bisa dibatalkan: stok {$product->name} sudah terpakai."]);
                }
                $product->decrement('stock', $item->quantity); // pulihkan stok
            }
            $purchase->update(['status' => 'cancelled']);
        });

        return back()->with('success', 'Pembelian dibatalkan, stok dikembalikan.');
    }

    private function authorizeView(Purchase $purchase): void
    {
        abort_unless(auth()->user()->isAdmin() || $purchase->user_id === auth()->id(), 403);
    }
}
