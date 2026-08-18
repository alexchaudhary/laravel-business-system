<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with('customer')
            ->latest()
            ->get();

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();

        $products = Product::orderBy('name')->get();

        return view('sales.create', compact(
            'customers',
            'products'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',

            'invoice_number' => [
                'required',
                'string',
                'max:255',
                'unique:sales,invoice_number',
            ],

            'sale_date' => 'required|date',

            'status' => [
                'required',
                'in:pending,completed,cancelled',
            ],

            'notes' => 'nullable|string',

            'items' => 'required|array|min:1',

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
                'min:0',
            ],
        ]);

        DB::transaction(function () use ($validated) {

            /*
            |--------------------------------------------------------------------------
            | Check stock for completed sale
            |--------------------------------------------------------------------------
            */

           if ($validated['status'] === 'completed') {

    /*
    |--------------------------------------------------------------------------
    | Combine quantities of the same product
    |--------------------------------------------------------------------------
    */

    $requestedQuantities = [];

    foreach ($validated['items'] as $item) {

        $productId = $item['product_id'];
        $quantity = (float) $item['quantity'];

        if (!isset($requestedQuantities[$productId])) {
            $requestedQuantities[$productId] = 0;
        }

        $requestedQuantities[$productId] += $quantity;
    }

    /*
    |--------------------------------------------------------------------------
    | Check combined stock
    |--------------------------------------------------------------------------
    */

    foreach ($requestedQuantities as $productId => $quantity) {

        $product = Product::lockForUpdate()
            ->findOrFail($productId);

        if (
            (float) $product->stock_quantity
            <
            $quantity
        ) {
            throw ValidationException::withMessages([
                'items' => "Insufficient stock for {$product->name}. "
                    . "Available stock: {$product->stock_quantity}, "
                    . "requested: {$quantity}.",
            ]);
        }
    }
}
            /*
            |--------------------------------------------------------------------------
            | Calculate Grand Total
            |--------------------------------------------------------------------------
            */

            $totalAmount = 0;

            foreach ($validated['items'] as $item) {

                $quantity = (float) $item['quantity'];

                $unitPrice = (float) $item['unit_price'];

                $totalAmount += $quantity * $unitPrice;
            }

            /*
            |--------------------------------------------------------------------------
            | Create Sale
            |--------------------------------------------------------------------------
            */

            $sale = Sale::create([
                'customer_id' => $validated['customer_id'],
                'invoice_number' => $validated['invoice_number'],
                'sale_date' => $validated['sale_date'],
                'total_amount' => $totalAmount,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Create Sale Items
            |--------------------------------------------------------------------------
            */

            foreach ($validated['items'] as $item) {

                $quantity = (float) $item['quantity'];

                $unitPrice = (float) $item['unit_price'];

                $itemTotal = $quantity * $unitPrice;

                $sale->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total' => $itemTotal,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Deduct Stock
            |--------------------------------------------------------------------------
            |
            | Stock decreases only when the sale is completed.
            |
            */

            if ($validated['status'] === 'completed') {

                foreach ($validated['items'] as $item) {

                    $product = Product::lockForUpdate()
                        ->findOrFail($item['product_id']);

                    $product->decrement(
                        'stock_quantity',
                        (float) $item['quantity']
                    );
                }
            }
        });

        return redirect()
            ->route('sales.index')
            ->with(
                'success',
                'Sale created successfully.'
            );
    }

    public function show(Sale $sale)
    {
        $sale->load([
            'customer',
            'items.product',
        ]);

        return view('sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        $sale->load('items.product');

        $customers = Customer::orderBy('name')
            ->get();

        $products = Product::orderBy('name')
            ->get();

        return view('sales.edit', compact(
            'sale',
            'customers',
            'products'
        ));
    }

    public function update(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',

            'invoice_number' => [
                'required',
                'string',
                'max:255',
                'unique:sales,invoice_number,' . $sale->id,
            ],

            'sale_date' => 'required|date',

            'status' => [
                'required',
                'in:pending,completed,cancelled',
            ],

            'notes' => 'nullable|string',

            'items' => 'required|array|min:1',

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
                'min:0',
            ],
        ]);

        DB::transaction(function () use ($validated, $sale) {

            /*
            |--------------------------------------------------------------------------
            | Lock all products involved
            |--------------------------------------------------------------------------
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
            |--------------------------------------------------------------------------
            | Restore stock from old completed sale
            |--------------------------------------------------------------------------
            */

            if ($sale->status === 'completed') {

                $oldItems = $sale->items()->get();

                foreach ($oldItems as $oldItem) {

                    $product = Product::lockForUpdate()
                        ->findOrFail($oldItem->product_id);

                    $product->increment(
                        'stock_quantity',
                        (float) $oldItem->quantity
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Check stock for new completed sale
            |--------------------------------------------------------------------------
            */

            if ($validated['status'] === 'completed') {

                /*
                | Combine same product quantities if the same product
                | appears more than once in the sale.
                */

                $requestedQuantities = [];

                foreach ($validated['items'] as $item) {

                    $productId = $item['product_id'];

                    $quantity = (float) $item['quantity'];

                    if (!isset($requestedQuantities[$productId])) {
                        $requestedQuantities[$productId] = 0;
                    }

                    $requestedQuantities[$productId] += $quantity;
                }

                foreach ($requestedQuantities as $productId => $quantity) {

                    $product = Product::lockForUpdate()
                        ->findOrFail($productId);

                    if (
                        (float) $product->stock_quantity
                        <
                        $quantity
                    ) {
                        throw ValidationException::withMessages([
                            'items' => "Insufficient stock for {$product->name}. "
                                . "Available stock: {$product->stock_quantity}, "
                                . "requested: {$quantity}.",
                        ]);
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Calculate new total
            |--------------------------------------------------------------------------
            */

            $totalAmount = 0;

            foreach ($validated['items'] as $item) {

                $quantity = (float) $item['quantity'];

                $unitPrice = (float) $item['unit_price'];

                $totalAmount += $quantity * $unitPrice;
            }

            /*
            |--------------------------------------------------------------------------
            | Update Sale
            |--------------------------------------------------------------------------
            */

            $sale->update([
                'customer_id' => $validated['customer_id'],
                'invoice_number' => $validated['invoice_number'],
                'sale_date' => $validated['sale_date'],
                'total_amount' => $totalAmount,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Remove Old Sale Items
            |--------------------------------------------------------------------------
            */

            $sale->items()->delete();

            /*
            |--------------------------------------------------------------------------
            | Create New Sale Items
            |--------------------------------------------------------------------------
            */

            foreach ($validated['items'] as $item) {

                $quantity = (float) $item['quantity'];

                $unitPrice = (float) $item['unit_price'];

                $itemTotal = $quantity * $unitPrice;

                $sale->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total' => $itemTotal,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Deduct Stock for New Completed Sale
            |--------------------------------------------------------------------------
            */

            if ($validated['status'] === 'completed') {

                foreach ($validated['items'] as $item) {

                    $product = Product::lockForUpdate()
                        ->findOrFail($item['product_id']);

                    $product->decrement(
                        'stock_quantity',
                        (float) $item['quantity']
                    );
                }
            }
        });

        return redirect()
            ->route('sales.show', $sale)
            ->with(
                'success',
                'Sale updated successfully.'
            );
    }

    public function destroy(Sale $sale)
    {
        DB::transaction(function () use ($sale) {

            /*
            |--------------------------------------------------------------------------
            | Restore stock when deleting a completed sale
            |--------------------------------------------------------------------------
            */

            if ($sale->status === 'completed') {

                $sale->load('items');

                foreach ($sale->items as $item) {

                    $product = Product::lockForUpdate()
                        ->findOrFail($item->product_id);

                    $product->increment(
                        'stock_quantity',
                        (float) $item->quantity
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Delete Sale
            |--------------------------------------------------------------------------
            |
            | sale_items will be deleted automatically because
            | sale_items.sale_id uses cascadeOnDelete().
            |
            */

            $sale->delete();
        });

        return redirect()
            ->route('sales.index')
            ->with(
                'success',
                'Sale deleted successfully.'
            );
    }
}
