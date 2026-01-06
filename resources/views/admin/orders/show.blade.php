@extends('admin.layout')

@section('title', 'تفاصيل أوردر')

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold">أوردر {{ $order->order_number }}</h1>
            <div class="mt-1 text-sm text-slate-600">{{ $order->created_at->format('Y-m-d H:i') }}</div>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="text-sm text-slate-700 hover:underline">رجوع</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-2xl border bg-white p-5">
            <h2 class="mb-3 text-base font-semibold">المنتجات</h2>
            <div class="overflow-hidden rounded-xl border">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                    <tr>
                        <th class="px-3 py-2 text-right">المنتج</th>
                        <th class="px-3 py-2 text-right">الاختيارات</th>
                        <th class="px-3 py-2 text-right">الكمية</th>
                        <th class="px-3 py-2 text-right">السعر</th>
                        <th class="px-3 py-2 text-right">الإجمالي</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y">
                    @foreach($order->items as $item)
                        <tr>
                            <td class="px-3 py-2 font-medium">{{ $item->product_name }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $item->color }} {{ $item->size ? '/ '.$item->size : '' }}</td>
                            <td class="px-3 py-2">{{ $item->qty }}</td>
                            <td class="px-3 py-2">{{ number_format($item->unit_price, 2) }} EGP</td>
                            <td class="px-3 py-2">{{ number_format($item->line_total, 2) }} EGP</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex justify-end">
                <div class="w-full max-w-sm space-y-1 text-sm">
                    <div class="flex justify-between"><span class="text-slate-600">الإجمالي قبل الخصم</span><span>{{ number_format($order->subtotal, 2) }} EGP</span></div>
                    <div class="flex justify-between"><span class="text-slate-600">الخصم</span><span>{{ number_format($order->discount_total, 2) }} EGP</span></div>
                    <div class="flex justify-between text-base font-semibold"><span>الإجمالي</span><span>{{ number_format($order->total, 2) }} EGP</span></div>
                    <div class="text-xs text-slate-600">الدفع: عند الاستلام</div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border bg-white p-5">
                <h2 class="mb-3 text-base font-semibold">بيانات العميل</h2>
                <div class="space-y-2 text-sm">
                    <div><span class="text-slate-600">الاسم:</span> {{ $order->customer_name }}</div>
                    <div><span class="text-slate-600">الهاتف:</span> {{ $order->phone }}</div>
                    @if($order->phone2)<div><span class="text-slate-600">هاتف بديل:</span> {{ $order->phone2 }}</div>@endif
                    <div><span class="text-slate-600">المحافظة:</span> {{ $order->governorate }}</div>
                    <div><span class="text-slate-600">العنوان:</span> {{ $order->address }}</div>
                    @if($order->notes)<div><span class="text-slate-600">ملاحظات:</span> {{ $order->notes }}</div>@endif
                </div>
            </div>

            <div class="rounded-2xl border bg-white p-5">
                <h2 class="mb-3 text-base font-semibold">حالة الأوردر</h2>
                <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="flex gap-2">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="w-full rounded-lg border px-3 py-2 text-sm">
                        @foreach(['new','confirmed','shipped','delivered','canceled'] as $s)
                            <option value="{{ $s }}" @selected($order->status===$s)>{{ $s }}</option>
                        @endforeach
                    </select>
                    <button class="rounded-lg bg-slate-900 px-3 py-2 text-sm text-white hover:bg-slate-800">حفظ</button>
                </form>
            </div>
        </div>
    </div>
@endsection

