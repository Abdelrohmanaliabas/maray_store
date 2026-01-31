<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'لوحة التحكم') - {{ config('app.name', 'Maray Store') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
<header class="border-b bg-white">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="text-lg font-semibold">لوحة التحكم</a>
            <nav class="hidden gap-3 text-sm text-slate-600 md:flex">
                <a class="hover:text-slate-900" href="{{ route('admin.products.index') }}">المنتجات</a>
                <a class="hover:text-slate-900" href="{{ route('admin.orders.index') }}">الأوردرات</a>
                <a class="hover:text-slate-900" href="{{ route('admin.promo-codes.index') }}">بروموكود</a>
            </nav>
        </div>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button class="rounded-lg bg-slate-900 px-3 py-2 text-sm text-white hover:bg-slate-800" type="submit">تسجيل الخروج</button>
        </form>
    </div>
</header>

<main class="mx-auto max-w-6xl px-4 py-6 page-enter">
    @if (session('status'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('status') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>
</body>
</html>
