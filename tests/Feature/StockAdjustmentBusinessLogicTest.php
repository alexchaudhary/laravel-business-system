<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockAdjustmentBusinessLogicTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
        ]);
    }

    private function user(): User
    {
        return User::factory()->create([
            'role' => 'user',
        ]);
    }

    private function createProduct(
        string $name = 'Test Product',
        float $stock = 100,
        float $purchasePrice = 500
    ): Product {
        return Product::create([
            'name' => $name,
            'sku' => 'SKU-' . uniqid(),
            'category' => 'Test Category',
            'purchase_price' => $purchasePrice,
            'selling_price' => 750,
            'stock_quantity' => $stock,
            'low_stock_threshold' => 10,
            'description' => 'Test product',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_access_inventory(): void
    {
        $this->actingAs($this->admin())
            ->get('/inventory')
            ->assertOk();
    }

    public function test_admin_can_access_stock_adjustment_form(): void
    {
        $product = $this->createProduct();

        $this->actingAs($this->admin())
            ->get("/inventory/{$product->id}/adjust-stock")
            ->assertOk();
    }

    public function test_guest_cannot_access_stock_adjustment_form(): void
    {
        $product = $this->createProduct();

        $this->get("/inventory/{$product->id}/adjust-stock")
            ->assertRedirect('/login');
    }

    public function test_normal_user_cannot_access_stock_adjustment_form(): void
    {
        $product = $this->createProduct();

        $this->actingAs($this->user())
            ->get("/inventory/{$product->id}/adjust-stock")
            ->assertForbidden();
    }

    public function test_admin_can_increase_product_stock(): void
    {
        $product = $this->createProduct(
            stock: 100
        );

        $response = $this->actingAs($this->admin())
            ->post(
                "/inventory/{$product->id}/adjust-stock",
                [
                    'type' => 'increase',
                    'quantity' => 25,
                    'reason' => 'Stock received',
                ]
            );

        $response
            ->assertRedirect('/inventory')
            ->assertSessionHas(
                'success',
                'Stock adjusted successfully.'
            );

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 125,
        ]);

        $this->assertDatabaseHas('stock_adjustments', [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 25,
            'stock_before' => 100,
            'stock_after' => 125,
            'note' => 'Stock received',
        ]);
    }

    public function test_admin_can_decrease_product_stock(): void
    {
        $product = $this->createProduct(
            stock: 100
        );

        $response = $this->actingAs($this->admin())
            ->post(
                "/inventory/{$product->id}/adjust-stock",
                [
                    'type' => 'decrease',
                    'quantity' => 30,
                    'reason' => 'Damaged stock',
                ]
            );

        $response
            ->assertRedirect('/inventory')
            ->assertSessionHas(
                'success',
                'Stock adjusted successfully.'
            );

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 70,
        ]);

        $this->assertDatabaseHas('stock_adjustments', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 30,
            'stock_before' => 100,
            'stock_after' => 70,
            'note' => 'Damaged stock',
        ]);
    }

    public function test_increase_adjustment_saves_correct_stock_before_and_after(): void
    {
        $product = $this->createProduct(
            stock: 45
        );

        $this->actingAs($this->admin())
            ->post(
                "/inventory/{$product->id}/adjust-stock",
                [
                    'type' => 'increase',
                    'quantity' => 15,
                    'reason' => 'Manual stock correction',
                ]
            );

        $this->assertDatabaseHas('stock_adjustments', [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 15,
            'stock_before' => 45,
            'stock_after' => 60,
        ]);
    }

    public function test_decrease_adjustment_saves_correct_stock_before_and_after(): void
    {
        $product = $this->createProduct(
            stock: 80
        );

        $this->actingAs($this->admin())
            ->post(
                "/inventory/{$product->id}/adjust-stock",
                [
                    'type' => 'decrease',
                    'quantity' => 20,
                    'reason' => 'Stock correction',
                ]
            );

        $this->assertDatabaseHas('stock_adjustments', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 20,
            'stock_before' => 80,
            'stock_after' => 60,
        ]);
    }

    public function test_decrease_cannot_be_greater_than_available_stock(): void
    {
        $product = $this->createProduct(
            stock: 50
        );

        $response = $this->actingAs($this->admin())
            ->post(
                "/inventory/{$product->id}/adjust-stock",
                [
                    'type' => 'decrease',
                    'quantity' => 51,
                    'reason' => 'Invalid decrease',
                ]
            );

        $response->assertSessionHasErrors('quantity');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 50,
        ]);

        $this->assertDatabaseMissing('stock_adjustments', [
            'product_id' => $product->id,
        ]);
    }

    public function test_decrease_can_reduce_stock_to_zero(): void
    {
        $product = $this->createProduct(
            stock: 50
        );

        $this->actingAs($this->admin())
            ->post(
                "/inventory/{$product->id}/adjust-stock",
                [
                    'type' => 'decrease',
                    'quantity' => 50,
                    'reason' => 'All stock removed',
                ]
            )
            ->assertRedirect('/inventory');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 0,
        ]);

        $this->assertDatabaseHas('stock_adjustments', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 50,
            'stock_before' => 50,
            'stock_after' => 0,
        ]);
    }

    public function test_quantity_is_required(): void
    {
        $product = $this->createProduct();

        $response = $this->actingAs($this->admin())
            ->post(
                "/inventory/{$product->id}/adjust-stock",
                [
                    'type' => 'increase',
                    'reason' => 'Missing quantity',
                ]
            );

        $response->assertSessionHasErrors('quantity');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 100,
        ]);
    }

    public function test_zero_quantity_is_rejected(): void
    {
        $product = $this->createProduct();

        $response = $this->actingAs($this->admin())
            ->post(
                "/inventory/{$product->id}/adjust-stock",
                [
                    'type' => 'increase',
                    'quantity' => 0,
                    'reason' => 'Zero quantity',
                ]
            );

        $response->assertSessionHasErrors('quantity');

        $this->assertDatabaseMissing('stock_adjustments', [
            'product_id' => $product->id,
        ]);
    }

    public function test_negative_quantity_is_rejected(): void
    {
        $product = $this->createProduct();

        $response = $this->actingAs($this->admin())
            ->post(
                "/inventory/{$product->id}/adjust-stock",
                [
                    'type' => 'increase',
                    'quantity' => -10,
                    'reason' => 'Negative quantity',
                ]
            );

        $response->assertSessionHasErrors('quantity');

        $this->assertDatabaseMissing('stock_adjustments', [
            'product_id' => $product->id,
        ]);
    }

    public function test_type_is_required(): void
    {
        $product = $this->createProduct();

        $response = $this->actingAs($this->admin())
            ->post(
                "/inventory/{$product->id}/adjust-stock",
                [
                    'quantity' => 10,
                    'reason' => 'Missing type',
                ]
            );

        $response->assertSessionHasErrors('type');

        $this->assertDatabaseMissing('stock_adjustments', [
            'product_id' => $product->id,
        ]);
    }

    public function test_invalid_adjustment_type_is_rejected(): void
    {
        $product = $this->createProduct();

        $response = $this->actingAs($this->admin())
            ->post(
                "/inventory/{$product->id}/adjust-stock",
                [
                    'type' => 'invalid',
                    'quantity' => 10,
                    'reason' => 'Invalid type',
                ]
            );

        $response->assertSessionHasErrors('type');

        $this->assertDatabaseMissing('stock_adjustments', [
            'product_id' => $product->id,
        ]);
    }

    public function test_reason_is_optional(): void
    {
        $product = $this->createProduct(
            stock: 100
        );

        $this->actingAs($this->admin())
            ->post(
                "/inventory/{$product->id}/adjust-stock",
                [
                    'type' => 'increase',
                    'quantity' => 10,
                ]
            )
            ->assertRedirect('/inventory');

        $this->assertDatabaseHas('stock_adjustments', [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 10,
            'stock_before' => 100,
            'stock_after' => 110,
            'note' => null,
        ]);
    }

    public function test_reason_cannot_exceed_500_characters(): void
    {
        $product = $this->createProduct();

        $response = $this->actingAs($this->admin())
            ->post(
                "/inventory/{$product->id}/adjust-stock",
                [
                    'type' => 'increase',
                    'quantity' => 10,
                    'reason' => str_repeat('A', 501),
                ]
            );

        $response->assertSessionHasErrors('reason');

        $this->assertDatabaseMissing('stock_adjustments', [
            'product_id' => $product->id,
        ]);
    }

    public function test_normal_user_cannot_create_stock_adjustment(): void
    {
        $product = $this->createProduct();

        $response = $this->actingAs($this->user())
            ->post(
                "/inventory/{$product->id}/adjust-stock",
                [
                    'type' => 'increase',
                    'quantity' => 10,
                    'reason' => 'Unauthorized adjustment',
                ]
            );

        $response->assertForbidden();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 100,
        ]);

        $this->assertDatabaseMissing('stock_adjustments', [
            'product_id' => $product->id,
        ]);
    }

    public function test_guest_cannot_create_stock_adjustment(): void
    {
        $product = $this->createProduct();

        $response = $this->post(
            "/inventory/{$product->id}/adjust-stock",
            [
                'type' => 'increase',
                'quantity' => 10,
                'reason' => 'Guest adjustment',
            ]
        );

        $response->assertRedirect('/login');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 100,
        ]);

        $this->assertDatabaseMissing('stock_adjustments', [
            'product_id' => $product->id,
        ]);
    }

    public function test_multiple_adjustments_update_stock_correctly(): void
    {
        $product = $this->createProduct(
            stock: 100
        );

        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(
                "/inventory/{$product->id}/adjust-stock",
                [
                    'type' => 'increase',
                    'quantity' => 25,
                    'reason' => 'First adjustment',
                ]
            );

        $this->actingAs($admin)
            ->post(
                "/inventory/{$product->id}/adjust-stock",
                [
                    'type' => 'decrease',
                    'quantity' => 40,
                    'reason' => 'Second adjustment',
                ]
            );

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 85,
        ]);

        $this->assertDatabaseCount('stock_adjustments', 2);

        $this->assertDatabaseHas('stock_adjustments', [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 25,
            'stock_before' => 100,
            'stock_after' => 125,
        ]);

        $this->assertDatabaseHas('stock_adjustments', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 40,
            'stock_before' => 125,
            'stock_after' => 85,
        ]);
    }

    public function test_adjustments_are_stored_for_correct_product(): void
    {
        $product1 = $this->createProduct(
            name: 'Product One',
            stock: 100
        );

        $product2 = $this->createProduct(
            name: 'Product Two',
            stock: 200
        );

        $this->actingAs($this->admin())
            ->post(
                "/inventory/{$product1->id}/adjust-stock",
                [
                    'type' => 'increase',
                    'quantity' => 30,
                    'reason' => 'Product one adjustment',
                ]
            );

        $this->assertDatabaseHas('products', [
            'id' => $product1->id,
            'stock_quantity' => 130,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product2->id,
            'stock_quantity' => 200,
        ]);

        $this->assertDatabaseHas('stock_adjustments', [
            'product_id' => $product1->id,
            'quantity' => 30,
            'type' => 'in',
        ]);

        $this->assertDatabaseMissing('stock_adjustments', [
            'product_id' => $product2->id,
        ]);
    }

    public function test_stock_adjustment_history_page_is_accessible_to_admin(): void
    {
        $this->actingAs($this->admin())
            ->get('/stock-adjustments')
            ->assertOk();
    }

    public function test_guest_cannot_access_stock_adjustment_history(): void
    {
        $this->get('/stock-adjustments')
            ->assertRedirect('/login');
    }

    public function test_normal_user_cannot_access_stock_adjustment_history(): void
    {
        $this->actingAs($this->user())
            ->get('/stock-adjustments')
            ->assertForbidden();
    }
}