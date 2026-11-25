<!DOCTYPE html>
<html lang="id" class="bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Cetak') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
    @include('partials.fonts')
</head>
<body class="min-h-screen bg-slate-100 py-8 font-sans text-slate-800 antialiased print:bg-white print:py-0">
    <div class="mx-auto max-w-5xl px-4 print:max-w-none print:px-0">
        @yield('content')
    </div>
</body>
</html>
