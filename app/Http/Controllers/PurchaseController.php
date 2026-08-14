<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with('supplier')
            ->latest()
            ->get();

        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::where('active', true)
            ->orderBy('name')
            ->get();

        $products = Product::orderBy('name')
            ->get();

        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'invoice_number' => 'required|string|max:255|unique:purchases,invoice_number',
            'purchase_date' => 'required|date',
            'status' => 'required|in:pending,received,cancelled',
            'notes' => 'nullable|string',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {

            $totalAmount = 0;

            foreach ($validated['items'] as $item) {
                $totalAmount +=
                    (float) $item['quantity'] *
                    (float) $item['unit_price'];
            }

            $purchase = Purchase::create([
                'supplier_id' => $validated['supplier_id'],
                'invoice_number' => $validated['invoice_number'],
                'purchase_date' => $validated['purchase_date'],
                'total_amount' => $totalAmount,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {

                $quantity = (float) $item['quantity'];

                $itemTotal =
                    $quantity *
                    (float) $item['unit_price'];

                $purchase->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $quantity,
                    'unit_price' => $item['unit_price'],
                    'total' => $itemTotal,
                ]);

                /*
                 * Only RECEIVED purchases increase stock.
                 */
                if ($validated['status'] === 'received') {
                    $product = Product::whereKey($item['product_id'])
                        ->lockForUpdate()
                        ->firstOrFail();

                    $product->increment('stock_quantity', $quantity);
                }
            }
        });

        return redirect()
            ->route('purchases.index')
            ->with('success', 'Purchase created successfully.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load([
            'supplier',
            'items.product',
        ]);

        return view('purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        $purchase->load('items.product');

        $suppliers = Supplier::where('active', true)
            ->orderBy('name')
            ->get();

        $products = Product::orderBy('name')
            ->get();

        return view('purchases.edit', compact(
            'purchase',
            'suppliers',
            'products'
        ));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'invoice_number' => 'required|string|max:255|unique:purchases,invoice_number,' . $purchase->id,
            'purchase_date' => 'required|date',
            'status' => 'required|in:pending,received,cancelled',
            'notes' => 'nullable|string',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $purchase) {

            /*
             * Keep old items before deleting them.
             */
            $purchase->load('items');

            /*
             * STEP 1:
             * Reverse old stock if the old purchase was RECEIVED.
             */
            if ($purchase->status === 'received') {

                foreach ($purchase->items as $oldItem) {

                    $product = Product::whereKey($oldItem->product_id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    $currentStock = (float) $product->stock_quantity;
                    $oldQuantity = (float) $oldItem->quantity;

                    /*
                     * Safety check.
                     * The old received purchase cannot be reversed
                     * if stock has already gone below its quantity.
                     */
                    if ($currentStock < $oldQuantity) {
                        throw ValidationException::withMessages([
                            'purchase' =>
                                "Cannot update this purchase because stock for {$product->name} has already been used. "
                                . "Available stock: {$currentStock}, required to reverse: {$oldQuantity}.",
                        ]);
                    }

                    $product->decrement(
                        'stock_quantity',
                        $oldQuantity
                    );
                }
            }

            /*
             * Calculate new purchase total.
             */
            $totalAmount = 0;

            foreach ($validated['items'] as $item) {
                $totalAmount +=
                    (float) $item['quantity'] *
                    (float) $item['unit_price'];
            }

            /*
             * Update purchase information.
             */
            $purchase->update([
                'supplier_id' => $validated['supplier_id'],
                'invoice_number' => $validated['invoice_number'],
                'purchase_date' => $validated['purchase_date'],
                'total_amount' => $totalAmount,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            /*
             * Remove old items.
             */
            $purchase->items()->delete();

            /*
             * Create new items.
             */
            foreach ($validated['items'] as $item) {

                $quantity = (float) $item['quantity'];

                $itemTotal =
                    $quantity *
                    (float) $item['unit_price'];

                $purchase->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $quantity,
                    'unit_price' => $item['unit_price'],
                    'total' => $itemTotal,
                ]);

                /*
                 * Apply new stock only if new status is RECEIVED.
                 */
                if ($validated['status'] === 'received') {

                    $product = Product::whereKey($item['product_id'])
                        ->lockForUpdate()
                        ->firstOrFail();

                    $product->increment(
                        'stock_quantity',
                        $quantity
                    );
                }
            }
        });

        return redirect()
            ->route('purchases.show', $purchase)
            ->with('success', 'Purchase updated successfully.');
    }

    public function destroy(Purchase $purchase)
    {
        DB::transaction(function () use ($purchase) {

            $purchase->load('items');

            /*
             * Only RECEIVED purchases affected stock.
             * Therefore only RECEIVED purchases need stock reversal.
             */
            if ($purchase->status === 'received') {

                foreach ($purchase->items as $item) {

                    $product = Product::whereKey($item->product_id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    $currentStock = (float) $product->stock_quantity;
                    $quantity = (float) $item->quantity;

                    /*
                     * Do not allow stock to become negative.
                     */
                    if ($currentStock < $quantity) {
                        throw ValidationException::withMessages([
                            'purchase' =>
                                "Cannot delete this purchase because stock for {$product->name} has already been used. "
                                . "Available stock: {$currentStock}, required to remove: {$quantity}.",
                        ]);
                    }

                    $product->decrement(
                        'stock_quantity',
                        $quantity
                    );
                }
            }

            /*
             * Delete purchase.
             * Purchase items will be deleted according to the
             * existing relationship/database setup.
             */
            $purchase->delete();
        });

        return redirect()
            ->route('purchases.index')
            ->with('success', 'Purchase deleted successfully.');
    }
}

