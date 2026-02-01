@extends('store.layout')

@section('title', 'تم الطلب')

@section('content')
    <div class="mx-auto max-w-2xl rounded-2xl border bg-white p-8 text-center">
        <h1 class="text-2xl font-semibold">تم استلام طلبك</h1>
        <div class="mt-2 text-sm text-slate-600">رقم الطلب: <span class="font-semibold">{{ $order->order_number }}</span></div>

        <div class="mt-4 rounded-xl border bg-slate-50 p-4 text-sm text-slate-700">
            <div class="flex justify-between"><span class="text-slate-600">إجمالي المنتجات</span><span>{{ number_format($order->subtotal, 2) }} EGP</span></div>
            <div class="mt-1 flex justify-between"><span class="text-slate-600">إجمالي الخصومات</span><span class="text-emerald-700">{{ number_format($order->discount_total, 2) }} EGP</span></div>
            <div class="mt-1 flex justify-between text-base font-semibold"><span>الإجمالي</span><span>{{ number_format($order->total, 2) }} EGP</span></div>
        </div>

        <div class="mt-4 rounded-xl bg-slate-50 p-4 text-sm text-slate-700">
            الدفع عند الاستلام. هنتواصل معاك قريبًا لتأكيد البيانات.
        </div>
        <div class="mt-6 flex flex-wrap justify-center gap-3">
            <a href="{{ route('store.home') }}" class="rounded-full bg-slate-900 px-6 py-3 text-sm text-white hover:bg-slate-800">متابعة التسوق</a>
            <a href="{{ route('store.cart') }}" class="rounded-full border px-6 py-3 text-sm hover:bg-slate-50">السلة</a>
        </div>
    </div>
@endsection
