@extends('admin.layout')

@section('title', 'بروموكود جديد')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold">بروموكود جديد</h1>
            <div class="mt-1 text-sm text-slate-600">إنشاء كود خصم جديد.</div>
        </div>
        <a href="{{ route('admin.promo-codes.index') }}" class="text-sm text-slate-700 hover:underline">رجوع</a>
    </div>

    <form method="POST" action="{{ route('admin.promo-codes.store') }}" class="rounded-2xl border bg-white p-5 space-y-4">
        @csrf
        @include('admin.promo_codes.partials.form', ['promoCode' => null])
        <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm text-white hover:bg-slate-800">حفظ</button>
    </form>
@endsection

