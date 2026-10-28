<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $request->validate(['from' => ['nullable', 'date'], 'to' => ['nullable', 'date']]);

        $from = $request->from ?: now()->startOfMonth()->toDateString();
        $to = $request->to ?: now()->toDateString();

        // Hanya transaksi berstatus "completed" (yang dibatalkan tidak dihitung)
        $sales = Sale::where('status', 'completed')
            ->whereDate('sold_at', '>=', $from)->whereDate('sold_at', '<=', $to);

        $perDay = (clone $sales)
            ->selectRaw('DATE(sold_at) as day, COUNT(*) as trx, SUM(total) as revenue')
            ->groupBy('day')->orderBy('day')->get();

        $perProduct = SaleItem::query()
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->where('sales.status', 'completed')
            ->whereDate('sales.sold_at', '>=', $from)->whereDate('sales.sold_at', '<=', $to)
            ->selectRaw('products.sku, products.name, SUM(sale_items.quantity) as qty, SUM(sale_items.subtotal) as revenue')
            ->groupBy('products.id', 'products.sku', 'products.name')
            ->orderByDesc('qty')->get();

        return view('reports.sales', [
            'from' => $from, 'to' => $to,
            'revenue' => (clone $sales)->sum('total'),
            'count' => (clone $sales)->count(),
            'itemsSold' => $perProduct->sum('qty'),
            'perDay' => $perDay,
            'perProduct' => $perProduct,
        ]);
    }
}
