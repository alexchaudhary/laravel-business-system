<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductBusinessLogicTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_created(): void
    {
        $response = $this->actingAs(
            User::factory()->create([
                'role' => 'admin',
            ])
        )->post('/products', [
            'name' => 'Test Laptop',
            'sku' => 'PRODUCT-TEST-001',
            'category' => 'Electronics',
            'purchase_price' => 50000,
            'selling_price' => 65000,
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
            'description' => 'Test laptop product',
            'is_active' => true,
        ]);

        $response->assertRedirect('/products');

        $this->assertDatabaseHas('products', [
            'name' => 'Test Laptop',
            'sku' => 'PRODUCT-TEST-001',
            'purchase_price' => 50000.00,
            'selling_price' => 65000.00,
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
            'is_active' => true,
        ]);
    }

    public function test_product_can_be_updated(): void
    {
        $product = Product::create([
            'name' => 'Old Product',
            'sku' => 'PRODUCT-TEST-002',
            'category' => 'Old Category',
            'purchase_price' => 500,
            'selling_price' => 800,
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
            'description' => 'Old description',
            'is_active' => true,
        ]);

        $response = $this->actingAs(
            User::factory()->create([
                'role' => 'admin',
            ])
        )->put("/products/{$product->id}", [
            'name' => 'Updated Product',
            'sku' => 'PRODUCT-TEST-002',
            'category' => 'Updated Category',
            'purchase_price' => 600,
            'selling_price' => 900,
            'stock_quantity' => 20,
            'low_stock_threshold' => 5,
            'description' => 'Updated description',
            'is_active' => true,
        ]);

        $response->assertRedirect('/products');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product',
            'sku' => 'PRODUCT-TEST-002',
            'purchase_price' => 600.00,
            'selling_price' => 900.00,
            'stock_quantity' => 20,
            'low_stock_threshold' => 5,
            'description' => 'Updated description',
            'is_active' => true,
        ]);
    }

    public function test_product_can_be_deleted(): void
    {
        $product = Product::create([
            'name' => 'Delete Product',
            'sku' => 'PRODUCT-TEST-003',
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
        )->delete("/products/{$product->id}");

        $response->assertRedirect('/products');

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_product_requires_name(): void
    {
        $response = $this->actingAs(
            User::factory()->create([
                'role' => 'admin',
            ])
        )->post('/products', [
            'name' => '',
            'sku' => 'PRODUCT-TEST-004',
            'category' => 'Test',
            'purchase_price' => 500,
            'selling_price' => 800,
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
            'description' => null,
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors([
            'name',
        ]);

        $this->assertDatabaseMissing('products', [
            'sku' => 'PRODUCT-TEST-004',
        ]);
    }

    public function test_product_requires_sku(): void
    {
        $response = $this->actingAs(
            User::factory()->create([
                'role' => 'admin',
            ])
        )->post('/products', [
            'name' => 'Missing SKU Product',
            'sku' => '',
            'category' => 'Test',
            'purchase_price' => 500,
            'selling_price' => 800,
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
            'description' => null,
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors([
            'sku',
        ]);
    }

    public function test_duplicate_sku_is_rejected(): void
    {
        Product::create([
            'name' => 'Existing Product',
            'sku' => 'PRODUCT-TEST-005',
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
        )->post('/products', [
            'name' => 'Duplicate SKU Product',
            'sku' => 'PRODUCT-TEST-005',
            'category' => 'Test',
            'purchase_price' => 600,
            'selling_price' => 900,
            'stock_quantity' => 5,
            'low_stock_threshold' => 2,
            'description' => null,
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors([
            'sku',
        ]);
    }

    public function test_product_can_keep_same_sku_when_updated(): void
    {
        $product = Product::create([
            'name' => 'Same SKU Product',
            'sku' => 'PRODUCT-TEST-006',
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
        )->put("/products/{$product->id}", [
            'name' => 'Updated Same SKU Product',
            'sku' => 'PRODUCT-TEST-006',
            'category' => 'Updated',
            'purchase_price' => 550,
            'selling_price' => 850,
            'stock_quantity' => 12,
            'low_stock_threshold' => 3,
            'description' => null,
            'is_active' => true,
        ]);

        $response->assertRedirect('/products');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'sku' => 'PRODUCT-TEST-006',
            'name' => 'Updated Same SKU Product',
        ]);
    }

    public function test_negative_purchase_price_is_rejected(): void
    {
        $response = $this->actingAs(
            User::factory()->create([
                'role' => 'admin',
            ])
        )->post('/products', [
            'name' => 'Negative Purchase Price Product',
            'sku' => 'PRODUCT-TEST-007',
            'category' => 'Test',
            'purchase_price' => -1,
            'selling_price' => 800,
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
            'description' => null,
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors([
            'purchase_price',
        ]);

        $this->assertDatabaseMissing('products', [
            'sku' => 'PRODUCT-TEST-007',
        ]);
    }

    public function test_negative_selling_price_is_rejected(): void
    {
        $response = $this->actingAs(
            User::factory()->create([
                'role' => 'admin',
            ])
        )->post('/products', [
            'name' => 'Negative Selling Price Product',
            'sku' => 'PRODUCT-TEST-008',
            'category' => 'Test',
            'purchase_price' => 500,
            'selling_price' => -1,
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
            'description' => null,
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors([
            'selling_price',
        ]);

        $this->assertDatabaseMissing('products', [
            'sku' => 'PRODUCT-TEST-008',
        ]);
    }

    public function test_negative_stock_quantity_is_rejected(): void
    {
        $response = $this->actingAs(
            User::factory()->create([
                'role' => 'admin',
            ])
        )->post('/products', [
            'name' => 'Negative Stock Product',
            'sku' => 'PRODUCT-TEST-009',
            'category' => 'Test',
            'purchase_price' => 500,
            'selling_price' => 800,
            'stock_quantity' => -1,
            'low_stock_threshold' => 2,
            'description' => null,
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors([
            'stock_quantity',
        ]);

        $this->assertDatabaseMissing('products', [
            'sku' => 'PRODUCT-TEST-009',
        ]);
    }

    public function test_negative_low_stock_threshold_is_rejected(): void
    {
        $response = $this->actingAs(
            User::factory()->create([
                'role' => 'admin',
            ])
        )->post('/products', [
            'name' => 'Negative Threshold Product',
            'sku' => 'PRODUCT-TEST-010',
            'category' => 'Test',
            'purchase_price' => 500,
            'selling_price' => 800,
            'stock_quantity' => 10,
            'low_stock_threshold' => -1,
            'description' => null,
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors([
            'low_stock_threshold',
        ]);

        $this->assertDatabaseMissing('products', [
            'sku' => 'PRODUCT-TEST-010',
        ]);
    }

    public function test_zero_prices_and_stock_are_allowed(): void
    {
        $response = $this->actingAs(
            User::factory()->create([
                'role' => 'admin',
            ])
        )->post('/products', [
            'name' => 'Zero Value Product',
            'sku' => 'PRODUCT-TEST-011',
            'category' => 'Test',
            'purchase_price' => 0,
            'selling_price' => 0,
            'stock_quantity' => 0,
            'low_stock_threshold' => 0,
            'description' => null,
            'is_active' => true,
        ]);

        $response->assertRedirect('/products');

        $this->assertDatabaseHas('products', [
            'name' => 'Zero Value Product',
            'sku' => 'PRODUCT-TEST-011',
            'purchase_price' => 0.00,
            'selling_price' => 0.00,
            'stock_quantity' => 0,
            'low_stock_threshold' => 0,
        ]);
    }

    public function test_product_can_be_deactivated(): void
    {
        $product = Product::create([
            'name' => 'Active Product',
            'sku' => 'PRODUCT-TEST-012',
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
        )->put("/products/{$product->id}", [
            'name' => 'Active Product',
            'sku' => 'PRODUCT-TEST-012',
            'category' => 'Test',
            'purchase_price' => 500,
            'selling_price' => 800,
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
            'description' => null,
            'is_active' => false,
        ]);

        $response->assertRedirect('/products');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'is_active' => false,
        ]);
    }

    public function test_product_name_cannot_exceed_255_characters(): void
    {
        $response = $this->actingAs(
            User::factory()->create([
                'role' => 'admin',
            ])
        )->post('/products', [
            'name' => str_repeat('A', 256),
            'sku' => 'PRODUCT-TEST-013',
            'category' => 'Test',
            'purchase_price' => 500,
            'selling_price' => 800,
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
            'description' => null,
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors([
            'name',
        ]);

        $this->assertDatabaseMissing('products', [
            'sku' => 'PRODUCT-TEST-013',
        ]);
    }
}

