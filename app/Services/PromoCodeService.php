<?php

namespace App\Services;

use App\Models\PromoCode;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class PromoCodeService
{
    public function normalize(string $code): string
    {
        $code = trim($code);
        $code = preg_replace('/\s+/', '', $code) ?? $code;

        return strtoupper($code);
    }

    public function findByCode(string $code, bool $forUpdate = false): ?PromoCode
    {
        $normalized = $this->normalize($code);

        $query = PromoCode::query()->where('code', $normalized);
        if ($forUpdate) {
            $query->lockForUpdate();
        }

        return $query->first();
    }

    public function assertCanApply(PromoCode $promo, float $baseTotal, ?Carbon $now = null): void
    {
        $now ??= now();

        if (! $promo->is_active) {
            throw ValidationException::withMessages([
                'promo_code' => 'البروموكود غير فعال.',
            ]);
        }

        if ($promo->starts_at && $now->lt($promo->starts_at)) {
            throw ValidationException::withMessages([
                'promo_code' => 'البروموكود لم يبدأ بعد.',
            ]);
        }

        if ($promo->ends_at && $now->gt($promo->ends_at)) {
            throw ValidationException::withMessages([
                'promo_code' => 'البروموكود انتهى.',
            ]);
        }

        if ($promo->usage_limit !== null && $promo->used_count >= $promo->usage_limit) {
            throw ValidationException::withMessages([
                'promo_code' => 'البروموكود وصل لحد الاستخدام.',
            ]);
        }

        if ($baseTotal < (float) $promo->min_order_total) {
            throw ValidationException::withMessages([
                'promo_code' => 'قيمة الأوردر أقل من الحد الأدنى للبروموكود.',
            ]);
        }
    }

    public function discountFor(PromoCode $promo, float $baseTotal): float
    {
        return $promo->discountForTotal($baseTotal);
    }
}

