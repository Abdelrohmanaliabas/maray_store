<?php

namespace App\Services;

use App\Models\Product;

class PricingService
{
    public function discountForProductQty(Product $product, int $qty, float $unitPrice): float
    {
        $qty = max(1, $qty);

        $rule = $product->bulkDiscounts()
            ->where('is_active', true)
            ->where('min_qty', '<=', $qty)
            ->orderByDesc('min_qty')
            ->first();

        if (! $rule) {
            return 0.0;
        }

        if ($rule->discount_type === 'percent') {
            $percent = min(100.0, max(0.0, (float) $rule->value));
            return round(($unitPrice * $qty) * ($percent / 100.0), 2);
        }

        if ($rule->discount_type === 'fixed') {
            $fixed = max(0.0, (float) $rule->value);
            return round($fixed * $qty, 2);
        }

        return 0.0;
    }
}

