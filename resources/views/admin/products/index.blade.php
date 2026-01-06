@extends('admin.layout')

@section('title', 'المنتجات')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold">المنتجات</h1>
        <a href="{{ route('admin.products.create') }}" class="rounded-lg bg-slate-900 px-3 py-2 text-sm text-white hover:bg-slate-800">إضافة منتج</a>
    </div>

    <form class="mt-4 flex gap-2" method="GET" action="{{ route('admin.products.index') }}">
        <input name="q" value="{{ request('q') }}" placeholder="بحث بالاسم..." class="w-full rounded-lg border px-3 py-2 text-sm">
        <button class="rounded-lg border bg-white px-3 py-2 text-sm hover:bg-slate-50">بحث</button>
    </form>

    <div class="mt-4 overflow-hidden rounded-2xl border bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-700">
            <tr>
                <th class="px-4 py-3 text-right">المنتج</th>
                <th class="px-4 py-3 text-right">القسم</th>
                <th class="px-4 py-3 text-right">السعر</th>
                <th class="px-4 py-3 text-right">الحالة</th>
                <th class="px-4 py-3"></th>
            </tr>
            </thead>
            <tbody class="divide-y">
            @forelse($products as $product)
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $product->name }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $product->category?->name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ number_format($product->price, 2) }} EGP</td>
                    <td class="px-4 py-3">
                        @if($product->is_active)
                            <span class="rounded-full bg-emerald-100 px-2 py-1 text-xs text-emerald-800">نشط</span>
                        @else
                            <span class="rounded-full bg-slate-100 px-2 py-1 text-xs text-slate-700">مخفي</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-left">
                        <a class="text-slate-900 hover:underline" href="{{ route('admin.products.edit', $product) }}">تعديل</a>
                    </td>
                </tr>
            @empty
                <tr><td class="px-4 py-10 text-center text-slate-600" colspan="5">لا يوجد منتجات.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $products->links() }}</div>
@endsection

