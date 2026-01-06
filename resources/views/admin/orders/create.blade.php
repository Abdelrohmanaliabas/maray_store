@extends('admin.layout')

@section('title', 'إضافة أوردر')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-xl font-semibold">إضافة أوردر</h1>
        <a href="{{ route('admin.orders.index') }}" class="text-sm text-slate-700 hover:underline">رجوع</a>
    </div>

    <form method="POST" action="{{ route('admin.orders.store') }}" class="grid gap-6 lg:grid-cols-3">
        @csrf

        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-2xl border bg-white p-5">
                <h2 class="mb-4 text-base font-semibold">المنتجات</h2>
                <div class="overflow-hidden rounded-xl border">
                    <table class="w-full text-sm" id="items-table">
                        <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 text-right">المنتج / اللون / المقاس</th>
                            <th class="px-3 py-2 text-right">الكمية</th>
                            <th class="px-3 py-2"></th>
                        </tr>
                        </thead>
                        <tbody class="divide-y"></tbody>
                    </table>
                </div>
                <div class="mt-3 flex flex-wrap gap-2">
                    <select id="variant-select" class="w-full rounded-lg border px-3 py-2 text-sm md:w-auto md:flex-1">
                        <option value="">اختر منتج...</option>
                        @foreach($variants as $v)
                            <option value="{{ $v->id }}">
                                {{ $v->product->name }} — {{ $v->color }} / {{ $v->size }} (متاح: {{ $v->quantity }})
                            </option>
                        @endforeach
                    </select>
                    <input id="qty-input" type="number" min="1" value="1" class="w-28 rounded-lg border px-3 py-2 text-sm">
                    <button type="button" class="rounded-lg border bg-white px-3 py-2 text-sm hover:bg-slate-50" onclick="window.addOrderItem()">إضافة</button>
                </div>
                <p class="mt-2 text-xs text-slate-600">ملاحظة: الكميات بتتخصم من المخزون عند حفظ الأوردر.</p>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border bg-white p-5">
                <h2 class="mb-4 text-base font-semibold">بيانات العميل</h2>
                <div class="space-y-3">
                    <div>
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
                        <input name="governorate" value="{{ old('governorate') }}" class="w-full rounded-lg border px-3 py-2 text-sm" required>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">العنوان</label>
                        <textarea name="address" rows="3" class="w-full rounded-lg border px-3 py-2 text-sm" required>{{ old('address') }}</textarea>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">ملاحظات (اختياري)</label>
                        <textarea name="notes" rows="2" class="w-full rounded-lg border px-3 py-2 text-sm">{{ old('notes') }}</textarea>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">الحالة</label>
                        <select name="status" class="w-full rounded-lg border px-3 py-2 text-sm">
                            @foreach(['new'=>'new','confirmed'=>'confirmed','shipped'=>'shipped','delivered'=>'delivered','canceled'=>'canceled'] as $k=>$v)
                                <option value="{{ $k }}" @selected(old('status','new')===$k)>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">حفظ الأوردر</button>
        </div>
    </form>

    <script>
        window.addOrderItem = function () {
            const select = document.getElementById('variant-select');
            const qty = parseInt(document.getElementById('qty-input').value || '1', 10);
            const variantId = select.value;
            const label = select.options[select.selectedIndex]?.text || '';

            if (!variantId) return;
            if (!qty || qty < 1) return;

            const tbody = document.querySelector('#items-table tbody');
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="px-3 py-2">
                    <div class="font-medium">${label}</div>
                    <input type="hidden" name="items[variant_id][]" value="${variantId}">
                </td>
                <td class="px-3 py-2">
                    <input name="items[qty][]" type="number" min="1" value="${qty}" class="w-24 rounded-lg border px-2 py-1">
                </td>
                <td class="px-3 py-2 text-left">
                    <button type="button" class="text-red-600 hover:underline" onclick="this.closest('tr').remove()">حذف</button>
                </td>
            `;
            tbody.appendChild(tr);
        };
    </script>
@endsection

