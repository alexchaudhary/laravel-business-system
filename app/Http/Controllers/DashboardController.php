<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;

class DashboardController extends Controller
{
    public function index()
    {
        $totalCustomers = Customer::count();

        $totalSuppliers = Supplier::count();

        $totalProducts = Product::count();

        $totalStockUnits = Product::sum('stock_quantity');

        $totalStockValue = Product::get()->sum(function ($product) {
            return (float) $product->stock_quantity
                * (float) $product->purchase_price;
        });

        $lowStockProducts = Product::whereColumn(
            'stock_quantity',
            '<=',
            'low_stock_threshold'
        )
            ->where('stock_quantity', '>', 0)
            ->orderBy('stock_quantity')
            ->get();

        $totalSales = Sale::count();

        $totalPurchases = Purchase::count();

        $recentSales = Sale::with('customer')
            ->latest()
            ->take(5)
            ->get();

        $recentPurchases = Purchase::with('supplier')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalCustomers',
            'totalSuppliers',
            'totalProducts',
            'totalStockUnits',
            'totalStockValue',
            'lowStockProducts',
            'totalSales',
            'totalPurchases',
            'recentSales',
            'recentPurchases'
        ));
    }
}