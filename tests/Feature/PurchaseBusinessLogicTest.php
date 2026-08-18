<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseBusinessLogicTest extends TestCase
{
    use RefreshDatabase;

    public function test_received_purchase_increases_product_stock(): void
    {
        $supplier = Supplier::create([
            'name' => 'Test Supplier',
            'email' => 'supplier@example.com',
            'phone' => '9800000000',
            'address' => 'Kathmandu',
            'active' => true,
        ]);

        $product = Product::create([
            'name' => 'Test Purchase Product',
            'sku' => 'PURCHASE-SKU-001',
            'category' => 'Test',
            'purchase_price' => 500,
            'selling_price' => 800,
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
            'description' => null,
            'is_active' => true,
        ]);

        $response = $this->actingAs(
            User::factory()->create([
                'role' => 'admin',
            ])
        )->post('/purchases', [
            'supplier_id' => $supplier->id,
            'invoice_number' => 'PUR-TEST-001',
            'purchase_date' => now()->format('Y-m-d'),
            'status' => 'received',
            'notes' => null,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 500,
                ],
            ],
        ]);

        $response->assertRedirect('/purchases');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 15.00,
        ]);

        $this->assertDatabaseHas('purchases', [
            'supplier_id' => $supplier->id,
            'invoice_number' => 'PUR-TEST-001',
            'status' => 'received',
            'total_amount' => 2500.00,
        ]);

        $this->assertDatabaseHas('purchase_items', [
            'product_id' => $product->id,
            'quantity' => 5.00,
            'unit_price' => 500.00,
            'total' => 2500.00,
        ]);
    }

    public function test_pending_purchase_does_not_increase_product_stock(): void
    {
        $supplier = Supplier::create([
            'name' => 'Pending Supplier',
            'email' => 'pending@example.com',
            'phone' => '9800000001',
            'address' => 'Kathmandu',
            'active' => true,
        ]);

        $product = Product::create([
            'name' => 'Pending Purchase Product',
            'sku' => 'PURCHASE-SKU-002',
            'category' => 'Test',
            'purchase_price' => 500,
            'selling_price' => 800,
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
            'description' => null,
            'is_active' => true,
        ]);

        $response = $this->actingAs(
            User::factory()->create([
                'role' => 'admin',
            ])
        )->post('/purchases', [
            'supplier_id' => $supplier->id,
            'invoice_number' => 'PUR-TEST-002',
            'purchase_date' => now()->format('Y-m-d'),
            'status' => 'pending',
            'notes' => null,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 500,
                ],
            ],
        ]);

        $response->assertRedirect('/purchases');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 10.00,
        ]);

        $this->assertDatabaseHas('purchases', [
            'supplier_id' => $supplier->id,
            'invoice_number' => 'PUR-TEST-002',
            'status' => 'pending',
            'total_amount' => 2500.00,
        ]);
    }

    public function test_updating_received_purchase_to_pending_restores_old_stock(): void
    {
        $supplier = Supplier::create([
            'name' => 'Update Supplier',
            'email' => 'update@example.com',
            'phone' => '9800000002',
            'address' => 'Kathmandu',
            'active' => true,
        ]);

        $product = Product::create([
            'name' => 'Update Purchase Product',
            'sku' => 'PURCHASE-SKU-003',
            'category' => 'Test',
            'purchase_price' => 500,
            'selling_price' => 800,
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
            'description' => null,
            'is_active' => true,
        ]);

        $this->actingAs(
            User::factory()->create([
                'role' => 'admin',
            ])
        )->post('/purchases', [
            'supplier_id' => $supplier->id,
            'invoice_number' => 'PUR-TEST-003',
            'purchase_date' => now()->format('Y-m-d'),
            'status' => 'received',
            'notes' => null,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 500,
                ],
            ],
        ])->assertRedirect('/purchases');

        $purchase = Purchase::where(
            'invoice_number',
            'PUR-TEST-003'
        )->firstOrFail();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 15.00,
        ]);

        $response = $this->actingAs(
            User::factory()->create([
                'role' => 'admin',
            ])
        )->put("/purchases/{$purchase->id}", [
            'supplier_id' => $supplier->id,
            'invoice_number' => 'PUR-TEST-003',
            'purchase_date' => now()->format('Y-m-d'),
            'status' => 'pending',
            'notes' => null,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 500,
                ],
            ],
        ]);

        $response->assertRedirect("/purchases/{$purchase->id}");

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 10.00,
        ]);

        $this->assertDatabaseHas('purchases', [
            'id' => $purchase->id,
            'status' => 'pending',
            'total_amount' => 2500.00,
        ]);
    }

    public function test_updating_received_purchase_quantity_adjusts_product_stock_correctly(): void
    {
        $supplier = Supplier::create([
            'name' => 'Quantity Update Supplier',
            'email' => 'quantity-update@example.com',
            'phone' => '9800000003',
            'address' => 'Kathmandu',
            'active' => true,
        ]);

        $product = Product::create([
            'name' => 'Quantity Update Product',
            'sku' => 'PURCHASE-SKU-004',
            'category' => 'Test',
            'purchase_price' => 500,
            'selling_price' => 800,
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
            'description' => null,
            'is_active' => true,
        ]);

        $this->actingAs(
            User::factory()->create([
                'role' => 'admin',
            ])
        )->post('/purchases', [
            'supplier_id' => $supplier->id,
            'invoice_number' => 'PUR-TEST-004',
            'purchase_date' => now()->format('Y-m-d'),
            'status' => 'received',
            'notes' => null,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 500,
                ],
            ],
        ])->assertRedirect('/purchases');

        $purchase = Purchase::where(
            'invoice_number',
            'PUR-TEST-004'
        )->firstOrFail();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 15.00,
        ]);

        $response = $this->actingAs(
            User::factory()->create([
                'role' => 'admin',
            ])
        )->put("/purchases/{$purchase->id}", [
            'supplier_id' => $supplier->id,
            'invoice_number' => 'PUR-TEST-004',
            'purchase_date' => now()->format('Y-m-d'),
            'status' => 'received',
            'notes' => null,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 8,
                    'unit_price' => 500,
                ],
            ],
        ]);

        $response->assertRedirect("/purchases/{$purchase->id}");

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 18.00,
        ]);

        $this->assertDatabaseHas('purchases', [
            'id' => $purchase->id,
            'status' => 'received',
            'total_amount' => 4000.00,
        ]);

        $this->assertDatabaseHas('purchase_items', [
            'purchase_id' => $purchase->id,
            'product_id' => $product->id,
            'quantity' => 8.00,
            'unit_price' => 500.00,
            'total' => 4000.00,
        ]);
    }

    public function test_updating_received_purchase_to_cancelled_restores_product_stock(): void
    {
        $supplier = Supplier::create([
            'name' => 'Cancelled Supplier',
            'email' => 'cancelled@example.com',
            'phone' => '9800000004',
            'address' => 'Kathmandu',
            'active' => true,
        ]);

        $product = Product::create([
            'name' => 'Cancelled Purchase Product',
            'sku' => 'PURCHASE-SKU-005',
            'category' => 'Test',
            'purchase_price' => 500,
            'selling_price' => 800,
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
            'description' => null,
            'is_active' => true,
        ]);

        $this->actingAs(
            User::factory()->create([
                'role' => 'admin',
            ])
        )->post('/purchases', [
            'supplier_id' => $supplier->id,
            'invoice_number' => 'PUR-TEST-005',
            'purchase_date' => now()->format('Y-m-d'),
            'status' => 'received',
            'notes' => null,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 500,
                ],
            ],
        ])->assertRedirect('/purchases');

        $purchase = Purchase::where(
            'invoice_number',
            'PUR-TEST-005'
        )->firstOrFail();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 15.00,
        ]);

        $response = $this->actingAs(
            User::factory()->create([
                'role' => 'admin',
            ])
        )->put("/purchases/{$purchase->id}", [
            'supplier_id' => $supplier->id,
            'invoice_number' => 'PUR-TEST-005',
            'purchase_date' => now()->format('Y-m-d'),
            'status' => 'cancelled',
            'notes' => null,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 500,
                ],
            ],
        ]);

        $response->assertRedirect("/purchases/{$purchase->id}");

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 10.00,
        ]);

        $this->assertDatabaseHas('purchases', [
            'id' => $purchase->id,
            'status' => 'cancelled',
            'total_amount' => 2500.00,
        ]);

        $this->assertDatabaseHas('purchase_items', [
            'purchase_id' => $purchase->id,
            'product_id' => $product->id,
            'quantity' => 5.00,
            'unit_price' => 500.00,
            'total' => 2500.00,
        ]);
    }

    public function test_deleting_received_purchase_restores_product_stock(): void
    {
        $supplier = Supplier::create([
            'name' => 'Delete Supplier',
            'email' => 'delete@example.com',
            'phone' => '9800000005',
            'address' => 'Kathmandu',
            'active' => true,
        ]);

        $product = Product::create([
            'name' => 'Delete Purchase Product',
            'sku' => 'PURCHASE-SKU-006',
            'category' => 'Test',
            'purchase_price' => 500,
            'selling_price' => 800,
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
            'description' => null,
            'is_active' => true,
        ]);

        $this->actingAs(
            User::factory()->create([
                'role' => 'admin',
            ])
        )->post('/purchases', [
            'supplier_id' => $supplier->id,
            'invoice_number' => 'PUR-TEST-006',
            'purchase_date' => now()->format('Y-m-d'),
            'status' => 'received',
            'notes' => null,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 500,
                ],
            ],
        ])->assertRedirect('/purchases');

        $purchase = Purchase::where(
            'invoice_number',
            'PUR-TEST-006'
        )->firstOrFail();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 15.00,
        ]);

        $response = $this->actingAs(
            User::factory()->create([
                'role' => 'admin',
            ])
        )->delete("/purchases/{$purchase->id}");

        $response->assertRedirect('/purchases');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 10.00,
        ]);

        $this->assertDatabaseMissing('purchases', [
            'id' => $purchase->id,
        ]);

        $this->assertDatabaseMissing('purchase_items', [
            'purchase_id' => $purchase->id,
        ]);
    }

    public function test_updating_pending_purchase_to_received_increases_product_stock(): void
    {
        $supplier = Supplier::create([
            'name' => 'Pending To Received Supplier',
            'email' => 'pending-to-received@example.com',
            'phone' => '9800000006',
            'address' => 'Kathmandu',
            'active' => true,
        ]);

        $product = Product::create([
            'name' => 'Pending To Received Product',
            'sku' => 'PURCHASE-SKU-007',
            'category' => 'Test',
            'purchase_price' => 500,
            'selling_price' => 800,
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
            'description' => null,
            'is_active' => true,
        ]);

        $this->actingAs(
            User::factory()->create([
                'role' => 'admin',
            ])
        )->post('/purchases', [
            'supplier_id' => $supplier->id,
            'invoice_number' => 'PUR-TEST-007',
            'purchase_date' => now()->format('Y-m-d'),
            'status' => 'pending',
            'notes' => null,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 500,
                ],
            ],
        ])->assertRedirect('/purchases');

        $purchase = Purchase::where(
            'invoice_number',
            'PUR-TEST-007'
        )->firstOrFail();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 10.00,
        ]);

        $response = $this->actingAs(
            User::factory()->create([
                'role' => 'admin',
            ])
        )->put("/purchases/{$purchase->id}", [
            'supplier_id' => $supplier->id,
            'invoice_number' => 'PUR-TEST-007',
            'purchase_date' => now()->format('Y-m-d'),
            'status' => 'received',
            'notes' => null,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 500,
                ],
            ],
        ]);

        $response->assertRedirect("/purchases/{$purchase->id}");

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 15.00,
        ]);

        $this->assertDatabaseHas('purchases', [
            'id' => $purchase->id,
            'status' => 'received',
            'total_amount' => 2500.00,
        ]);

        $this->assertDatabaseHas('purchase_items', [
            'purchase_id' => $purchase->id,
            'product_id' => $product->id,
            'quantity' => 5.00,
            'unit_price' => 500.00,
            'total' => 2500.00,
        ]);
    }

    public function test_purchase_with_multiple_products_increases_each_product_stock(): void
    {
        $supplier = Supplier::create([
            'name' => 'Multiple Products Supplier',
            'email' => 'multiple-products@example.com',
            'phone' => '9800000005',
            'address' => 'Kathmandu',
            'active' => true,
        ]);

        $productOne = Product::create([
            'name' => 'Multiple Product One',
            'sku' => 'PURCHASE-SKU-008',
            'category' => 'Test',
            'purchase_price' => 500,
            'selling_price' => 800,
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
            'description' => null,
            'is_active' => true,
        ]);

        $productTwo = Product::create([
            'name' => 'Multiple Product Two',
            'sku' => 'PURCHASE-SKU-009',
            'category' => 'Test',
            'purchase_price' => 1000,
            'selling_price' => 1500,
            'stock_quantity' => 20,
            'low_stock_threshold' => 2,
            'description' => null,
            'is_active' => true,
        ]);

        $response = $this->actingAs(
            User::factory()->create([
                'role' => 'admin',
            ])
        )->post('/purchases', [
            'supplier_id' => $supplier->id,
            'invoice_number' => 'PUR-TEST-008',
            'purchase_date' => now()->format('Y-m-d'),
            'status' => 'received',
            'notes' => null,
            'items' => [
                [
                    'product_id' => $productOne->id,
                    'quantity' => 5,
                    'unit_price' => 500,
                ],
                [
                    'product_id' => $productTwo->id,
                    'quantity' => 3,
                    'unit_price' => 1000,
                ],
            ],
        ]);

        $response->assertRedirect('/purchases');

        $purchase = Purchase::where(
            'invoice_number',
            'PUR-TEST-008'
        )->firstOrFail();

        $this->assertDatabaseHas('products', [
            'id' => $productOne->id,
            'stock_quantity' => 15.00,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $productTwo->id,
            'stock_quantity' => 23.00,
        ]);

        $this->assertDatabaseHas('purchases', [
            'id' => $purchase->id,
            'supplier_id' => $supplier->id,
            'invoice_number' => 'PUR-TEST-008',
            'status' => 'received',
            'total_amount' => 5500.00,
        ]);

        $this->assertDatabaseHas('purchase_items', [
            'purchase_id' => $purchase->id,
            'product_id' => $productOne->id,
            'quantity' => 5.00,
            'unit_price' => 500.00,
            'total' => 2500.00,
        ]);

        $this->assertDatabaseHas('purchase_items', [
            'purchase_id' => $purchase->id,
            'product_id' => $productTwo->id,
            'quantity' => 3.00,
            'unit_price' => 1000.00,
            'total' => 3000.00,
        ]);
    }

    public function test_purchase_with_same_product_multiple_items_handles_stock_correctly(): void
    {
        $supplier = Supplier::create([
            'name' => 'Duplicate Product Supplier',
            'email' => 'duplicate-product@example.com',
            'phone' => '9800000007',
            'address' => 'Kathmandu',
            'active' => true,
        ]);

        $product = Product::create([
            'name' => 'Duplicate Product Purchase',
            'sku' => 'PURCHASE-SKU-010',
            'category' => 'Test',
            'purchase_price' => 500,
            'selling_price' => 800,
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
            'description' => null,
            'is_active' => true,
        ]);

        $response = $this->actingAs(
            User::factory()->create([
                'role' => 'admin',
            ])
        )->post('/purchases', [
            'supplier_id' => $supplier->id,
            'invoice_number' => 'PUR-TEST-009',
            'purchase_date' => now()->format('Y-m-d'),
            'status' => 'received',
            'notes' => null,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 500,
                ],
                [
                    'product_id' => $product->id,
                    'quantity' => 3,
                    'unit_price' => 500,
                ],
            ],
        ]);

        $response->assertRedirect('/purchases');

        $purchase = Purchase::where(
            'invoice_number',
            'PUR-TEST-009'
        )->firstOrFail();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 18.00,
        ]);

        $this->assertDatabaseHas('purchases', [
            'id' => $purchase->id,
            'supplier_id' => $supplier->id,
            'invoice_number' => 'PUR-TEST-009',
            'status' => 'received',
            'total_amount' => 4000.00,
        ]);

        $this->assertDatabaseHas('purchase_items', [
            'purchase_id' => $purchase->id,
            'product_id' => $product->id,
            'quantity' => 5.00,
            'unit_price' => 500.00,
            'total' => 2500.00,
        ]);

        $this->assertDatabaseHas('purchase_items', [
            'purchase_id' => $purchase->id,
            'product_id' => $product->id,
            'quantity' => 3.00,
            'unit_price' => 500.00,
            'total' => 1500.00,
        ]);
    }

    public function test_purchase_with_zero_quantity_is_rejected(): void
    {
        $supplier = Supplier::create([
            'name' => 'Invalid Quantity Supplier',
            'email' => 'invalid-quantity@example.com',
            'phone' => '9800000009',
            'address' => 'Kathmandu',
            'active' => true,
        ]);

        $product = Product::create([
            'name' => 'Invalid Quantity Product',
            'sku' => 'PURCHASE-SKU-011',
            'category' => 'Test',
            'purchase_price' => 500,
            'selling_price' => 800,
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
            'description' => null,
            'is_active' => true,
        ]);

        $response = $this->actingAs(
            User::factory()->create([
                'role' => 'admin',
            ])
        )->post('/purchases', [
            'supplier_id' => $supplier->id,
            'invoice_number' => 'PUR-TEST-011',
            'purchase_date' => now()->format('Y-m-d'),
            'status' => 'received',
            'notes' => null,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 0,
                    'unit_price' => 500,
                ],
            ],
        ]);

        $response->assertSessionHasErrors([
            'items.0.quantity',
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 10.00,
        ]);

        $this->assertDatabaseMissing('purchases', [
            'invoice_number' => 'PUR-TEST-011',
        ]);
    }

    public function test_purchase_with_negative_quantity_is_rejected(): void
    {
        $supplier = Supplier::create([
            'name' => 'Negative Quantity Supplier',
            'email' => 'negative-quantity@example.com',
            'phone' => '9800000010',
            'address' => 'Kathmandu',
            'active' => true,
        ]);

        $product = Product::create([
            'name' => 'Negative Quantity Product',
            'sku' => 'PURCHASE-SKU-012',
            'category' => 'Test',
            'purchase_price' => 500,
            'selling_price' => 800,
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
            'description' => null,
            'is_active' => true,
        ]);

        $response = $this->actingAs(
            User::factory()->create([
                'role' => 'admin',
            ])
        )->post('/purchases', [
            'supplier_id' => $supplier->id,
            'invoice_number' => 'PUR-TEST-012',
            'purchase_date' => now()->format('Y-m-d'),
            'status' => 'received',
            'notes' => null,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => -1,
                    'unit_price' => 500,
                ],
            ],
        ]);

        $response->assertSessionHasErrors([
            'items.0.quantity',
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 10.00,
        ]);

        $this->assertDatabaseMissing('purchases', [
            'invoice_number' => 'PUR-TEST-012',
        ]);

        $this->assertDatabaseMissing('purchase_items', [
            'product_id' => $product->id,
            'quantity' => -1,
        ]);
    }

    public function test_purchase_with_zero_unit_price_is_rejected(): void
    {
        $supplier = Supplier::create([
            'name' => 'Zero Price Supplier',
            'email' => 'zero-price@example.com',
            'phone' => '9800000011',
            'address' => 'Kathmandu',
            'active' => true,
        ]);

        $product = Product::create([
            'name' => 'Zero Price Product',
            'sku' => 'PURCHASE-SKU-013',
            'category' => 'Test',
            'purchase_price' => 500,
            'selling_price' => 800,
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
            'description' => null,
            'is_active' => true,
        ]);

        $response = $this->actingAs(
            User::factory()->create([
                'role' => 'admin',
            ])
        )->post('/purchases', [
            'supplier_id' => $supplier->id,
            'invoice_number' => 'PUR-TEST-013',
            'purchase_date' => now()->format('Y-m-d'),
            'status' => 'received',
            'notes' => null,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 0,
                ],
            ],
        ]);

        $response->assertSessionHasErrors([
            'items.0.unit_price',
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 10.00,
        ]);

        $this->assertDatabaseMissing('purchases', [
            'invoice_number' => 'PUR-TEST-013',
        ]);

        $this->assertDatabaseMissing('purchase_items', [
            'product_id' => $product->id,
            'quantity' => 5,
            'unit_price' => 0,
        ]);
    }

    public function test_purchase_with_negative_unit_price_is_rejected(): void
    {
        $supplier = Supplier::create([
            'name' => 'Negative Price Supplier',
            'email' => 'negative-price@example.com',
            'phone' => '9800000012',
            'address' => 'Kathmandu',
            'active' => true,
        ]);

        $product = Product::create([
            'name' => 'Negative Price Product',
            'sku' => 'PURCHASE-SKU-014',
            'category' => 'Test',
            'purchase_price' => 500,
            'selling_price' => 800,
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
            'description' => null,
            'is_active' => true,
        ]);

        $response = $this->actingAs(
            User::factory()->create([
                'role' => 'admin',
            ])
        )->post('/purchases', [
            'supplier_id' => $supplier->id,
            'invoice_number' => 'PUR-TEST-014',
            'purchase_date' => now()->format('Y-m-d'),
            'status' => 'received',
            'notes' => null,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => -1,
                ],
            ],
        ]);

        $response->assertSessionHasErrors([
            'items.0.unit_price',
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 10.00,
        ]);

        $this->assertDatabaseMissing('purchases', [
            'invoice_number' => 'PUR-TEST-014',
        ]);

        $this->assertDatabaseMissing('purchase_items', [
            'product_id' => $product->id,
            'quantity' => 5,
            'unit_price' => -1,
        ]);
    }
}
