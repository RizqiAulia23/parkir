<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 — Akses Ditolak</title>
    @vite(['resources/css/app.css'])
    @include('partials.fonts')
</head>
<body class="flex min-h-screen items-center justify-center px-4 font-sans text-slate-800 antialiased">
    <div class="w-full max-w-md text-center">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-100 text-amber-600">
            <x-icon name="shield" class="h-7 w-7" />
        </div>
        <p class="mt-6 text-sm font-semibold uppercase tracking-widest text-slate-400">Error 403</p>
        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">Akses ditolak</h1>
        <p class="mt-2 text-sm text-slate-500">
            {{ $exception->getMessage() ?: 'Role akun Anda tidak memiliki izin untuk membuka halaman ini.' }}
        </p>
        <div class="mt-6 flex items-center justify-center gap-2">
            <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700">
                <x-icon name="arrow-right" class="h-4 w-4 rotate-180" />
                {{ auth()->check() ? 'Kembali ke Dashboard' : 'Ke Halaman Login' }}
            </a>
        </div>
    </div>
</body>
</html>
