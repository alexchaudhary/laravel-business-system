<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function index()
    {
        $adjustments = StockAdjustment::with('product')
            ->latest()
            ->get();

        return view('stock-adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();

        return view('stock-adjustments.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:in,out',
            'quantity' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::lockForUpdate()
                ->findOrFail($validated['product_id']);

            $stockBefore = (float) $product->stock_quantity;
            $quantity = (float) $validated['quantity'];

            if ($validated['type'] === 'in') {
                $stockAfter = $stockBefore + $quantity;
            } else {
                $stockAfter = $stockBefore - $quantity;

                if ($stockAfter < 0) {
                    abort(422, 'Insufficient stock.');
                }
            }

            StockAdjustment::create([
                'product_id' => $product->id,
                'type' => $validated['type'],
                'quantity' => $quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'note' => $validated['note'] ?? null,
            ]);

            $product->update([
                'stock_quantity' => $stockAfter,
            ]);
        });

        return redirect()
            ->route('stock-adjustments.index')
            ->with('success', 'Stock adjusted successfully.');
    }
}
