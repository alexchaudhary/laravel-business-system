<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentBusinessLogicTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_can_be_created_for_an_invoice(): void
    {
        $customer = Customer::create([
            'name' => 'Payment Test Customer',
            'email' => 'payment@test.com',
            'phone' => '9800000000',
            'address' => 'Test Address',
        ]);

        $sale = Sale::create([
            'customer_id' => $customer->id,
            'invoice_number' => 'SALE-PAY-001',
            'sale_date' => now()->toDateString(),
            'total_amount' => 250000,
            'status' => 'completed',
            'notes' => null,
        ]);

        $invoice = Invoice::create([
            'sale_id' => $sale->id,
            'invoice_number' => 'INV-PAY-001',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'subtotal' => 250000,
            'discount' => 0,
            'tax' => 0,
            'total_amount' => 250000,
            'status' => 'issued',
            'notes' => null,
        ]);

        $response = $this->actingAs(
            \App\Models\User::factory()->create([
                'role' => 'admin',
            ])
        )->post('/payments', [
            'invoice_id' => $invoice->id,
            'payment_number' => 'PAY-TEST-001',
            'payment_date' => now()->toDateString(),
            'amount' => 50000,
            'payment_method' => 'cash',
            'reference_number' => null,
            'notes' => 'Business logic payment test',
        ]);

        $response
            ->assertRedirect('/payments')
            ->assertSessionHas('success', 'Payment created successfully.');

        $this->assertDatabaseHas('payments', [
            'invoice_id' => $invoice->id,
            'payment_number' => 'PAY-TEST-001',
            'amount' => 50000,
            'payment_method' => 'cash',
        ]);
    }

    public function test_payment_amount_must_be_greater_than_zero(): void
    {
        $customer = Customer::create([
            'name' => 'Payment Validation Customer',
            'email' => 'validation@test.com',
            'phone' => '9811111111',
            'address' => 'Test Address',
        ]);

        $sale = Sale::create([
            'customer_id' => $customer->id,
            'invoice_number' => 'SALE-PAY-002',
            'sale_date' => now()->toDateString(),
            'total_amount' => 100000,
            'status' => 'completed',
            'notes' => null,
        ]);

        $invoice = Invoice::create([
            'sale_id' => $sale->id,
            'invoice_number' => 'INV-PAY-002',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'subtotal' => 100000,
            'discount' => 0,
            'tax' => 0,
            'total_amount' => 100000,
            'status' => 'issued',
            'notes' => null,
        ]);

        $response = $this->actingAs(
            \App\Models\User::factory()->create([
                'role' => 'admin',
            ])
        )->post('/payments', [
            'invoice_id' => $invoice->id,
            'payment_number' => 'PAY-TEST-002',
            'payment_date' => now()->toDateString(),
            'amount' => 0,
            'payment_method' => 'cash',
            'reference_number' => null,
            'notes' => null,
        ]);

        $response->assertSessionHasErrors('amount');

        $this->assertDatabaseMissing('payments', [
            'payment_number' => 'PAY-TEST-002',
        ]);
    }
}