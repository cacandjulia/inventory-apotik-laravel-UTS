<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Pembeli tidak punya dashboard internal
        if ($user->role === 'pembeli') {
            return redirect()->route('products.index');
        }

        // Staff hanya melihat transaksinya sendiri, Admin melihat semua
        $sales = Sale::where('status', 'completed'); // Admin & Staff melihat semua penjualan, termasuk dari pembeli
        $purchases = Purchase::where('status', 'completed')->when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id));

        return view('dashboard', [
            'totalProducts' => Product::count(),
            'lowStockCount' => Product::lowStock()->count(),
            'salesToday' => (clone $sales)->whereDate('sold_at', today())->sum('total'),
            'salesMonth' => (clone $sales)->whereYear('sold_at', now()->year)->whereMonth('sold_at', now()->month)->sum('total'),
            'salesCount' => (clone $sales)->count(),
            'purchaseCount' => (clone $purchases)->count(),
            'lowStock' => Product::with('category')->lowStock()->orderBy('stock')->limit(5)->get(),
        ]);
    }
}
