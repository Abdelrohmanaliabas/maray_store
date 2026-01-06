<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'المتجر') - {{ config('app.name', 'Maray Store') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-b from-slate-50 via-white to-white text-slate-900">
<header class="sticky top-0 z-40 border-b bg-white/80 backdrop-blur">
    <div class="mx-auto max-w-6xl px-4">
        <div class="relative flex items-center justify-between py-3">
            <div class="flex items-center gap-2">
                <a href="{{ route('store.cart') }}" class="relative inline-flex h-10 w-10 items-center justify-center rounded-full border bg-white hover:bg-slate-50" title="السلة">
                    <svg class="h-5 w-5 text-slate-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 6h15l-1.5 9h-13z"></path>
                        <path d="M6 6l-2-3H1"></path>
                        <circle cx="9" cy="20" r="1.5"></circle>
                        <circle cx="18" cy="20" r="1.5"></circle>
                    </svg>
                    <span id="cart-count" class="absolute -left-1 -top-1 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-semibold text-white">
                        {{ $cartSummary['count'] ?? 0 }}
                    </span>
                </a>
                <div id="cart-total" class="hidden rounded-full bg-slate-900 px-3 py-2 text-xs font-semibold text-white md:block">
                    {{ number_format($cartSummary['total'] ?? 0, 2) }} EGP
                </div>
                <button type="button" onclick="window.toggleSearch()" class="inline-flex h-10 w-10 items-center justify-center rounded-full border bg-white hover:bg-slate-50" title="بحث">
                    <svg class="h-5 w-5 text-slate-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="M21 21l-4.3-4.3"></path>
                    </svg>
                </button>
            </div>

            <a href="{{ route('store.home') }}" class="absolute left-1/2 -translate-x-1/2">
                <img src="{{ asset('image/logo.jpg') }}" class="h-11 w-auto" alt="MARAY">
            </a>

            <div class="w-[96px] md:w-[160px]"></div>
        </div>

        <nav class="pb-3">
            <div class="flex gap-2 overflow-auto whitespace-nowrap">
                @foreach($navCategories as $cat)
                    <a
                        href="{{ route('store.category', $cat) }}"
                        class="rounded-full border px-4 py-2 text-sm font-medium transition hover:bg-slate-50 {{ request()->routeIs('store.category') && request()->route('category')?->id === $cat->id ? 'border-slate-900 text-slate-900' : 'border-slate-200 text-slate-700' }}"
                    >
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </nav>
    </div>
</header>

<div id="search-drawer" class="fixed inset-0 z-50 hidden items-start justify-center bg-black/50 p-4">
    <div class="w-full max-w-xl rounded-2xl bg-white p-4 shadow-lg">
        <div class="flex items-center justify-between">
            <div class="text-sm font-semibold">بحث</div>
            <button type="button" class="rounded-lg border px-3 py-1 text-sm hover:bg-slate-50" onclick="window.toggleSearch()">×</button>
        </div>
        <form method="GET" action="{{ route('store.home') }}" class="mt-3 flex gap-2">
            <input id="search-input" name="q" value="{{ request('q') }}" placeholder="ابحث عن منتج..." class="w-full rounded-xl border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900">
            <button class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-medium text-white hover:bg-slate-800">بحث</button>
        </form>
    </div>
</div>

@if (session('status'))
    <div class="mx-auto max-w-6xl px-4 pt-4">
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
    </div>
@endif

@if ($errors->any())
    <div class="mx-auto max-w-6xl px-4 pt-4">
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
    </div>
@endif

<main class="mx-auto max-w-6xl px-4 py-6 page-enter">
    @yield('content')
</main>

@if (($cartSummary['count'] ?? 0) > 0)
    <div class="fixed bottom-4 left-4 right-4 z-30 md:hidden">
        <a href="{{ route('store.checkout') }}" class="flex items-center justify-between gap-3 rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-lg">
            <span>أكمل الطلب الآن</span>
            <span>{{ number_format($cartSummary['total'] ?? 0, 2) }} EGP</span>
        </a>
    </div>
@endif

<footer class="mt-10 border-t bg-white">
    <div class="mx-auto max-w-6xl px-4 py-10">
        <div class="grid gap-6 md:grid-cols-3">
            <div>
                <div class="flex items-center gap-3">
                    <img src="{{ asset('image/logo.jpg') }}" class="w-auto h-20" alt="MARAY">
                </div>
            </div>

            <div class="md:text-center">
                <div class="text-sm font-semibold">تابعنا</div>
                <div class="mt-3 flex justify-start gap-3 md:justify-center">
                    <a class="inline-flex h-10 w-10 items-center justify-center rounded-full border hover:bg-slate-50" href="{{ config('app.instagram_url', '#') }}" target="_blank" rel="noopener" aria-label="Instagram">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="5"></rect>
                            <circle cx="12" cy="12" r="4"></circle>
                            <circle cx="17.5" cy="6.5" r="1"></circle>
                        </svg>
                    </a>
                    {{-- <a class="inline-flex h-10 w-10 items-center justify-center rounded-full border hover:bg-slate-50" href="{{ config('app.facebook_url', '#') }}" target="_blank" rel="noopener" aria-label="Facebook">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M13.5 22v-8h2.8l.5-3H13.5V9c0-.9.2-1.5 1.6-1.5h2V4.7c-.3 0-1.5-.1-2.8-.1-2.8 0-4.7 1.7-4.7 4.9V11H7v3h2.6v8h3.9z"/>
                        </svg>
                    </a>
                    <a class="inline-flex h-10 w-10 items-center justify-center rounded-full border hover:bg-slate-50" href="{{ config('app.tiktok_url', '#') }}" target="_blank" rel="noopener" aria-label="TikTok">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M16 3c.6 3.4 2.8 5.6 6 6v4c-2.2.1-4.1-.6-6-1.8V16c0 3.5-2.8 6.4-6.3 6.4-3.5 0-6.3-2.9-6.3-6.4 0-3.8 3.4-6.8 7.2-6.3v3.5c-1.8-.6-3.7.7-3.7 2.8 0 1.7 1.3 2.9 2.8 2.9 1.6 0 2.6-1.1 2.6-3.1V3h3.7z"/>
                        </svg>
                    </a> --}}
                </div>
            </div>

            <div class="md:text-left">
                <div class="text-sm font-semibold">روابط</div>
                <div class="mt-3 flex flex-col gap-2 text-sm text-slate-600">
                    <a class="hover:text-slate-900" href="{{ route('store.home') }}">المنتجات</a>
                    <a class="hover:text-slate-900" href="{{ route('store.cart') }}">السلة</a>
                </div>
            </div>
        </div>

        <div class="mt-8 flex flex-wrap items-center justify-between gap-2 border-t pt-6 text-xs text-slate-500">
            <div>© {{ date('Y') }} {{ config('app.name', 'Maray Store') }}</div>
            <a class="hover:text-slate-900" href="{{ config('app.dev_portfolio_url') }}" target="_blank" rel="noopener">Created {{ date('Y') }} — abas</a>
        </div>
    </div>
</footer>

<div id="variant-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="modal-panel w-full max-w-xl rounded-2xl bg-white p-5 shadow-lg">
        <div class="flex items-center justify-between">
            <div class="text-base font-semibold">اختر المتغيرات</div>
            <button type="button" class="rounded-lg border px-3 py-1 text-sm hover:bg-slate-50" onclick="window.closeVariantModal()">×</button>
        </div>

        <div class="mt-4 flex gap-4">
            <img id="vm-image" class="h-24 w-24 rounded-xl object-cover" alt="">
            <div class="flex-1">
                <div id="vm-name" class="font-semibold"></div>
                <div id="vm-price" class="mt-1 text-sm text-slate-700"></div>
            </div>
        </div>

        <div class="mt-5 grid gap-4 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-medium">اختر اللون</label>
                <div id="vm-colors" class="flex flex-wrap gap-2"></div>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium">المقاس</label>
                <div id="vm-sizes" class="flex flex-wrap gap-2"></div>
            </div>
        </div>

        <div class="mt-5 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <button type="button" class="rounded-lg border px-3 py-2 text-sm hover:bg-slate-50" onclick="window.vmDec()">-</button>
                <input id="vm-qty" type="number" min="1" value="1" class="w-20 rounded-lg border px-3 py-2 text-sm text-center">
                <button type="button" class="rounded-lg border px-3 py-2 text-sm hover:bg-slate-50" onclick="window.vmInc()">+</button>
            </div>
            <button id="vm-add" type="button" class="flex-1 rounded-full bg-slate-900 px-4 py-3 text-sm font-medium text-white opacity-50" disabled onclick="window.vmAddToCart()">
                إضافة إلى السلة
            </button>
        </div>
        <div id="vm-hint" class="mt-2 text-xs text-slate-600">اختار اللون والمقاس.</div>
    </div>
</div>

<script>
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const modal = document.getElementById('variant-modal');
    let vm = { productId: null, variantsByColor: {}, color: null, size: null, variantId: null };

    window.toggleSearch = function () {
        const el = document.getElementById('search-drawer');
        el.classList.toggle('hidden');
        el.classList.toggle('flex');
        if (!el.classList.contains('hidden')) {
            setTimeout(() => document.getElementById('search-input')?.focus(), 50);
        }
    };

    window.openVariantModalFromButton = function (btn) {
        const d = btn?.dataset || {};
        window.openVariantModal({
            id: parseInt(d.productId || '0', 10),
            name: d.productName || '',
            price: d.productPrice || '',
            image: d.productImage || '',
            variantsUrl: d.variantsUrl || '',
        });
    };

    window.openVariantModal = async function (product) {
        vm = { productId: product.id, variantsByColor: {}, color: null, size: null, variantId: null };

        document.getElementById('vm-image').src = product.image || '';
        document.getElementById('vm-name').textContent = product.name || '';
        document.getElementById('vm-price').textContent = (product.price || '') + ' EGP';
        document.getElementById('vm-qty').value = 1;
        document.getElementById('vm-hint').textContent = 'تحميل الخيارات...';

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        const res = await fetch(product.variantsUrl, { headers: { 'Accept': 'application/json' }});
        let data = {};
        try {
            data = await res.json();
        } catch (e) {
            data = {};
        }
        vm.variantsByColor = data.colors || {};

        renderColors();
        renderSizes();
        updateAddButton();
        if (Object.keys(vm.variantsByColor).length === 0) {
            document.getElementById('vm-hint').textContent = 'لا توجد اختيارات متاحة حالياً.';
        } else {
            document.getElementById('vm-hint').textContent = 'اختار اللون والمقاس.';
        }
    };

    window.closeVariantModal = function () {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };

    function renderColors() {
        const el = document.getElementById('vm-colors');
        el.innerHTML = '';
        const colors = Object.keys(vm.variantsByColor);
        colors.forEach(color => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'relative h-10 w-10 rounded-lg border transition hover:bg-slate-50';
            if (vm.color === color) btn.classList.add('border-slate-900', 'ring-2', 'ring-slate-900/20');
            else btn.classList.add('border-slate-200');
            const swatch = document.createElement('span');
            swatch.className = 'absolute inset-1 rounded-md';
            swatch.style.background = colorToBg(color);
            btn.appendChild(swatch);
            btn.title = color;
            btn.onclick = () => { vm.color = color; vm.size = null; vm.variantId = null; renderColors(); renderSizes(); updateAddButton(); };
            el.appendChild(btn);
        });
    }

    function renderSizes() {
        const el = document.getElementById('vm-sizes');
        el.innerHTML = '';
        if (!vm.color) return;
        const sizes = vm.variantsByColor[vm.color] || [];
        sizes.forEach(v => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'h-10 min-w-10 rounded-lg border px-3 text-sm font-semibold transition hover:bg-slate-50';
            if (vm.size === v.size) btn.classList.add('border-slate-900', 'bg-slate-900', 'text-white');
            else btn.classList.add('border-slate-200', 'bg-white', 'text-slate-900');
            btn.textContent = v.size;
            btn.onclick = () => { vm.size = v.size; vm.variantId = v.id; renderSizes(); updateAddButton(); };
            el.appendChild(btn);
        });
    }

    function colorToBg(name) {
        const n = (name || '').trim().toLowerCase();
        if (n === 'black' || n === 'أسود' || n === 'اسود') return '#0b1220';
        if (n === 'white' || n === 'أبيض' || n === 'ابيض') return '#ffffff';
        if (n === 'red' || n === 'أحمر' || n === 'احمر') return '#ef4444';
        if (n === 'green' || n === 'أخضر' || n === 'اخضر') return '#22c55e';
        if (n === 'blue' || n === 'أزرق' || n === 'ازرق') return '#3b82f6';
        return 'linear-gradient(135deg, #e2e8f0, #cbd5e1)';
    }

    function updateAddButton() {
        const btn = document.getElementById('vm-add');
        const hasAny = Object.keys(vm.variantsByColor || {}).length > 0;
        const ok = !!vm.variantId && hasAny;
        btn.disabled = !ok;
        btn.classList.toggle('opacity-50', !ok);
        if (!hasAny) {
            document.getElementById('vm-hint').textContent = 'لا توجد اختيارات متاحة حالياً.';
        } else if (ok) {
            document.getElementById('vm-hint').textContent = 'جاهز للإضافة.';
        } else {
            document.getElementById('vm-hint').textContent = 'اختار اللون والمقاس.';
        }
    }

    window.vmInc = () => document.getElementById('vm-qty').value = (parseInt(document.getElementById('vm-qty').value || '1', 10) + 1);
    window.vmDec = () => document.getElementById('vm-qty').value = Math.max(1, parseInt(document.getElementById('vm-qty').value || '1', 10) - 1);

    window.vmAddToCart = async function () {
        const qty = Math.max(1, parseInt(document.getElementById('vm-qty').value || '1', 10));
        const btn = document.getElementById('vm-add');
        btn.disabled = true;

        const res = await fetch(@json(route('store.cart.add')), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify({ product_id: vm.productId, variant_id: vm.variantId, qty }),
        });

        if (!res.ok) {
            btn.disabled = false;
            document.getElementById('vm-hint').textContent = 'حدث خطأ. حاول تاني.';
            return;
        }

        const data = await res.json();
        const total = data?.cart?.total ?? 0;
        const count = data?.cart?.count ?? 0;
        document.getElementById('cart-total').textContent = Number(total).toFixed(2) + ' EGP';
        document.getElementById('cart-count').textContent = count;

        window.closeVariantModal();
    };
</script>
</body>
</html>
