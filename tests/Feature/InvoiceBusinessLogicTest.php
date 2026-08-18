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

    private function createAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
        ]);
    }

    private function createSale(
        string $invoiceNumber = 'SALE-001',
        float $totalAmount = 250000
    ): Sale {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => fake()->unique()->safeEmail(),
            'phone' => '9800000000',
            'address' => 'Test Address',
        ]);

        return Sale::create([
            'customer_id' => $customer->id,
            'invoice_number' => $invoiceNumber,
            'sale_date' => '2026-08-17',
            'total_amount' => $totalAmount,
            'status' => 'completed',
            'notes' => null,
        ]);
    }

    private function validInvoiceData(
        int $saleId,
        string $invoiceNumber = 'INV-TEST-001'
    ): array {
        return [
            'sale_id' => $saleId,
            'invoice_number' => $invoiceNumber,
            'invoice_date' => '2026-08-17',
            'due_date' => '2026-08-31',
            'subtotal' => 250000,
            'discount' => 0,
            'tax' => 0,
            'total_amount' => 250000,
            'status' => 'issued',
            'notes' => 'Test invoice',
        ];
    }

    public function test_invoice_can_be_created_for_a_sale(): void
    {
        $admin = $this->createAdmin();
        $sale = $this->createSale();

        $response = $this->actingAs($admin)
            ->post(route('invoices.store'), $this->validInvoiceData($sale->id));

        $response
            ->assertRedirect(route('invoices.index'))
            ->assertSessionHas(
                'success',
                'Invoice created successfully.'
            );

        $this->assertDatabaseHas('invoices', [
            'sale_id' => $sale->id,
            'invoice_number' => 'INV-TEST-001',
            'total_amount' => 250000,
            'status' => 'issued',
        ]);
    }

    public function test_invoice_rejects_due_date_before_invoice_date(): void
    {
        $admin = $this->createAdmin();
        $sale = $this->createSale();

        $data = $this->validInvoiceData($sale->id, 'INV-TEST-002');
        $data['due_date'] = '2026-08-10';

        $response = $this->actingAs($admin)
            ->post(route('invoices.store'), $data);

        $response->assertSessionHasErrors('due_date');

        $this->assertDatabaseMissing('invoices', [
            'invoice_number' => 'INV-TEST-002',
        ]);
    }

    public function test_invoice_rejects_invalid_status(): void
    {
        $admin = $this->createAdmin();
        $sale = $this->createSale();

        $data = $this->validInvoiceData($sale->id, 'INV-TEST-003');
        $data['status'] = 'invalid_status';

        $response = $this->actingAs($admin)
            ->post(route('invoices.store'), $data);

        $response->assertSessionHasErrors('status');

        $this->assertDatabaseMissing('invoices', [
            'invoice_number' => 'INV-TEST-003',
        ]);
    }

    public function test_invoice_requires_sale(): void
    {
        $admin = $this->createAdmin();

        $data = $this->validInvoiceData(1, 'INV-TEST-004');
        unset($data['sale_id']);

        $response = $this->actingAs($admin)
            ->post(route('invoices.store'), $data);

        $response->assertSessionHasErrors('sale_id');

        $this->assertDatabaseMissing('invoices', [
            'invoice_number' => 'INV-TEST-004',
        ]);
    }

    public function test_invoice_rejects_non_existing_sale(): void
    {
        $admin = $this->createAdmin();

        $data = $this->validInvoiceData(999999, 'INV-TEST-005');

        $response = $this->actingAs($admin)
            ->post(route('invoices.store'), $data);

        $response->assertSessionHasErrors('sale_id');

        $this->assertDatabaseMissing('invoices', [
            'invoice_number' => 'INV-TEST-005',
        ]);
    }

    public function test_invoice_number_must_be_unique(): void
    {
        $admin = $this->createAdmin();

        $sale1 = $this->createSale('SALE-006', 100000);
        $sale2 = $this->createSale('SALE-007', 200000);

        Invoice::create(
            $this->validInvoiceData(
                $sale1->id,
                'INV-DUPLICATE'
            )
        );

        $data = $this->validInvoiceData(
            $sale2->id,
            'INV-DUPLICATE'
        );

        $response = $this->actingAs($admin)
            ->post(route('invoices.store'), $data);

        $response->assertSessionHasErrors('invoice_number');

        $this->assertDatabaseCount('invoices', 1);
    }

    public function test_invoice_rejects_negative_amounts(): void
    {
        $admin = $this->createAdmin();
        $sale = $this->createSale();

        foreach ([
            'subtotal',
            'discount',
            'tax',
            'total_amount',
        ] as $field) {
            $data = $this->validInvoiceData(
                $sale->id,
                'INV-NEGATIVE-' . $field
            );

            $data[$field] = -1;

            $response = $this->actingAs($admin)
                ->post(route('invoices.store'), $data);

            $response->assertSessionHasErrors($field);
        }

        $this->assertDatabaseCount('invoices', 0);
    }

    public function test_invoice_can_be_updated(): void
    {
        $admin = $this->createAdmin();
        $sale = $this->createSale();

        $invoice = Invoice::create(
            $this->validInvoiceData(
                $sale->id,
                'INV-UPDATE-001'
            )
        );

        $response = $this->actingAs($admin)
            ->put(
                route('invoices.update', $invoice),
                [
                    'sale_id' => $sale->id,
                    'invoice_number' => 'INV-UPDATED-001',
                    'invoice_date' => '2026-08-18',
                    'due_date' => '2026-09-01',
                    'subtotal' => 300000,
                    'discount' => 10000,
                    'tax' => 20000,
                    'total_amount' => 310000,
                    'status' => 'issued',
                    'notes' => 'Updated invoice',
                ]
            );

        $invoice->refresh();

        $response
            ->assertRedirect(
                route('invoices.show', $invoice)
            )
            ->assertSessionHas(
                'success',
                'Invoice updated successfully.'
            );

        $this->assertSame(
            'INV-UPDATED-001',
            $invoice->invoice_number
        );

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'invoice_number' => 'INV-UPDATED-001',
            'subtotal' => 300000,
            'discount' => 10000,
            'tax' => 20000,
            'total_amount' => 310000,
            'notes' => 'Updated invoice',
        ]);
    }

    public function test_invoice_can_keep_same_invoice_number_when_updating(): void
    {
        $admin = $this->createAdmin();
        $sale = $this->createSale();

        $invoice = Invoice::create(
            $this->validInvoiceData(
                $sale->id,
                'INV-SAME-001'
            )
        );

        $data = $this->validInvoiceData(
            $sale->id,
            'INV-SAME-001'
        );

        $data['total_amount'] = 275000;

        $response = $this->actingAs($admin)
            ->put(
                route('invoices.update', $invoice),
                $data
            );

        $response->assertRedirect(
            route('invoices.show', $invoice)
        );

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'invoice_number' => 'INV-SAME-001',
            'total_amount' => 275000,
        ]);
    }

    public function test_invoice_cannot_be_updated_with_existing_invoice_number(): void
    {
        $admin = $this->createAdmin();

        $sale1 = $this->createSale('SALE-008', 100000);
        $sale2 = $this->createSale('SALE-009', 200000);

        $invoice1 = Invoice::create(
            $this->validInvoiceData(
                $sale1->id,
                'INV-EXISTING-001'
            )
        );

        $invoice2 = Invoice::create(
            $this->validInvoiceData(
                $sale2->id,
                'INV-EXISTING-002'
            )
        );

        $data = $this->validInvoiceData(
            $sale2->id,
            'INV-EXISTING-001'
        );

        $response = $this->actingAs($admin)
            ->put(
                route('invoices.update', $invoice2),
                $data
            );

        $response->assertSessionHasErrors('invoice_number');

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice1->id,
            'invoice_number' => 'INV-EXISTING-001',
        ]);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice2->id,
            'invoice_number' => 'INV-EXISTING-002',
        ]);
    }

    public function test_invoice_update_rejects_invalid_due_date(): void
    {
        $admin = $this->createAdmin();
        $sale = $this->createSale();

        $invoice = Invoice::create(
            $this->validInvoiceData(
                $sale->id,
                'INV-DATE-001'
            )
        );

        $data = $this->validInvoiceData(
            $sale->id,
            'INV-DATE-001'
        );

        $data['invoice_date'] = '2026-08-20';
        $data['due_date'] = '2026-08-19';

        $response = $this->actingAs($admin)
            ->put(
                route('invoices.update', $invoice),
                $data
            );

        $response->assertSessionHasErrors('due_date');

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'invoice_number' => 'INV-DATE-001',
        ]);
    }

    public function test_invoice_can_be_deleted(): void
    {
        $admin = $this->createAdmin();
        $sale = $this->createSale();

        $invoice = Invoice::create(
            $this->validInvoiceData(
                $sale->id,
                'INV-DELETE-001'
            )
        );

        $response = $this->actingAs($admin)
            ->delete(
                route('invoices.destroy', $invoice)
            );

        $response
            ->assertRedirect(route('invoices.index'))
            ->assertSessionHas(
                'success',
                'Invoice deleted successfully.'
            );

        $this->assertDatabaseMissing('invoices', [
            'id' => $invoice->id,
        ]);
    }

    public function test_guest_cannot_create_invoice(): void
    {
        $sale = $this->createSale();

        $data = $this->validInvoiceData(
            $sale->id,
            'INV-GUEST-001'
        );

        $response = $this->post(
            route('invoices.store'),
            $data
        );

        $response->assertRedirect(route('login'));

        $this->assertDatabaseMissing('invoices', [
            'invoice_number' => 'INV-GUEST-001',
        ]);
    }
}