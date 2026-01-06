@extends('store.layout')

@section('title', 'المنتجات')

@section('content')
    <div class="mb-6 flex items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold">أفضل المنتجات</h1>
            @if(request('q'))
                <div class="mt-1 text-sm text-slate-600">نتائج البحث عن: <span class="font-medium">{{ request('q') }}</span></div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 sm:gap-5 md:grid-cols-3 lg:grid-cols-4 anim-stagger">
        @foreach($products as $product)
            @include('store._product_card', ['product' => $product])
        @endforeach
    </div>

    <div class="mt-6">{{ $products->links() }}</div>
@endsection
