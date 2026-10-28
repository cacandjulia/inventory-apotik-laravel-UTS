<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Support\Msg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['from' => ['nullable', 'date'], 'to' => ['nullable', 'date'], 'status' => ['nullable', Rule::in(['completed', 'cancelled'])]]);

        $sales = Sale::with('user')
            ->when(auth()->user()->cannot('manage-stock'), fn ($q) => $q->where('user_id', auth()->id())) // hanya pembeli yang dibatasi ke miliknya
            ->when($request->q, fn ($q, $t) => $q->where('sale_number', 'like', "%{$t}%"))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->from, fn ($q, $d) => $q->whereDate('sold_at', '>=', $d))
            ->when($request->to, fn ($q, $d) => $q->whereDate('sold_at', '<=', $d))
            ->latest('sold_at')->paginate(10)->withQueryString();

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        return view('sales.create', ['products' => Product::where('stock', '>', 0)->orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', Rule::exists('products', 'id')->whereNull('deleted_at')],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ], Msg::id(), ['items.*.product_id' => 'Produk', 'items.*.quantity' => 'Jumlah']);

        $sale = DB::transaction(function () use ($data) {
            $sale = Sale::create([
                'user_id' => auth()->id(),
                'sale_number' => 'PJ-' . now()->format('YmdHis') . random_int(100, 999),
                'total' => 0,
                'status' => 'completed',
                'sold_at' => now(),
            ]);

            $total = 0;
            foreach ($data['items'] as $item) {
                // lockForUpdate: cegah dua penjualan bersamaan menembus stok (overselling)
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                $qty = (int) $item['quantity'];

                if ($product->stock < $qty) {
                    // Exception ini membuat DB::transaction rollback SEMUA perubahan
                    throw ValidationException::withMessages([
                        'items' => "Stok {$product->name} tidak cukup (tersedia {$product->stock}, diminta {$qty}).",
                    ]);
                }

                $subtotal = $product->selling_price * $qty;   // harga dari database, bukan browser
                $sale->items()->create([
                    'product_id' => $product->id, 'quantity' => $qty,
                    'unit_price' => $product->selling_price, 'subtotal' => $subtotal,
                ]);
                $product->decrement('stock', $qty);
                $total += $subtotal;
            }
            $sale->update(['total' => $total]);
            return $sale;
        });

        return redirect()->route('sales.show', $sale)->with('success', 'Penjualan berhasil disimpan.');
    }

    public function show(Sale $sale)
    {
        abort_unless(auth()->user()->can('manage-stock') || $sale->user_id === auth()->id(), 403);
        return view('sales.show', ['sale' => $sale->load(['user', 'items.product'])]);
    }

    // Hanya admin (middleware can:admin)
    public function cancel(Sale $sale)
    {
        DB::transaction(function () use ($sale) {
            $sale = Sale::lockForUpdate()->findOrFail($sale->id);
            if ($sale->status !== 'completed') {
                throw ValidationException::withMessages(['cancel' => 'Transaksi ini sudah dibatalkan.']);
            }
            foreach ($sale->items as $item) {
                Product::withTrashed()->lockForUpdate()->find($item->product_id)->increment('stock', $item->quantity);
            }
            $sale->update(['status' => 'cancelled']);
        });

        return back()->with('success', 'Penjualan dibatalkan, stok dikembalikan.');
    }
}
