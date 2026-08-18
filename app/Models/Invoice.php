<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'sale_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'subtotal',
        'discount',
        'tax',
        'total_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Invoice belongs to a Sale.
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Invoice has many Payments.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function updatePaymentStatus(): void
{
    $paidAmount = (float) $this->payments()->sum('amount');
    $totalAmount = (float) $this->total_amount;

    if ($paidAmount <= 0) {
        $this->update([
            'status' => 'issued',
        ]);

        return;
    }

    if ($paidAmount >= $totalAmount) {
        $this->update([
            'status' => 'paid',
        ]);

        return;
    }

    $this->update([
        'status' => 'partially_paid',
    ]);
}
}