@extends('admin.layout')

@section('title', 'البروموكود')

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold">البروموكود</h1>
            <div class="mt-1 text-sm text-slate-600">إنشاء وإدارة أكواد الخصم.</div>
        </div>
        <a href="{{ route('admin.promo-codes.create') }}" class="rounded-lg bg-slate-900 px-3 py-2 text-sm text-white hover:bg-slate-800">بروموكود جديد</a>
    </div>

    <form method="GET" class="mb-4 flex gap-2">
        <input name="q" value="{{ request('q') }}" placeholder="بحث بالكود أو الاسم..." class="w-full rounded-lg border px-3 py-2 text-sm">
        <button class="rounded-lg border px-3 py-2 text-sm hover:bg-slate-50">بحث</button>
    </form>

    <div class="overflow-hidden rounded-2xl border bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
            <tr>
                <th class="px-3 py-2 text-right">الكود</th>
                <th class="px-3 py-2 text-right">النوع</th>
                <th class="px-3 py-2 text-right">القيمة</th>
                <th class="px-3 py-2 text-right">الحالة</th>
                <th class="px-3 py-2 text-right">الاستخدام</th>
                <th class="px-3 py-2 text-right">إجراءات</th>
            </tr>
            </thead>
            <tbody class="divide-y">
            @forelse($promoCodes as $p)
                <tr>
                    <td class="px-3 py-2 font-semibold">{{ $p->code }}</td>
                    <td class="px-3 py-2 text-slate-700">{{ $p->discount_type }}</td>
                    <td class="px-3 py-2">
                        @if($p->discount_type === 'percent')
                            {{ number_format($p->value, 2) }}%
                        @else
                            {{ number_format($p->value, 2) }} EGP
                        @endif
                    </td>
                    <td class="px-3 py-2">
                        @if($p->is_active)
                            <span class="rounded-full bg-emerald-50 px-2 py-1 text-xs text-emerald-800">فعال</span>
                        @else
                            <span class="rounded-full bg-slate-100 px-2 py-1 text-xs text-slate-700">غير فعال</span>
                        @endif
                    </td>
                    <td class="px-3 py-2 text-slate-700">
                        {{ $p->used_count }}
                        @if($p->usage_limit) / {{ $p->usage_limit }} @endif
                    </td>
                    <td class="px-3 py-2">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.promo-codes.edit', $p) }}" class="rounded-lg border px-3 py-1 text-sm hover:bg-slate-50">تعديل</a>
                            <form method="POST" action="{{ route('admin.promo-codes.destroy', $p) }}" onsubmit="return confirm('حذف البروموكود؟');">
                                @csrf
                                @method('DELETE')
                                <button class="rounded-lg border border-red-200 bg-red-50 px-3 py-1 text-sm text-red-700 hover:bg-red-100">حذف</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="px-3 py-8 text-center text-slate-600" colspan="6">لا يوجد بروموكود.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $promoCodes->links() }}
    </div>
@endsection

