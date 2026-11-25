<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Login') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/login.tsx'])
    @include('partials.fonts')
</head>
<body class="min-h-full font-sans text-slate-800 antialiased">
    <div
        id="login-root"
        data-config="{{ json_encode([
            'action' => route('login'),
            'csrfToken' => csrf_token(),
            'errors' => $errors->toArray(),
            'oldUsername' => old('username'),
            'title' => 'Selamat Datang',
            'subtitle' => 'Masuk menggunakan kredensial akun Anda.',
            'brand' => config('app.name'),
        ]) }}"
    ></div>
</body>
</html>
