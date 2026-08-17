<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceBusinessLogicTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_can_be_created_for_a_sale(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'customer@test.com',
            'phone' => '9800000000',
            'address' => 'Test Address',
        ]);

        $sale = Sale::create([
            'customer_id' => $customer->id,
            'invoice_number' => 'SALE-001',
            'sale_date' => '2026-08-17',
            'total_amount' => 250000,
            'status' => 'completed',
            'notes' => null,
        ]);

        $this->actingAs($admin);

        $response = $this->post('/invoices', [
            'sale_id' => $sale->id,
            'invoice_number' => 'INV-TEST-001',
            'invoice_date' => '2026-08-17',
            'due_date' => '2026-08-31',
            'subtotal' => 250000,
            'discount' => 0,
            'tax' => 0,
            'total_amount' => 250000,
            'status' => 'issued',
            'notes' => 'Test invoice',
        ]);

        $response
            ->assertRedirect('/invoices');

        $this->assertDatabaseHas('invoices', [
            'sale_id' => $sale->id,
            'invoice_number' => 'INV-TEST-001',
            'total_amount' => 250000,
            'status' => 'issued',
        ]);
    }

    public function test_invoice_rejects_due_date_before_invoice_date(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'customer2@test.com',
            'phone' => '9800000001',
            'address' => 'Test Address',
        ]);

        $sale = Sale::create([
            'customer_id' => $customer->id,
            'invoice_number' => 'SALE-002',
            'sale_date' => '2026-08-17',
            'total_amount' => 100000,
            'status' => 'completed',
            'notes' => null,
        ]);

        $this->actingAs($admin);

        $response = $this->post('/invoices', [
            'sale_id' => $sale->id,
            'invoice_number' => 'INV-TEST-002',
            'invoice_date' => '2026-08-17',
            'due_date' => '2026-08-10',
            'subtotal' => 100000,
            'discount' => 0,
            'tax' => 0,
            'total_amount' => 100000,
            'status' => 'issued',
            'notes' => null,
        ]);

        $response
            ->assertSessionHasErrors('due_date');

        $this->assertDatabaseMissing('invoices', [
            'invoice_number' => 'INV-TEST-002',
        ]);
    }

    public function test_invoice_rejects_invalid_status(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'customer3@test.com',
            'phone' => '9800000002',
            'address' => 'Test Address',
        ]);

        $sale = Sale::create([
            'customer_id' => $customer->id,
            'invoice_number' => 'SALE-003',
            'sale_date' => '2026-08-17',
            'total_amount' => 50000,
            'status' => 'completed',
            'notes' => null,
        ]);

        $this->actingAs($admin);

        $response = $this->post('/invoices', [
            'sale_id' => $sale->id,
            'invoice_number' => 'INV-TEST-003',
            'invoice_date' => '2026-08-17',
            'due_date' => '2026-08-31',
            'subtotal' => 50000,
            'discount' => 0,
            'tax' => 0,
            'total_amount' => 50000,
            'status' => 'invalid_status',
            'notes' => null,
        ]);

        $response
            ->assertSessionHasErrors('status');

        $this->assertDatabaseMissing('invoices', [
            'invoice_number' => 'INV-TEST-003',
        ]);
    }
}