@php($isEdit = isset($product))

<div class="grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 space-y-6">
        <div class="rounded-2xl border bg-white p-5">
            <h2 class="mb-4 text-base font-semibold">بيانات المنتج</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium">اسم المنتج</label>
                    <input name="name" value="{{ old('name', $product->name ?? '') }}" class="w-full rounded-lg border px-3 py-2 text-sm" required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Slug (اختياري)</label>
                    <input name="slug" value="{{ old('slug', $product->slug ?? '') }}" class="w-full rounded-lg border px-3 py-2 text-sm" placeholder="مثال: essential-tshirt">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">القسم</label>
                    <select name="category_id" class="w-full rounded-lg border px-3 py-2 text-sm">
                        <option value="">بدون</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id ?? '') == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">السعر</label>
                    <input name="price" type="number" step="0.01" value="{{ old('price', $product->price ?? '') }}" class="w-full rounded-lg border px-3 py-2 text-sm" required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">السعر قبل الخصم (اختياري)</label>
                    <input name="compare_at_price" type="number" step="0.01" value="{{ old('compare_at_price', $product->compare_at_price ?? '') }}" class="w-full rounded-lg border px-3 py-2 text-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium">وصف (اختياري)</label>
                    <textarea name="description" rows="4" class="w-full rounded-lg border px-3 py-2 text-sm">{{ old('description', $product->description ?? '') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_active" value="1" class="rounded border" @checked(old('is_active', $product->is_active ?? true))>
                        المنتج ظاهر في الموقع
                    </label>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border bg-white p-5">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-semibold">المخزون (ألوان/مقاسات)</h2>
                <button type="button" class="rounded-lg border bg-white px-3 py-2 text-sm hover:bg-slate-50" onclick="window.addVariantRow()">+ إضافة صف</button>
            </div>
            <p class="mt-1 text-sm text-slate-600">العميل هيختار من الموجود فقط.</p>

            <div class="mt-4 overflow-hidden rounded-xl border">
                <table class="w-full text-sm" id="variants-table">
                    <thead class="bg-slate-50">
                    <tr>
                        <th class="px-3 py-2 text-right">اللون</th>
                        <th class="px-3 py-2 text-right">المقاس</th>
                        <th class="px-3 py-2 text-right">الكمية</th>
                        <th class="px-3 py-2"></th>
                    </tr>
                    </thead>
                    <tbody class="divide-y">
                    @if(old('variants.color'))
                        @foreach(old('variants.color', []) as $i => $c)
                            <tr>
                                <td class="px-3 py-2"><input list="color-options" name="variants[color][]" value="{{ $c }}" class="w-full rounded-lg border px-2 py-1" placeholder="اختر لون"></td>
                                <td class="px-3 py-2"><input list="size-options" name="variants[size][]" value="{{ old('variants.size.'.$i) }}" class="w-full rounded-lg border px-2 py-1" placeholder="اختر مقاس"></td>
                                <td class="px-3 py-2"><input name="variants[quantity][]" value="{{ old('variants.quantity.'.$i) }}" type="number" min="0" class="w-full rounded-lg border px-2 py-1"></td>
                                <td class="px-3 py-2 text-left"><button type="button" class="text-red-600 hover:underline" onclick="this.closest('tr').remove()">حذف</button></td>
                            </tr>
                        @endforeach
                    @else
                        @foreach(($product->variants ?? collect()) as $v)
                            <tr>
                                <td class="px-3 py-2"><input list="color-options" name="variants[color][]" value="{{ $v->color }}" class="w-full rounded-lg border px-2 py-1" placeholder="اختر لون"></td>
                                <td class="px-3 py-2"><input list="size-options" name="variants[size][]" value="{{ $v->size }}" class="w-full rounded-lg border px-2 py-1" placeholder="اختر مقاس"></td>
                                <td class="px-3 py-2"><input name="variants[quantity][]" value="{{ $v->quantity }}" type="number" min="0" class="w-full rounded-lg border px-2 py-1"></td>
                                <td class="px-3 py-2 text-left"><button type="button" class="text-red-600 hover:underline" onclick="this.closest('tr').remove()">حذف</button></td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>

            <datalist id="color-options">
                <option value="Black"></option>
                <option value="White"></option>
                <option value="Red"></option>
                <option value="Blue"></option>
                <option value="Green"></option>
                <option value="Gray"></option>
                <option value="Beige"></option>
                <option value="Brown"></option>
                <option value="Navy"></option>
                <option value="أسود"></option>
                <option value="أبيض"></option>
                <option value="أحمر"></option>
                <option value="أزرق"></option>
                <option value="أخضر"></option>
                <option value="رمادي"></option>
                <option value="بيج"></option>
                <option value="بني"></option>
                <option value="كحلي"></option>
            </datalist>

            <datalist id="size-options">
                <option value="XS"></option>
                <option value="S"></option>
                <option value="M"></option>
                <option value="L"></option>
                <option value="XL"></option>
                <option value="XXL"></option>
                <option value="3XL"></option>
            </datalist>
        </div>

        <div class="rounded-2xl border bg-white p-5">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-semibold">عروض على الكمية</h2>
                <button type="button" class="rounded-lg border bg-white px-3 py-2 text-sm hover:bg-slate-50" onclick="window.addDiscountRow()">+ إضافة عرض</button>
            </div>
            <p class="mt-1 text-sm text-slate-600">مثال: من 2 قطعة خصم 10%.</p>

            <div class="mt-4 overflow-hidden rounded-xl border">
                <table class="w-full text-sm" id="discounts-table">
                    <thead class="bg-slate-50">
                    <tr>
                        <th class="px-3 py-2 text-right">من كمية</th>
                        <th class="px-3 py-2 text-right">النوع</th>
                        <th class="px-3 py-2 text-right">القيمة</th>
                        <th class="px-3 py-2 text-right">مفعل</th>
                        <th class="px-3 py-2"></th>
                    </tr>
                    </thead>
                    <tbody class="divide-y">
                    @if(old('discounts.min_qty'))
                        @foreach(old('discounts.min_qty', []) as $i => $min)
                            <tr>
                                <td class="px-3 py-2"><input name="discounts[min_qty][]" value="{{ $min }}" type="number" min="2" class="w-full rounded-lg border px-2 py-1"></td>
                                <td class="px-3 py-2">
                                    <select name="discounts[discount_type][]" class="w-full rounded-lg border px-2 py-1">
                                        <option value="percent" @selected(old('discounts.discount_type.'.$i) === 'percent')>%</option>
                                        <option value="fixed" @selected(old('discounts.discount_type.'.$i) === 'fixed')>مبلغ/قطعة</option>
                                    </select>
                                </td>
                                <td class="px-3 py-2"><input name="discounts[value][]" value="{{ old('discounts.value.'.$i) }}" type="number" step="0.01" min="0" class="w-full rounded-lg border px-2 py-1"></td>
                                <td class="px-3 py-2"><input name="discounts[is_active][{{ $i }}]" value="1" type="checkbox" class="rounded border" @checked(old('discounts.is_active.'.$i, true))></td>
                                <td class="px-3 py-2 text-left"><button type="button" class="text-red-600 hover:underline" onclick="this.closest('tr').remove()">حذف</button></td>
                            </tr>
                        @endforeach
                    @else
                        @foreach(($product->bulkDiscounts ?? collect()) as $i => $d)
                            <tr>
                                <td class="px-3 py-2"><input name="discounts[min_qty][]" value="{{ $d->min_qty }}" type="number" min="2" class="w-full rounded-lg border px-2 py-1"></td>
                                <td class="px-3 py-2">
                                    <select name="discounts[discount_type][]" class="w-full rounded-lg border px-2 py-1">
                                        <option value="percent" @selected($d->discount_type === 'percent')>%</option>
                                        <option value="fixed" @selected($d->discount_type === 'fixed')>مبلغ/قطعة</option>
                                    </select>
                                </td>
                                <td class="px-3 py-2"><input name="discounts[value][]" value="{{ $d->value }}" type="number" step="0.01" min="0" class="w-full rounded-lg border px-2 py-1"></td>
                                <td class="px-3 py-2"><input name="discounts[is_active][{{ $i }}]" value="1" type="checkbox" class="rounded border" @checked($d->is_active)></td>
                                <td class="px-3 py-2 text-left"><button type="button" class="text-red-600 hover:underline" onclick="this.closest('tr').remove()">حذف</button></td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="rounded-2xl border bg-white p-5">
            <h2 class="mb-3 text-base font-semibold">صور المنتج</h2>
            <input type="file" name="images[]" multiple accept="image/*" class="w-full rounded-lg border px-3 py-2 text-sm">
        </div>
    </div>
</div>

<script>
    window.addVariantRow = function () {
        const tbody = document.querySelector('#variants-table tbody');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="px-3 py-2"><input list="color-options" name="variants[color][]" placeholder="اختر لون" class="w-full rounded-lg border px-2 py-1"></td>
            <td class="px-3 py-2"><input list="size-options" name="variants[size][]" placeholder="اختر مقاس" class="w-full rounded-lg border px-2 py-1"></td>
            <td class="px-3 py-2"><input name="variants[quantity][]" type="number" min="0" value="0" class="w-full rounded-lg border px-2 py-1"></td>
            <td class="px-3 py-2 text-left"><button type="button" class="text-red-600 hover:underline" onclick="this.closest('tr').remove()">حذف</button></td>
        `;
        tbody.appendChild(tr);
    };

    window.addDiscountRow = function () {
        const tbody = document.querySelector('#discounts-table tbody');
        const index = tbody.children.length;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="px-3 py-2"><input name="discounts[min_qty][]" type="number" min="2" value="2" class="w-full rounded-lg border px-2 py-1"></td>
            <td class="px-3 py-2">
                <select name="discounts[discount_type][]" class="w-full rounded-lg border px-2 py-1">
                    <option value="percent">%</option>
                    <option value="fixed">مبلغ/قطعة</option>
                </select>
            </td>
            <td class="px-3 py-2"><input name="discounts[value][]" type="number" step="0.01" min="0" value="0" class="w-full rounded-lg border px-2 py-1"></td>
            <td class="px-3 py-2"><input name="discounts[is_active][${index}]" value="1" type="checkbox" class="rounded border" checked></td>
            <td class="px-3 py-2 text-left"><button type="button" class="text-red-600 hover:underline" onclick="this.closest('tr').remove()">حذف</button></td>
        `;
        tbody.appendChild(tr);
    };
</script>
