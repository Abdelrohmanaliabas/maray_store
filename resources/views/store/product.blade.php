@extends('store.layout')

@section('title', $product->name)

@section('content')
    @php
        $images = $product->images;
        $main = $images->first()?->path;
        $hasDiscount = $product->compare_at_price && $product->compare_at_price > $product->price;
    @endphp

    <div class="grid gap-8 lg:grid-cols-2">
        <div>
            <div class="overflow-hidden rounded-2xl border bg-white">
                <img id="pd-main" class="aspect-square w-full object-cover" src="{{ $main ? asset($main) : '' }}" alt="{{ $product->name }}">
            </div>

            @if($images->count() > 1)
                <div class="mt-3 flex gap-2 overflow-auto">
                    @foreach($images as $img)
                        <button type="button" class="overflow-hidden rounded-xl border" onclick='document.getElementById("pd-main").src = @json(asset($img->path))'>
                            <img class="h-20 w-20 object-cover" src="{{ asset($img->path) }}" alt="">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <h1 class="text-2xl font-semibold">{{ $product->name }}</h1>

            <div class="mt-3 flex items-end gap-3">
                <div class="text-2xl font-semibold">{{ number_format($product->price, 2) }} EGP</div>
                @if($hasDiscount)
                    <div class="text-sm text-slate-500 line-through">{{ number_format($product->compare_at_price, 2) }} EGP</div>
                    <div class="rounded-md bg-red-600 px-2 py-1 text-xs font-medium text-white">خصم</div>
                @endif
            </div>

            @if($product->description)
                <div class="mt-4 text-sm leading-7 text-slate-700">{{ $product->description }}</div>
            @endif

            <div class="mt-6 rounded-2xl border bg-slate-50 p-4">
                <div class="text-sm font-semibold">اختيار اللون والمقاس</div>
                <div class="mt-1 text-sm text-slate-600">اضغط على "اشترِ الآن" لاختيار المتغيرات وإضافتها للسلة.</div>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <button
                    type="button"
                    class="flex-1 rounded-full bg-slate-900 px-6 py-3 text-sm font-medium text-white hover:bg-slate-800"
                    data-product-id="{{ $product->id }}"
                    data-product-name="{{ $product->name }}"
                    data-product-price="{{ number_format($product->price, 2) }}"
                    data-product-image="{{ $main ? asset($main) : '' }}"
                    data-variants-url="{{ route('store.product.variants', $product) }}"
                    onclick="window.openVariantModalFromButton(this)"
                >
                    اشترِ الآن
                </button>
                <a href="{{ route('store.cart') }}" class="rounded-full border px-6 py-3 text-sm hover:bg-slate-50">السلة</a>
            </div>

            <div class="mt-6 text-xs text-slate-600">
                الدفع عند الاستلام.
            </div>
        </div>
    </div>
@endsection
