@extends('admin.layout')

@section('title', 'الأوردرات')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold">الأوردرات</h1>
        <a href="{{ route('admin.orders.create') }}" class="rounded-lg bg-slate-900 px-3 py-2 text-sm text-white hover:bg-slate-800">إضافة أوردر</a>
    </div>

    <form class="mt-4 grid gap-2 md:grid-cols-3" method="GET" action="{{ route('admin.orders.index') }}">
        <input name="q" value="{{ request('q') }}" placeholder="بحث (رقم/اسم/هاتف)..." class="rounded-lg border px-3 py-2 text-sm md:col-span-2">
        <select name="status" class="rounded-lg border px-3 py-2 text-sm">
            <option value="">كل الحالات</option>
            @foreach(['new'=>'جديد','confirmed'=>'مؤكد','shipped'=>'تم الشحن','delivered'=>'تم التسليم','canceled'=>'ملغي'] as $k=>$v)
                <option value="{{ $k }}" @selected(request('status')===$k)>{{ $v }}</option>
            @endforeach
        </select>
        <div class="md:col-span-3 flex gap-2">
            <button class="rounded-lg border bg-white px-3 py-2 text-sm hover:bg-slate-50">تطبيق</button>
            <a class="rounded-lg border bg-white px-3 py-2 text-sm hover:bg-slate-50" href="{{ route('admin.orders.index') }}">مسح</a>
        </div>
    </form>

    <div class="mt-4 overflow-hidden rounded-2xl border bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-700">
            <tr>
                <th class="px-4 py-3 text-right">الرقم</th>
                <th class="px-4 py-3 text-right">العميل</th>
                <th class="px-4 py-3 text-right">الحالة</th>
                <th class="px-4 py-3 text-right">الإجمالي</th>
                <th class="px-4 py-3 text-right">التاريخ</th>
                <th class="px-4 py-3"></th>
            </tr>
            </thead>
            <tbody class="divide-y">
            @forelse($orders as $order)
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $order->order_number }}</td>
                    <td class="px-4 py-3">{{ $order->customer_name }}<div class="text-xs text-slate-600">{{ $order->phone }}</div></td>
                    <td class="px-4 py-3"><span class="rounded-full bg-slate-100 px-2 py-1 text-xs text-slate-700">{{ $order->status }}</span></td>
                    <td class="px-4 py-3">{{ number_format($order->total, 2) }} EGP</td>
                    <td class="px-4 py-3 text-slate-600">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                    <td class="px-4 py-3 text-left"><a class="hover:underline" href="{{ route('admin.orders.show', $order) }}">تفاصيل</a></td>
                </tr>
            @empty
                <tr><td class="px-4 py-10 text-center text-slate-600" colspan="6">لا يوجد أوردرات.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>
@endsection

