<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerBusinessLogicTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_be_created(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/customers', [
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'phone' => '9800000001',
            'address' => 'Kathmandu',
        ]);

        $response->assertRedirect('/customers');
        $response->assertSessionHas('success', 'Customer created successfully.');

        $this->assertDatabaseHas('customers', [
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'phone' => '9800000001',
            'address' => 'Kathmandu',
        ]);
    }

    public function test_customer_can_be_updated(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $customer = Customer::create([
            'name' => 'Old Customer',
            'email' => 'old@example.com',
            'phone' => '9800000002',
            'address' => 'Pokhara',
        ]);

        $response = $this->actingAs($admin)->put("/customers/{$customer->id}", [
            'name' => 'Updated Customer',
            'email' => 'updated@example.com',
            'phone' => '9800000003',
            'address' => 'Kathmandu',
        ]);

        $response->assertRedirect('/customers');
        $response->assertSessionHas('success', 'Customer updated successfully.');

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Updated Customer',
            'email' => 'updated@example.com',
            'phone' => '9800000003',
            'address' => 'Kathmandu',
        ]);
    }

    public function test_customer_can_be_deleted(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $customer = Customer::create([
            'name' => 'Delete Customer',
            'email' => 'delete@example.com',
            'phone' => '9800000004',
            'address' => 'Lalitpur',
        ]);

        $response = $this->actingAs($admin)->delete("/customers/{$customer->id}");

        $response->assertRedirect('/customers');
        $response->assertSessionHas('success', 'Customer deleted successfully.');

        $this->assertDatabaseMissing('customers', [
            'id' => $customer->id,
        ]);
    }

    public function test_customer_requires_name(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/customers', [
            'name' => '',
            'email' => 'noname@example.com',
            'phone' => '9800000005',
            'address' => 'Kathmandu',
        ]);

        $response->assertSessionHasErrors([
            'name',
        ]);

        $this->assertDatabaseMissing('customers', [
            'email' => 'noname@example.com',
        ]);
    }

    public function test_customer_name_cannot_exceed_255_characters(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/customers', [
            'name' => str_repeat('A', 256),
            'email' => 'long-name@example.com',
            'phone' => '9800000006',
            'address' => 'Kathmandu',
        ]);

        $response->assertSessionHasErrors([
            'name',
        ]);

        $this->assertDatabaseMissing('customers', [
            'email' => 'long-name@example.com',
        ]);
    }

    public function test_customer_rejects_invalid_email(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/customers', [
            'name' => 'Invalid Email Customer',
            'email' => 'not-an-email',
            'phone' => '9800000007',
            'address' => 'Kathmandu',
        ]);

        $response->assertSessionHasErrors([
            'email',
        ]);

        $this->assertDatabaseMissing('customers', [
            'name' => 'Invalid Email Customer',
        ]);
    }

    public function test_customer_email_is_optional(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/customers', [
            'name' => 'No Email Customer',
            'email' => null,
            'phone' => '9800000008',
            'address' => 'Kathmandu',
        ]);

        $response->assertRedirect('/customers');

        $this->assertDatabaseHas('customers', [
            'name' => 'No Email Customer',
            'email' => null,
        ]);
    }

    public function test_customer_phone_cannot_exceed_20_characters(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/customers', [
            'name' => 'Long Phone Customer',
            'email' => 'long-phone@example.com',
            'phone' => str_repeat('9', 21),
            'address' => 'Kathmandu',
        ]);

        $response->assertSessionHasErrors([
            'phone',
        ]);

        $this->assertDatabaseMissing('customers', [
            'name' => 'Long Phone Customer',
        ]);
    }

    public function test_customer_address_cannot_exceed_500_characters(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/customers', [
            'name' => 'Long Address Customer',
            'email' => 'long-address@example.com',
            'phone' => '9800000009',
            'address' => str_repeat('A', 501),
        ]);

        $response->assertSessionHasErrors([
            'address',
        ]);

        $this->assertDatabaseMissing('customers', [
            'name' => 'Long Address Customer',
        ]);
    }

    public function test_customer_optional_fields_can_be_null(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/customers', [
            'name' => 'Minimal Customer',
            'email' => null,
            'phone' => null,
            'address' => null,
        ]);

        $response->assertRedirect('/customers');

        $this->assertDatabaseHas('customers', [
            'name' => 'Minimal Customer',
            'email' => null,
            'phone' => null,
            'address' => null,
        ]);
    }

    public function test_guest_cannot_create_customer(): void
    {
        $response = $this->post('/customers', [
            'name' => 'Guest Customer',
            'email' => 'guest@example.com',
            'phone' => '9800000010',
            'address' => 'Kathmandu',
        ]);

        $response->assertRedirect('/login');

        $this->assertDatabaseMissing('customers', [
            'email' => 'guest@example.com',
        ]);
    }
}