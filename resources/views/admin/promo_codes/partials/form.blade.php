@php($p = $promoCode)

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium">الكود</label>
        <input name="code" value="{{ old('code', $p?->code) }}" class="w-full rounded-lg border px-3 py-2 text-sm" required>
        <div class="mt-1 text-xs text-slate-600">سيتم حفظ الكود بحروف كبيرة بدون مسافات.</div>
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium">الاسم (اختياري)</label>
        <input name="name" value="{{ old('name', $p?->name) }}" class="w-full rounded-lg border px-3 py-2 text-sm">
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium">نوع الخصم</label>
        <select name="discount_type" class="w-full rounded-lg border px-3 py-2 text-sm" required>
            @php($t = old('discount_type', $p?->discount_type ?? 'percent'))
            <option value="percent" @selected($t==='percent')>% نسبة</option>
            <option value="fixed" @selected($t==='fixed')>مبلغ ثابت</option>
        </select>
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium">قيمة الخصم</label>
        <input name="value" value="{{ old('value', $p?->value) }}" type="number" step="0.01" min="0.01" class="w-full rounded-lg border px-3 py-2 text-sm" required>
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium">حد أدنى للأوردر (اختياري)</label>
        <input name="min_order_total" value="{{ old('min_order_total', $p?->min_order_total ?? 0) }}" type="number" step="0.01" min="0" class="w-full rounded-lg border px-3 py-2 text-sm">
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium">أقصى خصم (اختياري)</label>
        <input name="max_discount" value="{{ old('max_discount', $p?->max_discount) }}" type="number" step="0.01" min="0" class="w-full rounded-lg border px-3 py-2 text-sm">
        <div class="mt-1 text-xs text-slate-600">مفيد مع خصم النسبة.</div>
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium">حد الاستخدام (اختياري)</label>
        <input name="usage_limit" value="{{ old('usage_limit', $p?->usage_limit) }}" type="number" min="1" class="w-full rounded-lg border px-3 py-2 text-sm">
    </div>

    <div class="flex items-end gap-2">
        @php($active = old('is_active', $p?->is_active ?? true))
        <label class="inline-flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" class="rounded border" @checked($active)>
            <span>فعال</span>
        </label>
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium">بداية (اختياري)</label>
        <input name="starts_at" value="{{ old('starts_at', $p?->starts_at?->format('Y-m-d\\TH:i')) }}" type="datetime-local" class="w-full rounded-lg border px-3 py-2 text-sm">
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium">نهاية (اختياري)</label>
        <input name="ends_at" value="{{ old('ends_at', $p?->ends_at?->format('Y-m-d\\TH:i')) }}" type="datetime-local" class="w-full rounded-lg border px-3 py-2 text-sm">
    </div>
</div>

