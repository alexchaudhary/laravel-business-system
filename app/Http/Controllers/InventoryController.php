<?php

namespace App\Http\Controllers;

use App\Models\Product;

class InventoryController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('name')->get();

        // Total number of products
        $totalProducts = $products->count();

        // Total stock units
        $totalStockUnits = $products->sum('stock_quantity');

        // Products with low stock
        // Each product uses its own low_stock_threshold
        $lowStockProducts = $products->filter(function ($product) {
            return $product->stock_quantity > 0
                && $product->stock_quantity <= $product->low_stock_threshold;
        });

        // Total stock value based on purchase price
        $totalStockValue = $products->sum(function ($product) {
            return (float) $product->stock_quantity
                * (float) $product->purchase_price;
        });

        return view('inventory.index', compact(
            'products',
            'totalProducts',
            'totalStockUnits',
            'lowStockProducts',
            'totalStockValue'
        ));
    }
}