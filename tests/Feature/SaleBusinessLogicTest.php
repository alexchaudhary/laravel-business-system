<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
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
            'name' => 'Test Customer',
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
}