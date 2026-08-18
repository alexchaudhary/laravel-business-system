<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryBusinessLogicTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
        ]);
    }

    private function createProduct(
        string $name,
        string $sku,
        float $purchasePrice,
        float $stockQuantity,
        float $lowStockThreshold = 5
    ): Product {
        return Product::create([
            'name' => $name,
            'sku' => $sku,
            'category' => 'Test Category',
            'purchase_price' => $purchasePrice,
            'selling_price' => $purchasePrice * 1.2,
            'stock_quantity' => $stockQuantity,
            'low_stock_threshold' => $lowStockThreshold,
            'description' => null,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_access_inventory(): void
    {
        $response = $this->actingAs($this->admin())
            ->get('/inventory');

        $response->assertStatus(200);
    }

    public function test_guest_cannot_access_inventory(): void
    {
        $response = $this->get('/inventory');

        $response->assertRedirect('/login');
    }

    public function test_normal_user_cannot_access_inventory(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)
            ->get('/inventory');

        $response->assertStatus(403);
    }

    public function test_inventory_counts_total_products(): void
    {
        $this->createProduct(
            'Laptop',
            'INV-001',
            50000,
            10
        );

        $this->createProduct(
            'Mouse',
            'INV-002',
            1000,
            20
        );

        $response = $this->actingAs($this->admin())
            ->get('/inventory');

        $response
            ->assertStatus(200)
            ->assertViewHas('totalProducts', 2);
    }

    public function test_inventory_calculates_total_stock_units(): void
    {
        $this->createProduct(
            'Laptop',
            'INV-003',
            50000,
            10
        );

        $this->createProduct(
            'Mouse',
            'INV-004',
            1000,
            20
        );

        $response = $this->actingAs($this->admin())
            ->get('/inventory');

        $response
            ->assertViewHas('totalStockUnits', 30);
    }

    public function test_product_is_low_stock_when_stock_is_at_threshold(): void
    {
        $product = $this->createProduct(
            'Laptop',
            'INV-005',
            50000,
            5,
            5
        );

        $response = $this->actingAs($this->admin())
            ->get('/inventory');

        $response->assertViewHas('lowStockProducts', function ($products) use ($product) {
            return $products->contains('id', $product->id);
        });
    }

    public function test_product_is_low_stock_when_stock_is_below_threshold(): void
    {
        $product = $this->createProduct(
            'Laptop',
            'INV-006',
            50000,
            3,
            5
        );

        $response = $this->actingAs($this->admin())
            ->get('/inventory');

        $response->assertViewHas('lowStockProducts', function ($products) use ($product) {
            return $products->contains('id', $product->id);
        });
    }

    public function test_product_is_not_low_stock_when_stock_is_above_threshold(): void
    {
        $product = $this->createProduct(
            'Laptop',
            'INV-007',
            50000,
            10,
            5
        );

        $response = $this->actingAs($this->admin())
            ->get('/inventory');

        $response->assertViewHas('lowStockProducts', function ($products) use ($product) {
            return ! $products->contains('id', $product->id);
        });
    }

    public function test_zero_stock_product_is_not_in_low_stock_list(): void
    {
        $product = $this->createProduct(
            'Laptop',
            'INV-008',
            50000,
            0,
            5
        );

        $response = $this->actingAs($this->admin())
            ->get('/inventory');

        $response->assertViewHas('lowStockProducts', function ($products) use ($product) {
            return ! $products->contains('id', $product->id);
        });
    }

    public function test_inventory_calculates_total_stock_value_using_purchase_price(): void
    {
        $this->createProduct(
            'Laptop',
            'INV-009',
            50000,
            2
        );

        $this->createProduct(
            'Mouse',
            'INV-010',
            1000,
            5
        );

        $response = $this->actingAs($this->admin())
            ->get('/inventory');

        $response
            ->assertViewHas('totalStockValue', 105000);
    }

    public function test_inventory_uses_each_products_own_low_stock_threshold(): void
    {
        $lowStockProduct = $this->createProduct(
            'Laptop',
            'INV-011',
            50000,
            7,
            7
        );

        $normalStockProduct = $this->createProduct(
            'Mouse',
            'INV-012',
            1000,
            7,
            5
        );

        $response = $this->actingAs($this->admin())
            ->get('/inventory');

        $response->assertViewHas('lowStockProducts', function ($products) use (
            $lowStockProduct,
            $normalStockProduct
        ) {
            return $products->contains('id', $lowStockProduct->id)
                && ! $products->contains('id', $normalStockProduct->id);
        });
    }

    public function test_inventory_displays_products_sorted_by_name(): void
    {
        $this->createProduct(
            'Zebra Product',
            'INV-013',
            1000,
            10
        );

        $this->createProduct(
            'Apple Product',
            'INV-014',
            1000,
            10
        );

        $response = $this->actingAs($this->admin())
            ->get('/inventory');

        $response->assertViewHas('products', function ($products) {
            return $products->first()->name === 'Apple Product'
                && $products->last()->name === 'Zebra Product';
        });
    }

    public function test_inventory_handles_multiple_low_stock_products(): void
    {
        $product1 = $this->createProduct(
            'Laptop',
            'INV-015',
            50000,
            2,
            5
        );

        $product2 = $this->createProduct(
            'Keyboard',
            'INV-016',
            3000,
            4,
            5
        );

        $normalProduct = $this->createProduct(
            'Monitor',
            'INV-017',
            20000,
            20,
            5
        );

        $response = $this->actingAs($this->admin())
            ->get('/inventory');

        $response->assertViewHas('lowStockProducts', function ($products) use (
            $product1,
            $product2,
            $normalProduct
        ) {
            return $products->contains('id', $product1->id)
                && $products->contains('id', $product2->id)
                && ! $products->contains('id', $normalProduct->id);
        });
    }
}