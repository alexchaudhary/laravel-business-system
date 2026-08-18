<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierBusinessLogicTest extends TestCase
{
    use RefreshDatabase;

    private function authenticatedUser(): User
    {
        return User::factory()->create();
    }

    public function test_supplier_can_be_created(): void
    {
        $user = $this->authenticatedUser();

        $response = $this
            ->actingAs($user)
            ->post('/suppliers', [
                'name' => 'ABC Suppliers',
                'company' => 'ABC Trading Pvt. Ltd.',
                'email' => 'supplier@example.com',
                'phone' => '9800000000',
                'address' => 'Kathmandu',
                'description' => 'Main supplier',
                'active' => true,
            ]);

        $response->assertRedirect('/suppliers');

        $this->assertDatabaseHas('suppliers', [
            'name' => 'ABC Suppliers',
            'company' => 'ABC Trading Pvt. Ltd.',
            'email' => 'supplier@example.com',
            'phone' => '9800000000',
            'address' => 'Kathmandu',
            'description' => 'Main supplier',
            'active' => 1,
        ]);
    }

    public function test_supplier_can_be_updated(): void
    {
        $user = $this->authenticatedUser();

        $supplier = Supplier::create([
            'name' => 'Old Supplier',
            'company' => 'Old Company',
            'email' => 'old@example.com',
            'phone' => '9800000000',
            'address' => 'Old Address',
            'description' => 'Old description',
            'active' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->put("/suppliers/{$supplier->id}", [
                'name' => 'Updated Supplier',
                'company' => 'Updated Company',
                'email' => 'updated@example.com',
                'phone' => '9811111111',
                'address' => 'Updated Address',
                'description' => 'Updated description',
                'active' => false,
            ]);

        $response->assertRedirect('/suppliers');

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'Updated Supplier',
            'company' => 'Updated Company',
            'email' => 'updated@example.com',
            'phone' => '9811111111',
            'address' => 'Updated Address',
            'description' => 'Updated description',
            'active' => 0,
        ]);
    }

    public function test_supplier_can_be_deleted(): void
    {
        $user = $this->authenticatedUser();

        $supplier = Supplier::create([
            'name' => 'Supplier To Delete',
            'phone' => '9800000000',
            'active' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->delete("/suppliers/{$supplier->id}");

        $response->assertRedirect('/suppliers');

        $this->assertDatabaseMissing('suppliers', [
            'id' => $supplier->id,
        ]);
    }

    public function test_supplier_requires_name(): void
    {
        $user = $this->authenticatedUser();

        $response = $this
            ->actingAs($user)
            ->post('/suppliers', [
                'name' => '',
                'phone' => '9800000000',
            ]);

        $response->assertSessionHasErrors('name');

        $this->assertDatabaseCount('suppliers', 0);
    }

    public function test_supplier_requires_phone(): void
    {
        $user = $this->authenticatedUser();

        $response = $this
            ->actingAs($user)
            ->post('/suppliers', [
                'name' => 'Supplier Without Phone',
                'phone' => '',
            ]);

        $response->assertSessionHasErrors('phone');

        $this->assertDatabaseCount('suppliers', 0);
    }

    public function test_supplier_name_cannot_exceed_255_characters(): void
    {
        $user = $this->authenticatedUser();

        $response = $this
            ->actingAs($user)
            ->post('/suppliers', [
                'name' => str_repeat('A', 256),
                'phone' => '9800000000',
            ]);

        $response->assertSessionHasErrors('name');

        $this->assertDatabaseCount('suppliers', 0);
    }

    public function test_supplier_phone_cannot_exceed_20_characters(): void
    {
        $user = $this->authenticatedUser();

        $response = $this
            ->actingAs($user)
            ->post('/suppliers', [
                'name' => 'Supplier',
                'phone' => str_repeat('9', 21),
            ]);

        $response->assertSessionHasErrors('phone');

        $this->assertDatabaseCount('suppliers', 0);
    }

    public function test_supplier_rejects_invalid_email(): void
    {
        $user = $this->authenticatedUser();

        $response = $this
            ->actingAs($user)
            ->post('/suppliers', [
                'name' => 'Supplier',
                'email' => 'invalid-email',
                'phone' => '9800000000',
            ]);

        $response->assertSessionHasErrors('email');

        $this->assertDatabaseCount('suppliers', 0);
    }

    public function test_supplier_email_is_optional(): void
    {
        $user = $this->authenticatedUser();

        $response = $this
            ->actingAs($user)
            ->post('/suppliers', [
                'name' => 'Supplier Without Email',
                'phone' => '9800000000',
            ]);

        $response->assertRedirect('/suppliers');

        $this->assertDatabaseHas('suppliers', [
            'name' => 'Supplier Without Email',
            'email' => null,
        ]);
    }

    public function test_supplier_optional_fields_can_be_null(): void
    {
        $user = $this->authenticatedUser();

        $response = $this
            ->actingAs($user)
            ->post('/suppliers', [
                'name' => 'Minimal Supplier',
                'company' => null,
                'email' => null,
                'phone' => '9800000000',
                'address' => null,
                'description' => null,
                'active' => null,
            ]);

        $response->assertRedirect('/suppliers');

        $this->assertDatabaseHas('suppliers', [
            'name' => 'Minimal Supplier',
            'company' => null,
            'email' => null,
            'phone' => '9800000000',
            'address' => null,
            'description' => null,
            'active' => 0,
        ]);
    }

    public function test_supplier_active_field_can_be_disabled(): void
    {
        $user = $this->authenticatedUser();

        $response = $this
            ->actingAs($user)
            ->post('/suppliers', [
                'name' => 'Inactive Supplier',
                'phone' => '9800000000',
                'active' => false,
            ]);

        $response->assertRedirect('/suppliers');

        $this->assertDatabaseHas('suppliers', [
            'name' => 'Inactive Supplier',
            'active' => 0,
        ]);
    }

    public function test_supplier_defaults_to_active_when_active_is_not_submitted(): void
    {
        $user = $this->authenticatedUser();

        $response = $this
            ->actingAs($user)
            ->post('/suppliers', [
                'name' => 'Default Active Supplier',
                'phone' => '9800000000',
            ]);

        $response->assertRedirect('/suppliers');

        $this->assertDatabaseHas('suppliers', [
            'name' => 'Default Active Supplier',
            'active' => 0,
        ]);
    }

    public function test_guest_cannot_create_supplier(): void
    {
        $response = $this->post('/suppliers', [
            'name' => 'Guest Supplier',
            'phone' => '9800000000',
        ]);

        $response->assertRedirect('/login');

        $this->assertDatabaseCount('suppliers', 0);
    }
}
