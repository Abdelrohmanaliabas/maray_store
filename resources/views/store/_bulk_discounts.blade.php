@php
    /** @var \App\Models\Product $product */
    $limit = isset($limit) ? (int) $limit : null;
    $rules = $product->bulkDiscounts
        ->where('is_active', true)
        ->values();

    if ($limit !== null && $limit > 0) {
        $rules = $rules->take($limit);
    }
@endphp

@if($rules->count())
    <div class="mt-2 space-y-1 text-xs text-slate-700">
        @foreach($rules as $r)
            @if($r->discount_type === 'percent')
                <div class="text-emerald-700">عند شراء {{ (int) $r->min_qty }}+ خصم {{ rtrim(rtrim(number_format((float) $r->value, 2), '0'), '.') }}%</div>
            @elseif($r->discount_type === 'fixed')
                <div class="text-emerald-700">عند شراء {{ (int) $r->min_qty }}+ خصم {{ number_format((float) $r->value, 2) }} EGP للقطعة</div>
            @endif
        @endforeach
    </div>
@endif

