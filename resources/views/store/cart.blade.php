@extends('store.layout')

@section('title', 'السلة')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-semibold">سلة التسوق</h1>
        @if(count($cart['items']))
            <form method="POST" action="{{ route('store.cart.clear') }}" onsubmit="return confirm('تفريغ السلة؟')">
                @csrf
                <button class="rounded-full border px-5 py-2 text-sm hover:bg-slate-50">تفريغ السلة</button>
            </form>
        @endif
    </div>

    @if(!count($cart['items']))
        <div class="rounded-2xl border bg-slate-50 p-8 text-center text-slate-700">
            السلة فارغة.
            <div class="mt-3">
                <a class="rounded-full bg-slate-900 px-6 py-3 text-sm text-white hover:bg-slate-800" href="{{ route('store.home') }}">تصفح المنتجات</a>
            </div>
        </div>
    @else
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-3">
                @foreach($cart['items'] as $item)
                    @php($img = $item['product']->images->first()?->path)
                    <div class="flex gap-4 rounded-2xl border bg-white p-4">
                        <img class="h-24 w-24 rounded-xl object-cover" src="{{ $img ? asset($img) : '' }}" alt="">
                        <div class="flex-1">
                            <div class="font-semibold">{{ $item['product']->name }}</div>
                            <div class="mt-1 text-sm text-slate-600">
                                اللون: {{ $item['variant']?->color }} — المقاس: {{ $item['variant']?->size }}
                            </div>
                            <div class="mt-1 text-sm text-slate-700">{{ number_format($item['unit_price'], 2) }} EGP</div>
                            @if($item['line_discount'] > 0)
                                <div class="mt-1 text-xs text-emerald-700">خصم: {{ number_format($item['line_discount'], 2) }} EGP</div>
                            @endif
                        </div>

                        <div class="flex flex-col items-end justify-between gap-2">
                            <form method="POST" action="{{ route('store.cart.remove') }}">
                                @csrf
                                <input type="hidden" name="key" value="{{ $item['key'] }}">
                                <button class="text-sm text-red-600 hover:underline" type="submit">حذف</button>
                            </form>

                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('store.cart.update') }}">
                                    @csrf
                                    <input type="hidden" name="key" value="{{ $item['key'] }}">
                                    <input type="hidden" name="qty" value="{{ max(0, $item['qty'] - 1) }}">
                                    <button type="submit" class="rounded-lg border px-3 py-2 text-sm hover:bg-slate-50">-</button>
                                </form>

                                <form method="POST" action="{{ route('store.cart.update') }}" class="flex items-center gap-2">
                                @csrf
                                <input type="hidden" name="key" value="{{ $item['key'] }}">
                                <input name="qty" value="{{ $item['qty'] }}" type="number" min="0" class="w-20 rounded-lg border px-3 py-2 text-sm text-center">
                                <button type="submit" class="rounded-lg border px-3 py-2 text-sm hover:bg-slate-50">تحديث</button>
                                </form>

                                <form method="POST" action="{{ route('store.cart.update') }}">
                                    @csrf
                                    <input type="hidden" name="key" value="{{ $item['key'] }}">
                                    <input type="hidden" name="qty" value="{{ $item['qty'] + 1 }}">
                                    <button type="submit" class="rounded-lg border px-3 py-2 text-sm hover:bg-slate-50">+</button>
                                </form>
                            </div>

                            <div class="text-sm font-semibold">{{ number_format($item['line_total'], 2) }} EGP</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="rounded-2xl border bg-white p-5">
                <h2 class="text-base font-semibold">ملخص السلة</h2>
                <div class="mt-3 space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-slate-600">الإجمالي قبل الخصم</span><span>{{ number_format($cart['subtotal'], 2) }} EGP</span></div>
                    <div class="flex justify-between"><span class="text-slate-600">الخصم</span><span>{{ number_format($cart['discount_total'], 2) }} EGP</span></div>
                    <div class="flex justify-between text-base font-semibold"><span>الإجمالي</span><span>{{ number_format($cart['total'], 2) }} EGP</span></div>
                </div>
                <a href="{{ route('store.checkout') }}" class="mt-5 block rounded-full bg-slate-900 px-6 py-3 text-center text-sm font-medium text-white hover:bg-slate-800">إتمام الشراء</a>
                <div class="mt-2 text-xs text-slate-600">الدفع عند الاستلام.</div>
            </div>
        </div>
    @endif
@endsection
