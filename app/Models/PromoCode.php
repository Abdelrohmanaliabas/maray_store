<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class PromoCode extends Model
{
    /** @use HasFactory<\Database\Factories\PromoCodeFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'discount_type',
        'value',
        'min_order_total',
        'max_discount',
        'usage_limit',
        'used_count',
        'starts_at',
        'ends_at',
        'is_active',
        'created_by_admin_id',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_order_total' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'usage_limit' => 'integer',
            'used_count' => 'integer',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function createdByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    public function isCurrentlyActive(?Carbon $now = null): bool
    {
        $now ??= now();

        if (! $this->is_active) {
            return false;
        }

        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false;
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function discountForTotal(float $baseTotal): float
    {
        $baseTotal = max(0.0, $baseTotal);
        if ($baseTotal <= 0.0) {
            return 0.0;
        }

        if ($baseTotal < (float) $this->min_order_total) {
            return 0.0;
        }

        $discount = 0.0;
        if ($this->discount_type === 'percent') {
            $percent = min(100.0, max(0.0, (float) $this->value));
            $discount = $baseTotal * ($percent / 100.0);
        } elseif ($this->discount_type === 'fixed') {
            $discount = max(0.0, (float) $this->value);
        }

        if ($this->max_discount !== null) {
            $discount = min($discount, max(0.0, (float) $this->max_discount));
        }

        return round(min($baseTotal, $discount), 2);
    }
}

