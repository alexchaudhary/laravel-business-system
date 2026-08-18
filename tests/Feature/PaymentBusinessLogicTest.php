<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentBusinessLogicTest extends TestCase
{
    use RefreshDatabase;

    private function createInvoice(
        string $saleNumber,
        string $invoiceNumber,
        float $totalAmount = 100000
    ): Invoice {
        $customer = Customer::create([
            'name' => 'Payment Test Customer',
            'email' => $invoiceNumber . '@test.com',
            'phone' => '9800000000',
            'address' => 'Test Address',
        ]);

        $sale = Sale::create([
            'customer_id' => $customer->id,
            'invoice_number' => $saleNumber,
            'sale_date' => now()->toDateString(),
            'total_amount' => $totalAmount,
            'status' => 'completed',
            'notes' => null,
        ]);

        return Invoice::create([
            'sale_id' => $sale->id,
            'invoice_number' => $invoiceNumber,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'subtotal' => $totalAmount,
            'discount' => 0,
            'tax' => 0,
            'total_amount' => $totalAmount,
            'status' => 'issued',
            'notes' => null,
        ]);
    }

    private function admin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
        ]);
    }

    public function test_payment_can_be_created_for_an_invoice(): void
    {
        $invoice = $this->createInvoice(
            'SALE-PAY-001',
            'INV-PAY-001',
            250000
        );

        $response = $this->actingAs($this->admin())
            ->post('/payments', [
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
            ->assertSessionHas(
                'success',
                'Payment created successfully.'
            );

        $this->assertDatabaseHas('payments', [
            'invoice_id' => $invoice->id,
            'payment_number' => 'PAY-TEST-001',
            'amount' => 50000,
            'payment_method' => 'cash',
        ]);
    }

    public function test_payment_amount_must_be_greater_than_zero(): void
    {
        $invoice = $this->createInvoice(
            'SALE-PAY-002',
            'INV-PAY-002',
            100000
        );

        $response = $this->actingAs($this->admin())
            ->post('/payments', [
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

    public function test_partial_payment_updates_invoice_status(): void
    {
        $invoice = $this->createInvoice(
            'SALE-PAY-003',
            'INV-PAY-003',
            100000
        );

        $this->actingAs($this->admin())
            ->post('/payments', [
                'invoice_id' => $invoice->id,
                'payment_number' => 'PAY-TEST-003',
                'payment_date' => now()->toDateString(),
                'amount' => 40000,
                'payment_method' => 'cash',
                'reference_number' => null,
                'notes' => null,
            ]);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'partially_paid',
        ]);
    }

    public function test_full_payment_updates_invoice_status_to_paid(): void
    {
        $invoice = $this->createInvoice(
            'SALE-PAY-004',
            'INV-PAY-004',
            100000
        );

        $this->actingAs($this->admin())
            ->post('/payments', [
                'invoice_id' => $invoice->id,
                'payment_number' => 'PAY-TEST-004',
                'payment_date' => now()->toDateString(),
                'amount' => 100000,
                'payment_method' => 'cash',
                'reference_number' => null,
                'notes' => null,
            ]);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'paid',
        ]);
    }

    public function test_deleting_full_payment_changes_invoice_status_back_to_issued(): void
    {
        $invoice = $this->createInvoice(
            'SALE-PAY-005',
            'INV-PAY-005',
            100000
        );

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'payment_number' => 'PAY-TEST-005',
            'payment_date' => now()->toDateString(),
            'amount' => 100000,
            'payment_method' => 'cash',
            'reference_number' => null,
            'notes' => null,
        ]);

        $invoice->updatePaymentStatus();

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'paid',
        ]);

        $this->actingAs($this->admin())
            ->delete("/payments/{$payment->id}");

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'issued',
        ]);

        $this->assertDatabaseMissing('payments', [
            'id' => $payment->id,
        ]);
    }

    public function test_updating_payment_recalculates_invoice_status(): void
    {
        $invoice = $this->createInvoice(
            'SALE-PAY-006',
            'INV-PAY-006',
            100000
        );

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'payment_number' => 'PAY-TEST-006',
            'payment_date' => now()->toDateString(),
            'amount' => 40000,
            'payment_method' => 'cash',
            'reference_number' => null,
            'notes' => null,
        ]);

        $invoice->updatePaymentStatus();

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'partially_paid',
        ]);

        $this->actingAs($this->admin())
            ->put("/payments/{$payment->id}", [
                'invoice_id' => $invoice->id,
                'payment_number' => 'PAY-TEST-006',
                'payment_date' => now()->toDateString(),
                'amount' => 100000,
                'payment_method' => 'cash',
                'reference_number' => null,
                'notes' => null,
            ]);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'paid',
        ]);
    }
}