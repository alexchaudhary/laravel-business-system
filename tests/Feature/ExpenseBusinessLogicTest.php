<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseBusinessLogicTest extends TestCase
{
    use RefreshDatabase;

    public function test_expense_can_be_created(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($user)
            ->post(route('expenses.store'), [
                'expense_date' => '2026-08-17',
                'title' => 'Office Rent',
                'category' => 'Rent',
                'amount' => 25000,
                'description' => 'Monthly office rent',
            ])
            ->assertRedirect(route('expenses.index'));

        $expense = Expense::where('title', 'Office Rent')->first();

        $this->assertNotNull($expense);
        $this->assertSame('Rent', $expense->category);
        $this->assertSame('25000.00', $expense->amount);
        $this->assertSame('2026-08-17', $expense->expense_date->format('Y-m-d'));
    }

    public function test_expense_can_be_updated(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $expense = Expense::create([
            'expense_date' => '2026-08-17',
            'title' => 'Old Expense',
            'category' => 'Office',
            'amount' => 1000,
            'description' => 'Old description',
        ]);

        $this->actingAs($user)
            ->put(route('expenses.update', $expense), [
                'expense_date' => '2026-08-18',
                'title' => 'Updated Expense',
                'category' => 'Utilities',
                'amount' => 1500,
                'description' => 'Updated description',
            ])
            ->assertRedirect(route('expenses.index'));

        $expense->refresh();

        $this->assertSame('Updated Expense', $expense->title);
        $this->assertSame('Utilities', $expense->category);
        $this->assertSame('1500.00', $expense->amount);
        $this->assertSame('2026-08-18', $expense->expense_date->format('Y-m-d'));
    }

    public function test_expense_can_be_deleted(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $expense = Expense::create([
            'expense_date' => '2026-08-17',
            'title' => 'Temporary Expense',
            'category' => 'Office',
            'amount' => 500,
            'description' => null,
        ]);

        $this->actingAs($user)
            ->delete(route('expenses.destroy', $expense))
            ->assertRedirect(route('expenses.index'));

        $this->assertDatabaseMissing('expenses', [
            'id' => $expense->id,
        ]);
    }

    public function test_expense_requires_date(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)
            ->post(route('expenses.store'), [
                'title' => 'Office Expense',
                'category' => 'Office',
                'amount' => 1000,
            ]);

        $response->assertSessionHasErrors('expense_date');
    }

    public function test_expense_requires_title(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)
            ->post(route('expenses.store'), [
                'expense_date' => '2026-08-17',
                'category' => 'Office',
                'amount' => 1000,
            ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_expense_requires_category(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)
            ->post(route('expenses.store'), [
                'expense_date' => '2026-08-17',
                'title' => 'Office Expense',
                'amount' => 1000,
            ]);

        $response->assertSessionHasErrors('category');
    }

    public function test_expense_requires_amount(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)
            ->post(route('expenses.store'), [
                'expense_date' => '2026-08-17',
                'title' => 'Office Expense',
                'category' => 'Office',
            ]);

        $response->assertSessionHasErrors('amount');
    }

    public function test_expense_amount_must_be_greater_than_zero(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)
            ->post(route('expenses.store'), [
                'expense_date' => '2026-08-17',
                'title' => 'Office Expense',
                'category' => 'Office',
                'amount' => 0,
            ]);

        $response->assertSessionHasErrors('amount');
    }

    public function test_expense_title_cannot_exceed_255_characters(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)
            ->post(route('expenses.store'), [
                'expense_date' => '2026-08-17',
                'title' => str_repeat('A', 256),
                'category' => 'Office',
                'amount' => 1000,
            ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_expense_description_cannot_exceed_2000_characters(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)
            ->post(route('expenses.store'), [
                'expense_date' => '2026-08-17',
                'title' => 'Office Expense',
                'category' => 'Office',
                'amount' => 1000,
                'description' => str_repeat('A', 2001),
            ]);

        $response->assertSessionHasErrors('description');
    }

    public function test_expense_description_can_be_null(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($user)
            ->post(route('expenses.store'), [
                'expense_date' => '2026-08-17',
                'title' => 'Office Expense',
                'category' => 'Office',
                'amount' => 1000,
                'description' => null,
            ])
            ->assertRedirect(route('expenses.index'));

        $this->assertDatabaseHas('expenses', [
            'title' => 'Office Expense',
            'description' => null,
        ]);
    }

    public function test_guest_cannot_create_expense(): void
    {
        $this->post(route('expenses.store'), [
            'expense_date' => '2026-08-17',
            'title' => 'Unauthorized Expense',
            'category' => 'Office',
            'amount' => 1000,
        ])->assertRedirect(route('login'));

        $this->assertDatabaseMissing('expenses', [
            'title' => 'Unauthorized Expense',
        ]);
    }
}
