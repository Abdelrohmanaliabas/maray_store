@extends('admin.layout')

@section('title', 'تعديل منتج')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold">تعديل: {{ $product->name }}</h1>
            <a class="text-sm text-slate-600 hover:underline" target="_blank" href="{{ route('store.product', $product) }}">عرض في الموقع</a>
        </div>
        <a href="{{ route('admin.products.index') }}" class="text-sm text-slate-700 hover:underline">رجوع</a>
    </div>

    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.products._form', ['product' => $product])
        <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800" type="submit">حفظ</button>
    </form>

    @if($product->images->count())
        <div class="mt-6 rounded-2xl border bg-white p-5">
            <h2 class="mb-4 text-base font-semibold">الصور الحالية</h2>
            <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                @foreach($product->images as $img)
                    <div class="overflow-hidden rounded-xl border bg-white">
                        <img class="h-32 w-full object-cover" src="{{ asset($img->path) }}" alt="">
                        <form method="POST" action="{{ route('admin.products.images.destroy', [$product, $img]) }}" class="p-2 text-left">
                            @csrf
                            @method('DELETE')
                            <button class="text-sm text-red-600 hover:underline" type="submit">حذف</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="mt-6 rounded-2xl border bg-white p-5">
        <h2 class="mb-3 text-base font-semibold text-red-700">حذف المنتج</h2>
        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('متأكد من حذف المنتج؟')">
            @csrf
            @method('DELETE')
            <button class="w-full rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700" type="submit">حذف</button>
        </form>
    </div>
@endsection
