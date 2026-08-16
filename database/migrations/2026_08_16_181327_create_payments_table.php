<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Related invoice
            $table->foreignId('invoice_id')
                ->constrained('invoices')
                ->cascadeOnDelete();

            // Payment details
            $table->string('payment_number')->unique();
            $table->date('payment_date');

            // Amount
            $table->decimal('amount', 12, 2);

            // Payment method
            $table->enum('payment_method', [
                'cash',
                'bank_transfer',
                'card',
                'mobile_payment',
                'other',
            ])->default('cash');

            // Optional reference
            $table->string('reference_number')->nullable();

            // Optional notes
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};