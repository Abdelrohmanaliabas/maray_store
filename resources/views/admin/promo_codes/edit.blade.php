@extends('admin.layout')

@section('title', 'تعديل بروموكود')

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold">تعديل: {{ $promoCode->code }}</h1>
            <div class="mt-1 text-sm text-slate-600">آخر تحديث: {{ $promoCode->updated_at->format('Y-m-d H:i') }}</div>
        </div>
        <a href="{{ route('admin.promo-codes.index') }}" class="text-sm text-slate-700 hover:underline">رجوع</a>
    </div>

    <form method="POST" action="{{ route('admin.promo-codes.update', $promoCode) }}" class="rounded-2xl border bg-white p-5 space-y-4">
        @csrf
        @method('PUT')
        @include('admin.promo_codes.partials.form', ['promoCode' => $promoCode])
        <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm text-white hover:bg-slate-800">حفظ التعديلات</button>
    </form>
@endsection

