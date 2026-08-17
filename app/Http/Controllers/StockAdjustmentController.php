<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockAdjustmentController extends Controller
{
    /**
     * Show stock adjustment history.
     */
    public function index()
    {
        $adjustments = StockAdjustment::with('product')
            ->latest()
            ->get();

        return view('stock-adjustments.index', compact('adjustments'));
    }

    /**
     * Show stock adjustment form.
     */
    public function create(Product $product)
    {
        return view('stock-adjustments.create', compact('product'));
    }

    /**
     * Store stock adjustment.
     */
    
public function store(Request $request, Product $product)
{
    $validated = $request->validate([
        'type' => [
            'required',
            'in:increase,decrease',
        ],

        'quantity' => [
            'required',
            'numeric',
            'min:0.01',
        ],

        'reason' => [
            'nullable',
            'string',
            'max:500',
        ],
    ]);

    DB::transaction(function () use ($validated, $product) {

        $product = Product::lockForUpdate()->findOrFail($product->id);

        $currentStock = (float) $product->stock_quantity;
        $quantity = (float) $validated['quantity'];

        if ($validated['type'] === 'increase') {

            $newStock = $currentStock + $quantity;
            $adjustmentType = 'in';

        } else {

            if ($quantity > $currentStock) {
                throw ValidationException::withMessages([
                    'quantity' => "Cannot decrease more than available stock. "
                        . "Available stock: {$currentStock}.",
                ]);
            }

            $newStock = $currentStock - $quantity;
            $adjustmentType = 'out';
        }

        // Update product stock
        $product->update([
            'stock_quantity' => $newStock,
        ]);

        // Save stock adjustment history
        StockAdjustment::create([
            'product_id' => $product->id,
            'type' => $adjustmentType,
            'quantity' => $quantity,
            'stock_before' => $currentStock,
            'stock_after' => $newStock,
            'note' => $validated['reason'] ?? null,
        ]);
    });

    return redirect()
        ->route('inventory.index')
        ->with(
            'success',
            'Stock adjusted successfully.'
        );
}
}