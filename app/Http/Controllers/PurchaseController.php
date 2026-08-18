<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Display a listing of purchases.
     */
    public function index()
    {
        $purchases = Purchase::with('supplier')
            ->latest('purchase_date')
            ->latest('id')
            ->get();

        return view('purchases.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new purchase.
     */
    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view(
            'purchases.create',
            compact('suppliers', 'products')
        );
    }

    /**
     * Store a newly created purchase.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => [
                'required',
                'exists:suppliers,id',
            ],

            'invoice_number' => [
                'required',
                'string',
                'max:255',
                'unique:purchases,invoice_number',
            ],

            'purchase_date' => [
                'required',
                'date',
            ],

            'status' => [
                'required',
                'in:pending,received,cancelled',
            ],

            'notes' => [
                'nullable',
                'string',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.product_id' => [
                'required',
                'exists:products,id',
            ],

            'items.*.quantity' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'items.*.unit_price' => [
                'required',
                'numeric',
                'min:0.01',
            ],
        ]);

        $purchase = DB::transaction(function () use ($validated) {
            /*
             * Lock all products involved in this purchase.
             */
            $productIds = collect($validated['items'])
                ->pluck('product_id')
                ->unique()
                ->values();

            foreach ($productIds as $productId) {
                Product::lockForUpdate()
                    ->findOrFail($productId);
            }

            /*
             * Calculate total amount.
             */
            $totalAmount = 0;

            foreach ($validated['items'] as $item) {
                $quantity = (float) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];

                $totalAmount += $quantity * $unitPrice;
            }

            /*
             * Create purchase.
             */
            $purchase = Purchase::create([
                'supplier_id' => $validated['supplier_id'],
                'invoice_number' => $validated['invoice_number'],
                'purchase_date' => $validated['purchase_date'],
                'total_amount' => $totalAmount,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            /*
             * Create purchase items.
             */
            foreach ($validated['items'] as $item) {
                $quantity = (float) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];

                $itemTotal = $quantity * $unitPrice;

                $purchase->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total' => $itemTotal,
                ]);
            }

            /*
             * Increase stock only for received purchases.
             *
             * Pending and cancelled purchases do not affect stock.
             */
            if ($validated['status'] === 'received') {
                foreach ($validated['items'] as $item) {
                    $product = Product::lockForUpdate()
                        ->findOrFail($item['product_id']);

                    $product->increment(
                        'stock_quantity',
                        (float) $item['quantity']
                    );
                }
            }

            return $purchase;
        });

        return redirect()
            ->route('purchases.index')
            ->with(
                'success',
                'Purchase created successfully.'
            );
    }

    /**
     * Display the specified purchase.
     */
    public function show(Purchase $purchase)
    {
        $purchase->load([
            'supplier',
            'items.product',
        ]);

        return view(
            'purchases.show',
            compact('purchase')
        );
    }

    /**
     * Show the form for editing the specified purchase.
     */
    public function edit(Purchase $purchase)
    {
        $purchase->load('items.product');

        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view(
            'purchases.edit',
            compact(
                'purchase',
                'suppliers',
                'products'
            )
        );
    }

    /**
     * Update the specified purchase.
     */
    public function update(
        Request $request,
        Purchase $purchase
    ) {
        $validated = $request->validate([
            'supplier_id' => [
                'required',
                'exists:suppliers,id',
            ],

            'invoice_number' => [
                'required',
                'string',
                'max:255',
                'unique:purchases,invoice_number,' . $purchase->id,
            ],

            'purchase_date' => [
                'required',
                'date',
            ],

            'status' => [
                'required',
                'in:pending,received,cancelled',
            ],

            'notes' => [
                'nullable',
                'string',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.product_id' => [
                'required',
                'exists:products,id',
            ],

            'items.*.quantity' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'items.*.unit_price' => [
                'required',
                'numeric',
                'min:0.01',
            ],
        ]);

        DB::transaction(function () use (
            $validated,
            $purchase
        ) {
            /*
             * Get old purchase items before deleting them.
             */
            $oldItems = $purchase->items()->get();

            /*
             * Lock all products involved in both
             * old and new purchase items.
             */
            $productIds = $oldItems
                ->pluck('product_id')
                ->merge(
                    collect($validated['items'])
                        ->pluck('product_id')
                )
                ->unique()
                ->values();

            foreach ($productIds as $productId) {
                Product::lockForUpdate()
                    ->findOrFail($productId);
            }

            /*
             * If the old purchase was received,
             * remove its old stock effect first.
             *
             * Example:
             * old quantity = 5
             * stock = 15
             *
             * Restore old purchase:
             * stock = 10
             */
            if ($purchase->status === 'received') {
                foreach ($oldItems as $oldItem) {
                    $product = Product::lockForUpdate()
                        ->findOrFail($oldItem->product_id);

                    $product->decrement(
                        'stock_quantity',
                        (float) $oldItem->quantity
                    );
                }
            }

            /*
             * Calculate new total.
             */
            $totalAmount = 0;

            foreach ($validated['items'] as $item) {
                $quantity = (float) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];

                $totalAmount += $quantity * $unitPrice;
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
             * Remove old purchase items.
             */
            $purchase->items()->delete();

            /*
             * Create new purchase items.
             */
            foreach ($validated['items'] as $item) {
                $quantity = (float) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];

                $itemTotal = $quantity * $unitPrice;

                $purchase->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total' => $itemTotal,
                ]);
            }

            /*
             * Apply stock effect for the NEW status.
             *
             * received = increase stock
             * pending  = no stock change
             * cancelled = no stock change
             */
            if ($validated['status'] === 'received') {
                foreach ($validated['items'] as $item) {
                    $product = Product::lockForUpdate()
                        ->findOrFail($item['product_id']);

                    $product->increment(
                        'stock_quantity',
                        (float) $item['quantity']
                    );
                }
            }
        });

        return redirect()
            ->route('purchases.show', $purchase)
            ->with(
                'success',
                'Purchase updated successfully.'
            );
    }

    /**
     * Remove the specified purchase.
     */
    public function destroy(Purchase $purchase)
    {
        DB::transaction(function () use ($purchase) {
            /*
             * Restore stock when deleting a received purchase.
             */
            if ($purchase->status === 'received') {
                $purchase->load('items');

                foreach ($purchase->items as $item) {
                    $product = Product::lockForUpdate()
                        ->findOrFail($item->product_id);

                    $product->decrement(
                        'stock_quantity',
                        (float) $item->quantity
                    );
                }
            }

            /*
             * Delete purchase.
             *
             * purchase_items should be deleted automatically
             * if the foreign key uses cascadeOnDelete().
             */
            $purchase->delete();
        });

        return redirect()
            ->route('purchases.index')
            ->with(
                'success',
                'Purchase deleted successfully.'
            );
    }
}