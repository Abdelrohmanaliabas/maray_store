@extends('store.layout')

@section('title', 'إتمام الطلب')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-semibold">إتمام الطلب</h1>
        <a href="{{ route('store.cart') }}" class="text-sm text-slate-700 hover:underline">رجوع للسلة</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-2xl border bg-white p-5">
                <h2 class="mb-4 text-base font-semibold">المنتجات</h2>
                <div class="overflow-hidden rounded-xl border">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 text-right">المنتج</th>
                            <th class="px-3 py-2 text-right">السعر</th>
                            <th class="px-3 py-2 text-right">الكمية</th>
                            <th class="px-3 py-2 text-right">الإجمالي</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y">
                        @foreach($cart['items'] as $item)
                            <tr>
                                <td class="px-3 py-2">
                                    <div class="font-medium">{{ $item['product']->name }}</div>
                                    <div class="text-xs text-slate-600">{{ $item['variant']?->color }} / {{ $item['variant']?->size }}</div>
                                </td>
                                <td class="px-3 py-2">{{ number_format($item['unit_price'], 2) }} EGP</td>
                                <td class="px-3 py-2">{{ $item['qty'] }}</td>
                                <td class="px-3 py-2">{{ number_format($item['line_total'], 2) }} EGP</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <form method="POST" action="{{ route('store.checkout.place') }}" class="rounded-2xl border bg-white p-5 space-y-5">
                @csrf
                <h2 class="text-base font-semibold">بيانات التوصيل</h2>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm font-medium">الاسم</label>
                        <input name="customer_name" value="{{ old('customer_name') }}" class="w-full rounded-lg border px-3 py-2 text-sm" required>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">رقم الهاتف</label>
                        <input name="phone" value="{{ old('phone') }}" class="w-full rounded-lg border px-3 py-2 text-sm" required>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">هاتف بديل (اختياري)</label>
                        <input name="phone2" value="{{ old('phone2') }}" class="w-full rounded-lg border px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">المحافظة</label>
                        @php($govs = ['القاهرة','الجيزة','الإسكندرية','الدقهلية','الشرقية','الغربية','المنوفية','البحيرة','كفر الشيخ','دمياط','بورسعيد','الإسماعيلية','السويس','شمال سيناء','جنوب سيناء','بني سويف','الفيوم','المنيا','أسيوط','سوهاج','قنا','الأقصر','أسوان','البحر الأحمر','مطروح','الوادي الجديد'])
                        <select name="governorate" class="w-full rounded-lg border px-3 py-2 text-sm" required>
                            <option value="">اختر المحافظة</option>
                            @foreach($govs as $g)
                                <option value="{{ $g }}" @selected(old('governorate')===$g)>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm font-medium">تفاصيل العنوان</label>
                        <textarea name="address" rows="3" class="w-full rounded-lg border px-3 py-2 text-sm" required>{{ old('address') }}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm font-medium">ملاحظات (اختياري)</label>
                        <textarea name="notes" rows="2" class="w-full rounded-lg border px-3 py-2 text-sm">{{ old('notes') }}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm font-medium">بروموكود (اختياري)</label>
                        <input name="promo_code" value="{{ old('promo_code') }}" class="w-full rounded-lg border px-3 py-2 text-sm" placeholder="مثال: SAVE10">
                        <div class="mt-1 text-xs text-slate-600">لو الكود صحيح هيتم تطبيق الخصم عند تأكيد الطلب.</div>
                    </div>
                </div>

                <div class="rounded-2xl border bg-slate-50 p-4">
                    <div class="text-sm font-semibold">طريقة الدفع</div>
                    <div class="mt-2 flex items-center gap-2 text-sm">
                        <input type="radio" checked class="accent-slate-900">
                        <div>دفع عند الاستلام</div>
                    </div>
                    <div class="mt-1 text-xs text-slate-600">الدفع بالكامل عند استلام الطلب.</div>
                </div>

                <button type="submit" class="w-full rounded-full bg-slate-900 px-6 py-3 text-sm font-medium text-white hover:bg-slate-800">
                    تأكيد الطلب
                </button>
            </form>
        </div>

        <div class="rounded-2xl border bg-white p-5">
            <h2 class="text-base font-semibold">ملخص الطلب</h2>
            <div class="mt-3 space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-slate-600">إجمالي المنتجات</span><span>{{ number_format($cart['subtotal'], 2) }} EGP</span></div>
                <div class="flex justify-between"><span class="text-slate-600">الخصم</span><span>{{ number_format($cart['discount_total'], 2) }} EGP</span></div>
                <div class="flex justify-between text-base font-semibold"><span>الإجمالي</span><span>{{ number_format($cart['total'], 2) }} EGP</span></div>
            </div>
            <div class="mt-4 text-xs text-slate-600">الشحن يتم الاتفاق عليه عند التأكيد (يمكنك إضافة ملاحظة).</div>
        </div>
    </div>
@endsection
