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
                                <td class="px-3 py-2">
                                    <div>{{ number_format($item['line_total'], 2) }} EGP</div>
                                    @if(($item['line_discount'] ?? 0) > 0)
                                        <div class="text-xs text-emerald-700">خصم: {{ number_format($item['line_discount'], 2) }} EGP</div>
                                    @endif
                                </td>
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
                        <div class="mt-1 text-xs text-slate-600">هتعرف قيمة الخصم قبل تأكيد الطلب.</div>
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

                <div id="checkout-form-error" class="hidden rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800"></div>

                <button type="submit" class="w-full rounded-full bg-slate-900 px-6 py-3 text-sm font-medium text-white hover:bg-slate-800">
                    تأكيد الطلب
                </button>
            </form>
        </div>

        <div class="rounded-2xl border bg-white p-5">
            <h2 class="text-base font-semibold">ملخص الطلب</h2>
            <div class="mt-3 space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-slate-600">إجمالي المنتجات</span><span id="subtotal-amount">{{ number_format($cart['subtotal'], 2) }} EGP</span></div>
                <div class="flex justify-between"><span class="text-slate-600">خصم العروض</span><span id="bulk-discount-amount">{{ number_format($cart['discount_total'], 2) }} EGP</span></div>
                <div class="flex justify-between"><span class="text-slate-600">خصم البروموكود</span><span id="promo-discount-amount">0.00 EGP</span></div>
                <div class="flex justify-between"><span class="text-slate-600">إجمالي الخصومات</span><span id="discount-total-amount">{{ number_format($cart['discount_total'], 2) }} EGP</span></div>
                <div class="flex justify-between text-base font-semibold"><span>الإجمالي</span><span id="final-total-amount">{{ number_format($cart['total'], 2) }} EGP</span></div>
            </div>
            <div class="mt-4 text-xs text-slate-600">الشحن يتم الاتفاق عليه عند التأكيد (يمكنك إضافة ملاحظة).</div>
        </div>
    </div>

    <div id="checkout-confirm-overlay" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
        <div class="w-full max-w-2xl rounded-2xl bg-white shadow-xl">
            <div class="flex items-center justify-between gap-3 border-b p-5">
                <div class="text-base font-semibold">مراجعة الطلب قبل التأكيد</div>
                <button type="button" class="rounded-lg border px-3 py-2 text-sm hover:bg-slate-50" onclick="window.closeCheckoutConfirm()">إغلاق</button>
            </div>

            <div class="p-5 space-y-4">
                <div id="checkout-confirm-error" class="hidden rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800"></div>

                <div class="rounded-xl border">
                    <div class="border-b bg-slate-50 px-4 py-2 text-sm font-semibold">تفاصيل المنتجات</div>
                    <div id="checkout-confirm-items" class="divide-y"></div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-xl border bg-slate-50 p-4">
                        <div class="text-sm font-semibold">ملخص الخصومات</div>
                        <div class="mt-2 space-y-1 text-sm">
                            <div class="flex justify-between"><span class="text-slate-600">إجمالي المنتجات</span><span id="confirm-subtotal">0.00 EGP</span></div>
                            <div class="flex justify-between"><span class="text-slate-600">خصم العروض</span><span id="confirm-bulk-discount">0.00 EGP</span></div>
                            <div class="flex justify-between"><span class="text-slate-600">خصم البروموكود</span><span id="confirm-promo-discount">0.00 EGP</span></div>
                            <div class="flex justify-between font-semibold"><span>إجمالي الخصومات</span><span id="confirm-discount-total">0.00 EGP</span></div>
                            <div class="flex justify-between text-base font-semibold"><span>الإجمالي</span><span id="confirm-total">0.00 EGP</span></div>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-slate-50 p-4">
                        <div class="text-sm font-semibold">بيانات التوصيل</div>
                        <div class="mt-2 space-y-1 text-sm text-slate-700">
                            <div id="confirm-customer-name"></div>
                            <div id="confirm-phone"></div>
                            <div id="confirm-governorate"></div>
                            <div id="confirm-address"></div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-3">
                    <button type="button" class="rounded-full border px-6 py-3 text-sm hover:bg-slate-50" onclick="window.closeCheckoutConfirm()">تعديل</button>
                    <button type="button" class="rounded-full bg-slate-900 px-6 py-3 text-sm font-medium text-white hover:bg-slate-800" onclick="window.confirmCheckoutPlace()">تأكيد الطلب</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const overlay = document.getElementById('checkout-confirm-overlay');
            const errorBox = document.getElementById('checkout-confirm-error');
            const pageErrorBox = document.getElementById('checkout-form-error');
            const itemsEl = document.getElementById('checkout-confirm-items');
            const form = document.querySelector('form[action="{{ route('store.checkout.place') }}"]');
            if (!form) return;

            let allowSubmit = false;

            function money(n) {
                const v = Number(n || 0);
                return v.toFixed(2) + ' EGP';
            }

            function setText(id, value) {
                const el = document.getElementById(id);
                if (el) el.textContent = value;
            }

            function setSidebar(data) {
                setText('subtotal-amount', money(data.subtotal));
                setText('bulk-discount-amount', money(data.bulk_discount));
                setText('promo-discount-amount', money(data.promo_discount));
                setText('discount-total-amount', money(data.discount_total));
                setText('final-total-amount', money(data.total));
            }

            function openConfirm(data) {
                if (pageErrorBox) {
                    pageErrorBox.classList.add('hidden');
                    pageErrorBox.textContent = '';
                }
                if (errorBox) {
                    errorBox.classList.add('hidden');
                    errorBox.textContent = '';
                }

                if (itemsEl) {
                    itemsEl.innerHTML = '';
                    (data.items || []).forEach((it) => {
                        const row = document.createElement('div');
                        row.className = 'px-4 py-3 text-sm';
                        const variant = it.variant ? ` <span class="text-xs text-slate-500">(${it.variant})</span>` : '';
                        const discount = Number(it.line_discount || 0) > 0 ? `<div class="text-xs text-emerald-700">خصم: ${money(it.line_discount)}</div>` : '';
                        row.innerHTML = `
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="font-medium truncate">${it.name || ''}${variant}</div>
                                    <div class="mt-1 text-xs text-slate-600">الكمية: ${it.qty || 0} — سعر القطعة: ${money(it.unit_price)}</div>
                                    ${discount}
                                </div>
                                <div class="text-right font-semibold whitespace-nowrap">${money(it.line_total)}</div>
                            </div>
                        `;
                        itemsEl.appendChild(row);
                    });
                }

                setText('confirm-subtotal', money(data.subtotal));
                setText('confirm-bulk-discount', money(data.bulk_discount));
                setText('confirm-promo-discount', money(data.promo_discount));
                setText('confirm-discount-total', money(data.discount_total));
                setText('confirm-total', money(data.total));

                setText('confirm-customer-name', `الاسم: ${form.customer_name?.value || ''}`);
                setText('confirm-phone', `الهاتف: ${form.phone?.value || ''}${form.phone2?.value ? ' — ' + form.phone2.value : ''}`);
                setText('confirm-governorate', `المحافظة: ${form.governorate?.value || ''}`);
                setText('confirm-address', `العنوان: ${form.address?.value || ''}`);

                setSidebar(data);

                if (overlay) {
                    overlay.classList.remove('hidden');
                    overlay.classList.add('flex');
                }
            }

            window.closeCheckoutConfirm = function () {
                if (!overlay) return;
                overlay.classList.add('hidden');
                overlay.classList.remove('flex');
            };

            window.confirmCheckoutPlace = function () {
                allowSubmit = true;
                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                } else {
                    form.submit();
                }
            };

            async function preview() {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const promo = (form.promo_code?.value || '').trim();

                const res = await fetch(@json(route('store.checkout.preview')), {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf || '',
                    },
                    body: JSON.stringify({ promo_code: promo }),
                });

                const data = await res.json().catch(() => null);
                if (!res.ok) {
                    const msg = data?.errors?.promo_code?.[0] || data?.errors?.cart?.[0] || 'حصل خطأ. حاول تاني.';
                    if (pageErrorBox) {
                        pageErrorBox.classList.remove('hidden');
                        pageErrorBox.textContent = msg;
                        pageErrorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    } else if (errorBox) {
                        errorBox.classList.remove('hidden');
                        errorBox.textContent = msg;
                    } else {
                        alert(msg);
                    }
                    return null;
                }

                return data;
            }

            form.addEventListener('submit', async (e) => {
                if (allowSubmit) return;
                e.preventDefault();

                if (pageErrorBox) {
                    pageErrorBox.classList.add('hidden');
                    pageErrorBox.textContent = '';
                }

                if (typeof form.reportValidity === 'function' && !form.reportValidity()) {
                    return;
                }

                const data = await preview();
                if (!data?.ok) return;

                openConfirm(data);
            });
        })();
    </script>
@endsection
