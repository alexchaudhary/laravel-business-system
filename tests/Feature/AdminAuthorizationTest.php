<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_routes(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $routes = [
            '/admin',
            '/users',
            '/expenses',
            '/purchases',
            '/inventory',
            '/reports',
            '/stock-adjustments',
        ];

        foreach ($routes as $route) {
            $this->get($route)->assertSuccessful();
        }
    }

    public function test_normal_user_cannot_access_admin_routes(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $this->actingAs($user);

        $routes = [
            '/admin',
            '/users',
            '/expenses',
            '/purchases',
            '/inventory',
            '/reports',
            '/stock-adjustments',
        ];

        foreach ($routes as $route) {
            $this->get($route)->assertForbidden();
        }
    }

    public function test_normal_user_cannot_create_user(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $this->actingAs($user)
            ->post(route('users.store'), [
                'name' => 'Unauthorized Admin',
                'email' => 'unauthorized@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'admin',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('users', [
            'email' => 'unauthorized@example.com',
        ]);
    }

    public function test_normal_user_cannot_update_user_role(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $targetUser = User::factory()->create([
            'role' => 'user',
        ]);

        $this->actingAs($user)
            ->put(route('users.update', $targetUser), [
                'name' => $targetUser->name,
                'email' => $targetUser->email,
                'password' => '',
                'password_confirmation' => '',
                'role' => 'admin',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'role' => 'user',
        ]);
    }

    public function test_normal_user_cannot_delete_user(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $targetUser = User::factory()->create([
            'role' => 'user',
        ]);

        $this->actingAs($user)
            ->delete(route('users.destroy', $targetUser))
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
        ]);
    }

    public function test_normal_user_cannot_create_expense(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $this->actingAs($user)
            ->post(route('expenses.store'), [
                'expense_date' => '2026-08-17',
                'title' => 'Unauthorized Expense',
                'category' => 'Office',
                'amount' => 1000,
                'description' => 'Should not be created',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('expenses', [
            'title' => 'Unauthorized Expense',
        ]);
    }

    public function test_normal_user_cannot_create_purchase(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $this->actingAs($user)
            ->get(route('purchases.create'))
            ->assertForbidden();
    }

    public function test_normal_user_cannot_access_inventory_actions(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $product = Product::create([
            'name' => 'Authorization Test Product',
            'sku' => 'AUTH-TEST-001',
            'purchase_price' => 100,
            'selling_price' => 150,
            'stock_quantity' => 10,
            'low_stock_threshold' => 5,
            'active' => true,
        ]);

        $this->actingAs($user)
            ->get(route('inventory.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('stock-adjustments.create', $product))
            ->assertForbidden();
    }

    public function test_normal_user_cannot_access_reports(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $this->actingAs($user)
            ->get(route('reports.index'))
            ->assertForbidden();
    }

    public function test_guest_cannot_access_admin_routes(): void
    {
        $routes = [
            '/admin',
            '/users',
            '/expenses',
            '/purchases',
            '/inventory',
            '/reports',
            '/stock-adjustments',
        ];

        foreach ($routes as $route) {
            $this->get($route)->assertRedirect(route('login'));
        }
    }
}
