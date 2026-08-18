<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBusinessLogicTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_can_be_rendered(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertSuccessful();
        $response->assertViewIs('admin.index');
    }

    public function test_admin_dashboard_displays_correct_statistics(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        User::factory()->create([
            'role' => 'user',
        ]);

        Customer::create([
            'name' => 'Admin Test Customer',
            'email' => 'admin-customer@example.com',
            'phone' => '9800000101',
            'address' => 'Kathmandu',
            'active' => true,
        ]);

        Product::create([
            'name' => 'Admin Test Product',
            'sku' => 'ADMIN-TEST-001',
            'category' => 'Test',
            'purchase_price' => 500,
            'selling_price' => 800,
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
            'description' => null,
            'is_active' => true,
        ]);

        Supplier::create([
            'name' => 'Admin Test Supplier',
            'email' => 'admin-supplier@example.com',
            'phone' => '9800000102',
            'address' => 'Kathmandu',
            'active' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertSuccessful();

        $response->assertViewHas('stats', function (array $stats) {
            return $stats['users'] === 2
                && $stats['customers'] === 1
                && $stats['products'] === 1
                && $stats['suppliers'] === 1
                && $stats['purchases'] === 0
                && $stats['sales'] === 0
                && $stats['invoices'] === 0
                && $stats['payments'] === 0
                && $stats['expenses'] === 0;
        });
    }

    public function test_admin_dashboard_loads_recent_sales(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $customer = Customer::create([
            'name' => 'Recent Sale Customer',
            'email' => 'recent-sale@example.com',
            'phone' => '9800000103',
            'address' => 'Kathmandu',
            'active' => true,
        ]);

        $product = Product::create([
            'name' => 'Recent Sale Product',
            'sku' => 'ADMIN-SALE-001',
            'category' => 'Test',
            'purchase_price' => 500,
            'selling_price' => 800,
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
            'description' => null,
            'is_active' => true,
        ]);

        $sale = Sale::create([
            'customer_id' => $customer->id,
            'invoice_number' => 'ADMIN-SALE-001',
            'sale_date' => now()->format('Y-m-d'),
            'status' => 'completed',
            'total_amount' => 800,
        ]);

        $sale->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 800,
            'total' => 800,
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertSuccessful();

        $response->assertViewHas('recentSales', function ($recentSales) use ($sale) {
            return $recentSales->contains('id', $sale->id);
        });
    }

    public function test_admin_dashboard_loads_recent_invoices(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $customer = Customer::create([
            'name' => 'Recent Invoice Customer',
            'email' => 'recent-invoice@example.com',
            'phone' => '9800000104',
            'address' => 'Kathmandu',
            'active' => true,
        ]);

        $sale = Sale::create([
            'customer_id' => $customer->id,
            'invoice_number' => 'ADMIN-INVOICE-SALE-001',
            'sale_date' => now()->format('Y-m-d'),
            'status' => 'completed',
            'total_amount' => 1500,
        ]);

        $invoice = Invoice::create([
            'sale_id' => $sale->id,
            'invoice_number' => 'ADMIN-INVOICE-001',
            'invoice_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            'total_amount' => 1500,
            'status' => 'issued',
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertSuccessful();

        $response->assertViewHas('recentInvoices', function ($recentInvoices) use ($invoice) {
            return $recentInvoices->contains('id', $invoice->id);
        });
    }

    public function test_admin_dashboard_loads_recent_payments(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $customer = Customer::create([
            'name' => 'Recent Payment Customer',
            'email' => 'recent-payment@example.com',
            'phone' => '9800000105',
            'address' => 'Kathmandu',
            'active' => true,
        ]);

        $sale = Sale::create([
            'customer_id' => $customer->id,
            'invoice_number' => 'ADMIN-PAYMENT-SALE-001',
            'sale_date' => now()->format('Y-m-d'),
            'status' => 'completed',
            'total_amount' => 2000,
        ]);

        $invoice = Invoice::create([
            'sale_id' => $sale->id,
            'invoice_number' => 'ADMIN-PAYMENT-INV-001',
            'invoice_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            'total_amount' => 2000,
            'status' => 'issued',
        ]);

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'payment_number' => 'ADMIN-PAYMENT-001',
            'payment_date' => now()->format('Y-m-d'),
            'amount' => 1000,
            'payment_method' => 'cash',
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertSuccessful();

        $response->assertViewHas('recentPayments', function ($recentPayments) use ($payment) {
            return $recentPayments->contains('id', $payment->id);
        });
    }

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }
}