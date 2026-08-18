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

    private function validPaymentData(
        int $invoiceId,
        string $paymentNumber = 'PAY-TEST-001'
    ): array {
        return [
            'invoice_id' => $invoiceId,
            'payment_number' => $paymentNumber,
            'payment_date' => now()->toDateString(),
            'amount' => 50000,
            'payment_method' => 'cash',
            'reference_number' => null,
            'notes' => null,
        ];
    }

    public function test_payment_can_be_created_for_an_invoice(): void
    {
        $invoice = $this->createInvoice(
            'SALE-PAY-001',
            'INV-PAY-001',
            250000
        );

        $response = $this->actingAs($this->admin())
            ->post(route('payments.store'), $this->validPaymentData(
                $invoice->id,
                'PAY-TEST-001'
            ));

        $response
            ->assertRedirect(route('payments.index'))
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

    public function test_partial_payment_updates_invoice_status(): void
    {
        $invoice = $this->createInvoice(
            'SALE-PAY-002',
            'INV-PAY-002',
            100000
        );

        $this->actingAs($this->admin())
            ->post(route('payments.store'), [
                ...$this->validPaymentData(
                    $invoice->id,
                    'PAY-TEST-002'
                ),
                'amount' => 40000,
            ]);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'partially_paid',
        ]);
    }

    public function test_full_payment_updates_invoice_status_to_paid(): void
    {
        $invoice = $this->createInvoice(
            'SALE-PAY-003',
            'INV-PAY-003',
            100000
        );

        $this->actingAs($this->admin())
            ->post(route('payments.store'), [
                ...$this->validPaymentData(
                    $invoice->id,
                    'PAY-TEST-003'
                ),
                'amount' => 100000,
            ]);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'paid',
        ]);
    }

    public function test_payment_amount_must_be_greater_than_zero(): void
    {
        $invoice = $this->createInvoice(
            'SALE-PAY-004',
            'INV-PAY-004'
        );

        foreach ([0, -1] as $amount) {
            $data = $this->validPaymentData(
                $invoice->id,
                'PAY-AMOUNT-' . abs($amount)
            );

            $data['amount'] = $amount;

            $response = $this->actingAs($this->admin())
                ->post(route('payments.store'), $data);

            $response->assertSessionHasErrors('amount');
        }

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_payment_requires_invoice(): void
    {
        $this->actingAs($this->admin());

        $data = [
            'payment_number' => 'PAY-MISSING-INVOICE',
            'payment_date' => now()->toDateString(),
            'amount' => 50000,
            'payment_method' => 'cash',
            'reference_number' => null,
            'notes' => null,
        ];

        $response = $this->post(
            route('payments.store'),
            $data
        );

        $response->assertSessionHasErrors('invoice_id');

        $this->assertDatabaseMissing('payments', [
            'payment_number' => 'PAY-MISSING-INVOICE',
        ]);
    }

    public function test_payment_rejects_non_existing_invoice(): void
    {
        $data = $this->validPaymentData(
            999999,
            'PAY-NONEXISTING-INVOICE'
        );

        $response = $this->actingAs($this->admin())
            ->post(route('payments.store'), $data);

        $response->assertSessionHasErrors('invoice_id');

        $this->assertDatabaseMissing('payments', [
            'payment_number' => 'PAY-NONEXISTING-INVOICE',
        ]);
    }

    public function test_payment_number_must_be_unique(): void
    {
        $invoice = $this->createInvoice(
            'SALE-PAY-005',
            'INV-PAY-005'
        );

        Payment::create(
            $this->validPaymentData(
                $invoice->id,
                'PAY-DUPLICATE'
            )
        );

        $response = $this->actingAs($this->admin())
            ->post(
                route('payments.store'),
                $this->validPaymentData(
                    $invoice->id,
                    'PAY-DUPLICATE'
                )
            );

        $response->assertSessionHasErrors('payment_number');

        $this->assertDatabaseCount('payments', 1);
    }

    public function test_payment_method_must_be_valid(): void
    {
        $invoice = $this->createInvoice(
            'SALE-PAY-006',
            'INV-PAY-006'
        );

        $data = $this->validPaymentData(
            $invoice->id,
            'PAY-INVALID-METHOD'
        );

        $data['payment_method'] = 'bitcoin';

        $response = $this->actingAs($this->admin())
            ->post(route('payments.store'), $data);

        $response->assertSessionHasErrors('payment_method');

        $this->assertDatabaseMissing('payments', [
            'payment_number' => 'PAY-INVALID-METHOD',
        ]);
    }

    public function test_payment_can_be_updated(): void
    {
        $invoice = $this->createInvoice(
            'SALE-PAY-007',
            'INV-PAY-007',
            100000
        );

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'payment_number' => 'PAY-UPDATE-001',
            'payment_date' => now()->toDateString(),
            'amount' => 40000,
            'payment_method' => 'cash',
            'reference_number' => null,
            'notes' => null,
        ]);

        $invoice->updatePaymentStatus();

        $response = $this->actingAs($this->admin())
            ->put(
                route('payments.update', $payment),
                [
                    'invoice_id' => $invoice->id,
                    'payment_number' => 'PAY-UPDATE-001',
                    'payment_date' => now()->toDateString(),
                    'amount' => 100000,
                    'payment_method' => 'bank_transfer',
                    'reference_number' => 'REF-001',
                    'notes' => 'Updated payment',
                ]
            );

        $payment->refresh();

        $response
            ->assertRedirect(route('payments.show', $payment))
            ->assertSessionHas(
                'success',
                'Payment updated successfully.'
            );

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'amount' => 100000,
            'payment_method' => 'bank_transfer',
            'reference_number' => 'REF-001',
            'notes' => 'Updated payment',
        ]);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'paid',
        ]);
    }

    public function test_payment_can_keep_same_payment_number_when_updating(): void
    {
        $invoice = $this->createInvoice(
            'SALE-PAY-008',
            'INV-PAY-008'
        );

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'payment_number' => 'PAY-SAME-001',
            'payment_date' => now()->toDateString(),
            'amount' => 30000,
            'payment_method' => 'cash',
            'reference_number' => null,
            'notes' => null,
        ]);

        $data = $this->validPaymentData(
            $invoice->id,
            'PAY-SAME-001'
        );

        $data['amount'] = 60000;

        $response = $this->actingAs($this->admin())
            ->put(
                route('payments.update', $payment),
                $data
            );

        $response->assertRedirect(
            route('payments.show', $payment)
        );

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'payment_number' => 'PAY-SAME-001',
            'amount' => 60000,
        ]);
    }

    public function test_payment_cannot_be_updated_with_existing_payment_number(): void
    {
        $invoice = $this->createInvoice(
            'SALE-PAY-009',
            'INV-PAY-009'
        );

        $payment1 = Payment::create([
            'invoice_id' => $invoice->id,
            'payment_number' => 'PAY-EXISTING-001',
            'payment_date' => now()->toDateString(),
            'amount' => 20000,
            'payment_method' => 'cash',
            'reference_number' => null,
            'notes' => null,
        ]);

        $payment2 = Payment::create([
            'invoice_id' => $invoice->id,
            'payment_number' => 'PAY-EXISTING-002',
            'payment_date' => now()->toDateString(),
            'amount' => 30000,
            'payment_method' => 'cash',
            'reference_number' => null,
            'notes' => null,
        ]);

        $data = $this->validPaymentData(
            $invoice->id,
            'PAY-EXISTING-001'
        );

        $response = $this->actingAs($this->admin())
            ->put(
                route('payments.update', $payment2),
                $data
            );

        $response->assertSessionHasErrors('payment_number');

        $this->assertDatabaseHas('payments', [
            'id' => $payment1->id,
            'payment_number' => 'PAY-EXISTING-001',
        ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment2->id,
            'payment_number' => 'PAY-EXISTING-002',
        ]);
    }

    public function test_deleting_full_payment_changes_invoice_status_back_to_issued(): void
    {
        $invoice = $this->createInvoice(
            'SALE-PAY-010',
            'INV-PAY-010',
            100000
        );

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'payment_number' => 'PAY-DELETE-001',
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

        $response = $this->actingAs($this->admin())
            ->delete(
                route('payments.destroy', $payment)
            );

        $response
            ->assertRedirect(route('payments.index'))
            ->assertSessionHas(
                'success',
                'Payment deleted successfully.'
            );

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'issued',
        ]);

        $this->assertDatabaseMissing('payments', [
            'id' => $payment->id,
        ]);
    }

    public function test_deleting_partial_payment_recalculates_invoice_status(): void
    {
        $invoice = $this->createInvoice(
            'SALE-PAY-011',
            'INV-PAY-011',
            100000
        );

        $payment1 = Payment::create([
            'invoice_id' => $invoice->id,
            'payment_number' => 'PAY-PARTIAL-001',
            'payment_date' => now()->toDateString(),
            'amount' => 40000,
            'payment_method' => 'cash',
            'reference_number' => null,
            'notes' => null,
        ]);

        $payment2 = Payment::create([
            'invoice_id' => $invoice->id,
            'payment_number' => 'PAY-PARTIAL-002',
            'payment_date' => now()->toDateString(),
            'amount' => 30000,
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
            ->delete(
                route('payments.destroy', $payment2)
            );

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'partially_paid',
        ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment1->id,
        ]);
    }

    public function test_updating_payment_recalculates_invoice_status(): void
    {
        $invoice = $this->createInvoice(
            'SALE-PAY-012',
            'INV-PAY-012',
            100000
        );

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'payment_number' => 'PAY-UPDATE-002',
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
            ->put(
                route('payments.update', $payment),
                [
                    'invoice_id' => $invoice->id,
                    'payment_number' => 'PAY-UPDATE-002',
                    'payment_date' => now()->toDateString(),
                    'amount' => 100000,
                    'payment_method' => 'cash',
                    'reference_number' => null,
                    'notes' => null,
                ]
            );

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'paid',
        ]);
    }

    public function test_guest_cannot_create_payment(): void
    {
        $invoice = $this->createInvoice(
            'SALE-PAY-013',
            'INV-PAY-013'
        );

        $response = $this->post(
            route('payments.store'),
            $this->validPaymentData(
                $invoice->id,
                'PAY-GUEST-001'
            )
        );

        $response->assertRedirect(route('login'));

        $this->assertDatabaseMissing('payments', [
            'payment_number' => 'PAY-GUEST-001',
        ]);
    }

    public function test_guest_cannot_update_payment(): void
    {
        $invoice = $this->createInvoice(
            'SALE-PAY-014',
            'INV-PAY-014'
        );

        $payment = Payment::create(
            $this->validPaymentData(
                $invoice->id,
                'PAY-GUEST-002'
            )
        );

        $response = $this->put(
            route('payments.update', $payment),
            $this->validPaymentData(
                $invoice->id,
                'PAY-GUEST-002'
            )
        );

        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_delete_payment(): void
    {
        $invoice = $this->createInvoice(
            'SALE-PAY-015',
            'INV-PAY-015'
        );

        $payment = Payment::create(
            $this->validPaymentData(
                $invoice->id,
                'PAY-GUEST-003'
            )
        );

        $response = $this->delete(
            route('payments.destroy', $payment)
        );

        $response->assertRedirect(route('login'));

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
        ]);
    }

    public function test_updating_payment_to_another_invoice_recalculates_both_invoices(): void
    {
        $invoice1 = $this->createInvoice(
            'SALE-PAY-016',
            'INV-PAY-016',
            100000
        );

        $invoice2 = $this->createInvoice(
            'SALE-PAY-017',
            'INV-PAY-017',
            100000
        );

        $payment = Payment::create([
            'invoice_id' => $invoice1->id,
            'payment_number' => 'PAY-MOVE-001',
            'payment_date' => now()->toDateString(),
            'amount' => 50000,
            'payment_method' => 'cash',
            'reference_number' => null,
            'notes' => null,
        ]);

        $invoice1->updatePaymentStatus();

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice1->id,
            'status' => 'partially_paid',
        ]);

        $this->actingAs($this->admin())
            ->put(
                route('payments.update', $payment),
                [
                    'invoice_id' => $invoice2->id,
                    'payment_number' => 'PAY-MOVE-001',
                    'payment_date' => now()->toDateString(),
                    'amount' => 100000,
                    'payment_method' => 'bank_transfer',
                    'reference_number' => null,
                    'notes' => null,
                ]
            );

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'invoice_id' => $invoice2->id,
            'amount' => 100000,
        ]);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice1->id,
            'status' => 'issued',
        ]);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice2->id,
            'status' => 'paid',
        ]);
    }
}