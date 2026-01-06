@extends('admin.layout')

@section('title', 'إضافة منتج')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-xl font-semibold">إضافة منتج</h1>
        <a href="{{ route('admin.products.index') }}" class="text-sm text-slate-700 hover:underline">رجوع</a>
    </div>

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @include('admin.products._form')
        <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800" type="submit">حفظ</button>
    </form>
@endsection

