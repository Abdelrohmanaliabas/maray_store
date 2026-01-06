<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'order_number',
        'status',
        'customer_name',
        'phone',
        'phone2',
        'governorate',
        'address',
        'notes',
        'subtotal',
        'discount_total',
        'total',
        'payment_method',
        'created_by_admin_id',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_total' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function createdByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }
}

