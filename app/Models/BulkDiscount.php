<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BulkDiscount extends Model
{
    /** @use HasFactory<\Database\Factories\BulkDiscountFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'min_qty',
        'discount_type',
        'value',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'min_qty' => 'integer',
            'value' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

