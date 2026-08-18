<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleBusinessLogicTest extends TestCase
{
    use RefreshDatabase;

    public function test_completed_sale_reduces_product_stock(): void
    {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'phone' => '9800000000',
            'address' => 'Kathmandu',
        ]);

        $product = Product::create([
            'name' => 'Test Product',
            'sku' => 'TEST-SKU-001',
            'category' => 'Test',
            'purchase_price' => 500,
            'selling_price' => 800,
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
            'description' => null,
            'is_active' => true,
        ]);

        $response = $this->actingAs(
            \App\Models\User::factory()->create()
        )->post('/sales', [
            'customer_id' => $customer->id,
            'invoice_number' => 'INV-TEST-001',
            'sale_date' => now()->format('Y-m-d'),
            'status' => 'completed',
            'notes' => null,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 3,
                    'unit_price' => 800,
                ],
            ],
        ]);

        $response->assertRedirect('/sales');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 7.00,
        ]);

        $this->assertDatabaseHas('sales', [
            'customer_id' => $customer->id,
            'invoice_number' => 'INV-TEST-001',
            'status' => 'completed',
            'total_amount' => 2400.00,
        ]);

        $this->assertDatabaseHas('sale_items', [
            'product_id' => $product->id,
            'quantity' => 3.00,
            'unit_price' => 800.00,
            'total' => 2400.00,
        ]);
    }

    public function test_completed_sale_is_rejected_when_stock_is_insufficient(): void
    {
        $customer = Customer::create([
            'name' => 'Test Customer 2',
            'email' => 'customer2@example.com',
            'phone' => '9800000001',
            'address' => 'Kathmandu',
        ]);

        $product = Product::create([
            'name' => 'Limited Product',
            'sku' => 'TEST-SKU-002',
            'category' => 'Test',
            'purchase_price' => 500,
            'selling_price' => 800,
            'stock_quantity' => 2,
            'low_stock_threshold' => 1,
            'description' => null,
            'is_active' => true,
        ]);

        $response = $this->actingAs(
            \App\Models\User::factory()->create()
        )->post('/sales', [
            'customer_id' => $customer->id,
            'invoice_number' => 'INV-TEST-002',
            'sale_date' => now()->format('Y-m-d'),
            'status' => 'completed',
            'notes' => null,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 800,
                ],
            ],
        ]);

        $response->assertSessionHasErrors('items');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 2.00,
        ]);

        $this->assertDatabaseMissing('sales', [
            'invoice_number' => 'INV-TEST-002',
        ]);
    }

    public function test_updating_completed_sale_restores_old_stock_and_deducts_new_stock(): void
    {
        $customer = Customer::create([
            'name' => 'Update Customer',
            'email' => 'update-sale@example.com',
            'phone' => '9800000002',
            'address' => 'Kathmandu',
        ]);

        $product = Product::create([
            'name' => 'Update Product',
            'sku' => 'TEST-SKU-003',
            'category' => 'Test',
            'purchase_price' => 500,
            'selling_price' => 800,
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
            'description' => null,
            'is_active' => true,
        ]);

        $this->actingAs(
            \App\Models\User::factory()->create()
        )->post('/sales', [
            'customer_id' => $customer->id,
            'invoice_number' => 'INV-TEST-003',
            'sale_date' => now()->format('Y-m-d'),
            'status' => 'completed',
            'notes' => null,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 3,
                    'unit_price' => 800,
                ],
            ],
        ])->assertRedirect('/sales');

        $sale = Sale::where(
            'invoice_number',
            'INV-TEST-003'
        )->firstOrFail();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 7.00,
        ]);

        $response = $this->actingAs(
            \App\Models\User::factory()->create()
        )->put("/sales/{$sale->id}", [
            'customer_id' => $customer->id,
            'invoice_number' => 'INV-TEST-003',
            'sale_date' => now()->format('Y-m-d'),
            'status' => 'completed',
            'notes' => null,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 800,
                ],
            ],
        ]);

        $response->assertRedirect("/sales/{$sale->id}");

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 5.00,
        ]);

        $this->assertDatabaseHas('sale_items', [
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 5.00,
        ]);
    }

    public function test_cancelling_completed_sale_restores_product_stock(): void
    {
        $customer = Customer::create([
            'name' => 'Cancel Customer',
            'email' => 'cancel-sale@example.com',
            'phone' => '9800000003',
            'address' => 'Kathmandu',
        ]);

        $product = Product::create([
            'name' => 'Cancel Product',
            'sku' => 'TEST-SKU-004',
            'category' => 'Test',
            'purchase_price' => 500,
            'selling_price' => 800,
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
            'description' => null,
            'is_active' => true,
        ]);

        $this->actingAs(
            \App\Models\User::factory()->create()
        )->post('/sales', [
            'customer_id' => $customer->id,
            'invoice_number' => 'INV-TEST-004',
            'sale_date' => now()->format('Y-m-d'),
            'status' => 'completed',
            'notes' => null,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 4,
                    'unit_price' => 800,
                ],
            ],
        ])->assertRedirect('/sales');

        $sale = Sale::where(
            'invoice_number',
            'INV-TEST-004'
        )->firstOrFail();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 6.00,
        ]);

        $response = $this->actingAs(
            \App\Models\User::factory()->create()
        )->put("/sales/{$sale->id}", [
            'customer_id' => $customer->id,
            'invoice_number' => 'INV-TEST-004',
            'sale_date' => now()->format('Y-m-d'),
            'status' => 'cancelled',
            'notes' => null,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 4,
                    'unit_price' => 800,
                ],
            ],
        ]);

        $response->assertRedirect("/sales/{$sale->id}");

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 10.00,
        ]);

        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_deleting_completed_sale_restores_product_stock(): void
    {
        $customer = Customer::create([
            'name' => 'Delete Customer',
            'email' => 'delete-sale@example.com',
            'phone' => '9800000004',
            'address' => 'Kathmandu',
        ]);

        $product = Product::create([
            'name' => 'Delete Product',
            'sku' => 'TEST-SKU-005',
            'category' => 'Test',
            'purchase_price' => 500,
            'selling_price' => 800,
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
            'description' => null,
            'is_active' => true,
        ]);

        $this->actingAs(
            \App\Models\User::factory()->create()
        )->post('/sales', [
            'customer_id' => $customer->id,
            'invoice_number' => 'INV-TEST-005',
            'sale_date' => now()->format('Y-m-d'),
            'status' => 'completed',
            'notes' => null,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 3,
                    'unit_price' => 800,
                ],
            ],
        ])->assertRedirect('/sales');

        $sale = Sale::where(
            'invoice_number',
            'INV-TEST-005'
        )->firstOrFail();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 7.00,
        ]);

        $response = $this->actingAs(
            \App\Models\User::factory()->create()
        )->delete("/sales/{$sale->id}");

        $response->assertRedirect('/sales');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 10.00,
        ]);

        $this->assertDatabaseMissing('sales', [
            'id' => $sale->id,
        ]);
    }

    public function test_same_product_quantities_are_combined_for_stock_check(): void
    {
        $customer = Customer::create([
            'name' => 'Duplicate Product Customer',
            'email' => 'duplicate-sale@example.com',
            'phone' => '9800000005',
            'address' => 'Kathmandu',
        ]);

        $product = Product::create([
            'name' => 'Duplicate Product',
            'sku' => 'TEST-SKU-006',
            'category' => 'Test',
            'purchase_price' => 500,
            'selling_price' => 800,
            'stock_quantity' => 5,
            'low_stock_threshold' => 1,
            'description' => null,
            'is_active' => true,
        ]);

        $response = $this->actingAs(
            \App\Models\User::factory()->create()
        )->post('/sales', [
            'customer_id' => $customer->id,
            'invoice_number' => 'INV-TEST-006',
            'sale_date' => now()->format('Y-m-d'),
            'status' => 'completed',
            'notes' => null,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 3,
                    'unit_price' => 800,
                ],
                [
                    'product_id' => $product->id,
                    'quantity' => 3,
                    'unit_price' => 800,
                ],
            ],
        ]);

        $response->assertSessionHasErrors('items');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 5.00,
        ]);

        $this->assertDatabaseMissing('sales', [
            'invoice_number' => 'INV-TEST-006',
        ]);
    }
}