@extends('admin.layout')

@section('title', 'لوحة التحكم')

@section('content')
    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl border bg-white p-5">
            <div class="text-sm text-slate-600">عدد المنتجات</div>
            <div class="mt-2 text-3xl font-semibold">{{ $productsCount }}</div>
        </div>
        <div class="rounded-2xl border bg-white p-5">
            <div class="text-sm text-slate-600">الأوردرات الجديدة</div>
            <div class="mt-2 text-3xl font-semibold">{{ $newOrdersCount }}</div>
        </div>
        <div class="rounded-2xl border bg-white p-5">
            <div class="text-sm text-slate-600">إجمالي الأوردرات</div>
            <div class="mt-2 text-3xl font-semibold">{{ $ordersCount }}</div>
        </div>
    </div>

    <div class="mt-6 grid gap-3 md:grid-cols-2">
        <a href="{{ route('admin.products.index') }}" class="rounded-2xl border bg-white p-5 hover:bg-slate-50">
            <div class="text-lg font-semibold">إدارة المنتجات</div>
            <div class="mt-1 text-sm text-slate-600">الكميات + الألوان + المقاسات + الصور + العروض.</div>
        </a>
        <a href="{{ route('admin.orders.index') }}" class="rounded-2xl border bg-white p-5 hover:bg-slate-50">
            <div class="text-lg font-semibold">إدارة الأوردرات</div>
            <div class="mt-1 text-sm text-slate-600">قائمة الأوردرات + تفاصيل + إنشاء أوردر جديد.</div>
        </a>
    </div>
@endsection

