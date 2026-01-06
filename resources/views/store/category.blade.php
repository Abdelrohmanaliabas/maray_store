@extends('store.layout')

@section('title', $category->name)

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold">{{ $category->name }}</h1>
            <div class="mt-1 text-sm text-slate-600">جميع المنتجات في القسم.</div>
        </div>
        <a href="{{ route('store.home') }}" class="text-sm text-slate-700 hover:underline">عرض كل الأقسام</a>
    </div>

    <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3 anim-stagger">
        @forelse($products as $product)
            @include('store._product_card', ['product' => $product])
        @empty
            <div class="rounded-2xl border bg-slate-50 p-6 text-center text-slate-700 md:col-span-2 lg:col-span-3">لا يوجد منتجات.</div>
        @endforelse
    </div>

    <div class="mt-6">{{ $products->links() }}</div>
@endsection
