@extends('store.layout')

@section('title', $product->name)

@section('content')
    @php
        $images = $product->images;
        $main = $images->first()?->path;
        $hasDiscount = $product->compare_at_price && $product->compare_at_price > $product->price;
        $availableVariants = $product->variants->where('quantity', '>', 0);
        $colors = $availableVariants->groupBy('color');
        $variantsJson = $availableVariants
            ->map(function ($v) {
                return [
                    'id' => $v->id,
                    'color' => $v->color,
                    'size' => $v->size,
                    'qty' => (int) $v->quantity,
                ];
            })
            ->values();
    @endphp

    <div class="grid gap-8 lg:grid-cols-2">
        <div>
            <div class="overflow-hidden rounded-2xl border bg-white">
                <img id="pd-main" class="aspect-square w-full object-cover" src="{{ $main ? asset($main) : '' }}" alt="{{ $product->name }}">
            </div>

            @if($images->count() > 1)
                <div class="mt-3 flex gap-2 overflow-auto">
                    @foreach($images as $img)
                        <button type="button" class="overflow-hidden rounded-xl border" onclick='document.getElementById("pd-main").src = @json(asset($img->path))'>
                            <img class="h-20 w-20 object-cover" src="{{ asset($img->path) }}" alt="">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <h1 class="text-2xl font-semibold">{{ $product->name }}</h1>

            <div class="mt-3 flex items-end gap-3">
                <div class="text-2xl font-semibold">{{ number_format($product->price, 2) }} EGP</div>
                @if($hasDiscount)
                    <div class="text-sm text-slate-500 line-through">{{ number_format($product->compare_at_price, 2) }} EGP</div>
                    <div class="rounded-md bg-red-600 px-2 py-1 text-xs font-medium text-white">خصم</div>
                @endif
            </div>

            @if($product->description)
                <div class="mt-4 text-sm leading-7 text-slate-700">{{ $product->description }}</div>
            @endif

            @include('store._bulk_discounts', ['product' => $product])

            <div class="mt-6 rounded-2xl border bg-white p-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="text-sm font-semibold">اختيار اللون والمقاس</div>
                    <div class="text-xs text-slate-500">المتاح فقط</div>
                </div>

                @if($colors->count() === 0)
                    <div class="mt-3 rounded-xl bg-slate-50 p-4 text-sm text-slate-700">غير متاح حالياً.</div>
                @else
                    <div class="mt-4 grid gap-5 md:grid-cols-2">
                        <div>
                            <div class="mb-2 text-sm font-medium text-slate-700">اللون</div>
                            <div class="flex flex-wrap gap-2" id="pd-colors">
                                @foreach($colors as $colorName => $group)
                                    <button
                                        type="button"
                                        class="pd-color relative h-11 w-11 rounded-xl border border-slate-200 bg-white transition hover:bg-slate-50"
                                        data-color="{{ $colorName }}"
                                        title="{{ $colorName }}"
                                    >
                                        <span class="pd-swatch absolute inset-1 rounded-lg"></span>
                                    </button>
                                @endforeach
                            </div>
                            <div class="mt-2 text-xs text-slate-600" id="pd-color-label">اختر لون</div>
                        </div>

                        <div>
                            <div class="mb-2 text-sm font-medium text-slate-700">المقاس</div>
                            <div class="flex flex-wrap gap-2" id="pd-sizes"></div>
                            <div class="mt-2 text-xs text-slate-600" id="pd-size-label">اختر مقاس</div>
                        </div>
                    </div>

                    <div class="mt-5 flex flex-wrap items-center gap-3">
                        <div class="flex items-center gap-2">
                            <button type="button" class="rounded-xl border px-3 py-2 text-sm hover:bg-slate-50" onclick="window.pdDec()">-</button>
                            <input id="pd-qty" type="number" min="1" value="1" class="w-20 rounded-xl border px-3 py-2 text-center text-sm">
                            <button type="button" class="rounded-xl border px-3 py-2 text-sm hover:bg-slate-50" onclick="window.pdInc()">+</button>
                        </div>
                        <button id="pd-add" type="button" class="flex-1 rounded-full bg-slate-900 px-6 py-3 text-sm font-semibold text-white opacity-50" disabled onclick="window.pdAddToCart()">
                            إضافة إلى السلة
                        </button>
                        <a href="{{ route('store.cart') }}" class="rounded-full border px-6 py-3 text-sm hover:bg-slate-50">السلة</a>
                    </div>
                    <div class="mt-2 text-xs text-slate-600" id="pd-hint">اختار اللون والمقاس.</div>
                @endif
            </div>

            @if($product->bulkDiscounts->where('is_active', true)->count())
                <div class="mt-4 rounded-2xl border bg-slate-50 p-5">
                    <div class="text-sm font-semibold">العروض على الكمية</div>
                    <div class="mt-3 space-y-2 text-sm text-slate-700">
                        @foreach($product->bulkDiscounts->where('is_active', true)->sortBy('min_qty') as $rule)
                            <div class="flex items-center justify-between rounded-xl bg-white px-4 py-3">
                                <div>من {{ $rule->min_qty }} قطعة</div>
                                <div class="font-semibold">
                                    @if($rule->discount_type === 'percent')
                                        خصم {{ rtrim(rtrim(number_format($rule->value, 2), '0'), '.') }}%
                                    @else
                                        خصم {{ rtrim(rtrim(number_format($rule->value, 2), '0'), '.') }} EGP / قطعة
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mt-6 text-xs text-slate-600">
                الدفع عند الاستلام.
            </div>
        </div>
    </div>

    @if(isset($relatedProducts) && $relatedProducts->count())
        <div class="mt-10">
            <div class="mb-4 flex items-end justify-between">
                <div>
                    <div class="text-xl font-semibold">منتجات مشابهة</div>
                    <div class="mt-1 text-sm text-slate-600">من نفس الفئة أولاً ثم باقي الفئات.</div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 sm:gap-5 md:grid-cols-3 lg:grid-cols-4 anim-stagger">
                @foreach($relatedProducts as $rp)
                    @include('store._product_card', ['product' => $rp])
                @endforeach
            </div>
        </div>
    @endif

    <script>
        (function () {
            const variants = @json($variantsJson);
            const csrf = document.querySelector('meta[name="csrf-token"]').content;

            const pd = {
                color: null,
                size: null,
                variantId: null,
                byColor: {},
            };

            variants.forEach(v => {
                if (!pd.byColor[v.color]) pd.byColor[v.color] = [];
                pd.byColor[v.color].push(v);
            });

            const colorsEl = document.getElementById('pd-colors');
            const sizesEl = document.getElementById('pd-sizes');
            const addBtn = document.getElementById('pd-add');

            function renderColors() {
                if (!colorsEl) return;
                [...colorsEl.querySelectorAll('.pd-color')].forEach(btn => {
                    const active = btn.dataset.color === pd.color;
                    btn.classList.toggle('border-slate-900', active);
                    btn.classList.toggle('ring-2', active);
                    btn.classList.toggle('ring-slate-900/20', active);
                    const swatch = btn.querySelector('.pd-swatch');
                    if (swatch) swatch.style.background = colorToBg(btn.dataset.color || '');
                });
                const label = document.getElementById('pd-color-label');
                if (label) label.textContent = pd.color ? `اللون: ${pd.color}` : 'اختر لون';
            }

            function renderSizes() {
                if (!sizesEl) return;
                sizesEl.innerHTML = '';
                pd.size = null;
                pd.variantId = null;

                const label = document.getElementById('pd-size-label');
                if (!pd.color) {
                    if (label) label.textContent = 'اختر مقاس';
                    update();
                    return;
                }

                const options = (pd.byColor[pd.color] || []).slice().sort((a, b) => a.size.localeCompare(b.size));
                options.forEach(v => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'h-11 min-w-11 rounded-xl border border-slate-200 bg-white px-3 text-sm font-semibold transition hover:bg-slate-50';
                    btn.textContent = v.size;
                    btn.onclick = () => {
                        pd.size = v.size;
                        pd.variantId = v.id;
                        [...sizesEl.querySelectorAll('button')].forEach(b => {
                            const active = b.textContent === v.size;
                            b.classList.toggle('bg-slate-900', active);
                            b.classList.toggle('text-white', active);
                            b.classList.toggle('border-slate-900', active);
                        });
                        if (label) label.textContent = `المقاس: ${v.size} (متاح: ${v.qty})`;
                        update();
                    };
                    sizesEl.appendChild(btn);
                });

                if (label) label.textContent = 'اختر مقاس';
                update();
            }

            function update() {
                const ok = !!pd.variantId;
                if (addBtn) {
                    addBtn.disabled = !ok;
                    addBtn.classList.toggle('opacity-50', !ok);
                }
                const hint = document.getElementById('pd-hint');
                if (hint) hint.textContent = ok ? 'جاهز للإضافة.' : 'اختار اللون والمقاس.';
            }

            function colorToBg(name) {
                const n = (name || '').trim().toLowerCase();
                if (n === 'black' || n === 'أسود' || n === 'اسود') return '#0b1220';
                if (n === 'white' || n === 'أبيض' || n === 'ابيض') return '#ffffff';
                if (n === 'red' || n === 'أحمر' || n === 'احمر') return '#ef4444';
                if (n === 'green' || n === 'أخضر' || n === 'اخضر') return '#22c55e';
                if (n === 'blue' || n === 'أزرق' || n === 'ازرق') return '#3b82f6';
                if (n === 'gray' || n === 'grey' || n === 'رمادي') return '#94a3b8';
                return 'linear-gradient(135deg, #e2e8f0, #cbd5e1)';
            }

            if (colorsEl) {
                colorsEl.addEventListener('click', (e) => {
                    const btn = e.target.closest('.pd-color');
                    if (!btn) return;
                    pd.color = btn.dataset.color;
                    renderColors();
                    renderSizes();
                });
            }

            window.pdInc = () => {
                const el = document.getElementById('pd-qty');
                if (!el) return;
                el.value = (parseInt(el.value || '1', 10) + 1);
            };
            window.pdDec = () => {
                const el = document.getElementById('pd-qty');
                if (!el) return;
                el.value = Math.max(1, parseInt(el.value || '1', 10) - 1);
            };

            window.pdAddToCart = async function () {
                const qty = Math.max(1, parseInt(document.getElementById('pd-qty')?.value || '1', 10));
                if (!pd.variantId) return;

                addBtn.disabled = true;

                const res = await fetch(@json(route('store.cart.add')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({ product_id: {{ $product->id }}, variant_id: pd.variantId, qty }),
                });

                if (!res.ok) {
                    addBtn.disabled = false;
                    const hint = document.getElementById('pd-hint');
                    if (hint) hint.textContent = 'حصل خطأ. حاول تاني.';
                    return;
                }

                const data = await res.json();
                const total = data?.cart?.total ?? 0;
                const count = data?.cart?.count ?? 0;
                const cartTotal = document.getElementById('cart-total');
                const cartCount = document.getElementById('cart-count');
                if (cartTotal) cartTotal.textContent = Number(total).toFixed(2) + ' EGP';
                if (cartCount) cartCount.textContent = count;

                addBtn.disabled = false;
                const hint = document.getElementById('pd-hint');
                if (hint) hint.textContent = 'تمت الإضافة للسلة.';
            };

            renderColors();
            update();
        })();
    </script>
@endsection
