<?php

namespace Tests\Feature;

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
}