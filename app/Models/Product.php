<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'category',
        'purchase_price',
        'selling_price',
        'stock_quantity',
        'low_stock_threshold',
        'description',
        'is_active',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'stock_quantity' => 'decimal:2',
        'low_stock_threshold' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}

