<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>دخول الأدمن - {{ config('app.name', 'Maray Store') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 page-enter">
<div class="mx-auto flex min-h-screen max-w-md items-center px-4">
    <div class="w-full rounded-2xl border bg-white p-6 shadow-sm">
        <h1 class="mb-1 text-xl font-semibold">دخول الأدمن</h1>
        <p class="mb-6 text-sm text-slate-600">الدخول للوحة التحكم (للأدمن فقط).</p>

        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-4">
            @csrf

            <div>
                <label class="mb-1 block text-sm font-medium">الإيميل</label>
                <input name="email" value="{{ old('email') }}" type="email" required class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium">كلمة المرور</label>
                <input name="password" type="password" required class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900">
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="remember" value="1" class="rounded border">
                تذكرني
            </label>

            <button type="submit" class="w-full rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                دخول
            </button>
        </form>
    </div>
</div>
</body>
</html>
