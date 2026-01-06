@php
    /** @var \App\Models\Product $product */
    $img = $product->images->first()?->path;
    $hasDiscount = $product->compare_at_price && $product->compare_at_price > $product->price;
    $placeholder = 'data:image/svg+xml;utf8,'.rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="800" height="800"><rect width="100%" height="100%" fill="#f1f5f9"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#0f172a" font-family="Arial" font-size="32">MARAY</text></svg>');
    $inStock = (int) ($product->in_stock_variants_count ?? 0) > 0;
@endphp

<div class="overflow-hidden rounded-2xl border bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
    <div class="relative">
        <a href="{{ route('store.product', $product) }}">
            <img class="aspect-square w-full object-cover" src="{{ $img ? asset($img) : $placeholder }}" alt="{{ $product->name }}">
        </a>
        @if($hasDiscount)
            <div class="absolute right-3 top-3 rounded-md bg-red-600 px-2 py-1 text-xs font-medium text-white">خصم</div>
        @endif
    </div>

    <div class="p-3 sm:p-4">
        <div class="min-h-12 text-sm font-semibold">
            <a class="hover:underline" href="{{ route('store.product', $product) }}">{{ $product->name }}</a>
        </div>

        <div class="mt-2 flex items-end justify-between gap-3">
            <div class="text-right">
                @if($hasDiscount)
                    <div class="text-xs text-slate-500 line-through">{{ number_format($product->compare_at_price, 2) }} EGP</div>
                @endif
                <div class="text-lg font-semibold">{{ number_format($product->price, 2) }} EGP</div>
            </div>

            <button
                type="button"
                class="rounded-full px-5 py-2 text-sm font-medium text-white {{ $inStock ? 'bg-slate-900 hover:bg-slate-800' : 'bg-slate-400 cursor-not-allowed' }}"
                data-product-id="{{ $product->id }}"
                data-product-name="{{ $product->name }}"
                data-product-price="{{ number_format($product->price, 2) }}"
                data-product-image="{{ $img ? asset($img) : '' }}"
                data-variants-url="{{ route('store.product.variants', $product) }}"
                onclick="{{ $inStock ? 'window.openVariantModalFromButton(this)' : '' }}"
                @disabled(! $inStock)
            >
                {{ $inStock ? 'اشترِ الآن' : 'غير متاح' }}
            </button>
        </div>
    </div>
</div>
