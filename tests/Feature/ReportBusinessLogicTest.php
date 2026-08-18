<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportBusinessLogicTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
        ]);
    }

    private function customer(): Customer
    {
        return Customer::create([
            'name' => 'Test Customer',
            'email' => 'customer' . uniqid() . '@example.com',
            'phone' => '9800000000',
            'address' => 'Kathmandu',
        ]);
    }

    private function supplier(): Supplier
    {
        return Supplier::create([
            'name' => 'Test Supplier',
            'company' => 'Test Company',
            'email' => 'supplier' . uniqid() . '@example.com',
            'phone' => '9810000000',
            'address' => 'Kathmandu',
            'description' => 'Test supplier',
            'active' => true,
        ]);
    }

    private function sale(
        float $amount,
        string $date = '2026-08-15',
        string $status = 'completed'
    ): Sale {
        return Sale::create([
            'customer_id' => $this->customer()->id,
            'invoice_number' => 'SALE-' . uniqid(),
            'sale_date' => $date,
            'total_amount' => $amount,
            'status' => $status,
            'notes' => null,
        ]);
    }

    private function purchase(
        float $amount,
        string $date = '2026-08-15',
        string $status = 'received'
    ): Purchase {
        return Purchase::create([
            'supplier_id' => $this->supplier()->id,
            'invoice_number' => 'PUR-' . uniqid(),
            'purchase_date' => $date,
            'total_amount' => $amount,
            'status' => $status,
            'notes' => null,
        ]);
    }

    private function expense(
        float $amount,
        string $date = '2026-08-15'
    ): Expense {
        return Expense::create([
            'expense_date' => $date,
            'title' => 'Test Expense',
            'category' => 'Office',
            'amount' => $amount,
            'description' => null,
        ]);
    }

    public function test_admin_can_access_reports(): void
    {
        $response = $this->actingAs($this->admin())
            ->get(route('reports.index'));

        $response->assertOk();
        $response->assertViewIs('reports.index');
    }

    public function test_guest_cannot_access_reports(): void
    {
        $response = $this->get(route('reports.index'));

        $response->assertRedirect();
    }

    public function test_normal_user_cannot_access_reports(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)
            ->get(route('reports.index'));

        $response->assertForbidden();
    }

    public function test_report_calculates_total_sales(): void
    {
        $this->sale(1000);
        $this->sale(2500);

        $response = $this->actingAs($this->admin())
            ->get(route('reports.index'));

        $response->assertViewHas('totalSales', function ($totalSales) {
            return (float) $totalSales === 3500.0;
        });
    }

    public function test_cancelled_sales_are_excluded(): void
    {
        $this->sale(1000, '2026-08-15', 'completed');
        $this->sale(5000, '2026-08-15', 'cancelled');

        $response = $this->actingAs($this->admin())
            ->get(route('reports.index'));

        $response->assertViewHas('totalSales', function ($totalSales) {
            return (float) $totalSales === 1000.0;
        });
    }

    public function test_report_calculates_total_purchases(): void
    {
        $this->purchase(2000);
        $this->purchase(3000);

        $response = $this->actingAs($this->admin())
            ->get(route('reports.index'));

        $response->assertViewHas('totalPurchases', function ($totalPurchases) {
            return (float) $totalPurchases === 5000.0;
        });
    }

    public function test_cancelled_purchases_are_excluded(): void
    {
        $this->purchase(2000, '2026-08-15', 'received');
        $this->purchase(5000, '2026-08-15', 'cancelled');

        $response = $this->actingAs($this->admin())
            ->get(route('reports.index'));

        $response->assertViewHas('totalPurchases', function ($totalPurchases) {
            return (float) $totalPurchases === 2000.0;
        });
    }

    public function test_report_calculates_total_expenses(): void
    {
        $this->expense(500);
        $this->expense(1500);

        $response = $this->actingAs($this->admin())
            ->get(route('reports.index'));

        $response->assertViewHas('totalExpenses', function ($totalExpenses) {
            return (float) $totalExpenses === 2000.0;
        });
    }

    public function test_report_calculates_profit(): void
    {
        $this->sale(10000);
        $this->purchase(4000);
        $this->expense(1500);

        $response = $this->actingAs($this->admin())
            ->get(route('reports.index'));

        $response->assertViewHas('profit', function ($profit) {
            return (float) $profit === 4500.0;
        });
    }

    public function test_report_can_filter_by_from_date(): void
    {
        $this->sale(1000, '2026-08-10');
        $this->sale(2000, '2026-08-15');
        $this->sale(3000, '2026-08-20');

        $response = $this->actingAs($this->admin())
            ->get(route('reports.index', [
                'from_date' => '2026-08-15',
            ]));

        $response->assertViewHas('totalSales', function ($totalSales) {
            return (float) $totalSales === 5000.0;
        });
    }

    public function test_report_can_filter_by_to_date(): void
    {
        $this->sale(1000, '2026-08-10');
        $this->sale(2000, '2026-08-15');
        $this->sale(3000, '2026-08-20');

        $response = $this->actingAs($this->admin())
            ->get(route('reports.index', [
                'to_date' => '2026-08-15',
            ]));

        $response->assertViewHas('totalSales', function ($totalSales) {
            return (float) $totalSales === 3000.0;
        });
    }

    public function test_report_can_filter_by_date_range(): void
    {
        $this->sale(1000, '2026-08-10');
        $this->sale(2000, '2026-08-15');
        $this->sale(3000, '2026-08-20');

        $response = $this->actingAs($this->admin())
            ->get(route('reports.index', [
                'from_date' => '2026-08-12',
                'to_date' => '2026-08-18',
            ]));

        $response->assertViewHas('totalSales', function ($totalSales) {
            return (float) $totalSales === 2000.0;
        });
    }

    public function test_report_date_filter_applies_to_sales_purchases_and_expenses(): void
    {
        $this->sale(1000, '2026-08-10');
        $this->sale(2000, '2026-08-15');
        $this->sale(3000, '2026-08-20');

        $this->purchase(500, '2026-08-10');
        $this->purchase(1000, '2026-08-15');
        $this->purchase(1500, '2026-08-20');

        $this->expense(200, '2026-08-10');
        $this->expense(400, '2026-08-15');
        $this->expense(600, '2026-08-20');

        $response = $this->actingAs($this->admin())
            ->get(route('reports.index', [
                'from_date' => '2026-08-12',
                'to_date' => '2026-08-18',
            ]));

        $response->assertViewHas('totalSales', function ($value) {
            return (float) $value === 2000.0;
        });

        $response->assertViewHas('totalPurchases', function ($value) {
            return (float) $value === 1000.0;
        });

        $response->assertViewHas('totalExpenses', function ($value) {
            return (float) $value === 400.0;
        });

        $response->assertViewHas('profit', function ($value) {
            return (float) $value === 600.0;
        });
    }

    public function test_from_date_must_be_valid_date(): void
    {
        $response = $this->actingAs($this->admin())
            ->get(route('reports.index', [
                'from_date' => 'invalid-date',
            ]));

        $response->assertSessionHasErrors('from_date');
    }

    public function test_to_date_must_be_valid_date(): void
    {
        $response = $this->actingAs($this->admin())
            ->get(route('reports.index', [
                'to_date' => 'invalid-date',
            ]));

        $response->assertSessionHasErrors('to_date');
    }

    public function test_to_date_cannot_be_before_from_date(): void
    {
        $response = $this->actingAs($this->admin())
            ->get(route('reports.index', [
                'from_date' => '2026-08-20',
                'to_date' => '2026-08-19',
            ]));

        $response->assertSessionHasErrors('to_date');
    }

    public function test_report_handles_zero_values(): void
    {
        $response = $this->actingAs($this->admin())
            ->get(route('reports.index'));

        $response->assertOk();

        $response->assertViewHas('totalSales', 0);
        $response->assertViewHas('totalPurchases', 0);
        $response->assertViewHas('totalExpenses', 0);
        $response->assertViewHas('profit', 0);
    }
}