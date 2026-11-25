<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/sidebar.tsx'])
    @include('partials.fonts')
</head>
<body class="relative min-h-full overflow-x-hidden bg-gradient-to-br from-sky-100 via-slate-100 to-indigo-100 font-sans text-slate-800 antialiased">
    <div aria-hidden="true" class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -left-40 top-8 h-72 w-72 rounded-full bg-sky-400/40 blur-3xl"></div>
        <div class="absolute -right-32 bottom-8 h-80 w-80 rounded-full bg-indigo-400/40 blur-3xl"></div>
    </div>

    <div id="sidebar-overlay" class="fixed inset-0 z-40 hidden bg-slate-900/40 backdrop-blur-sm lg:hidden"></div>

    @php
        $u = auth()->user();
        $user = $u;

        $menu = [];

        $menu[] = ['label' => 'Menu Utama', 'items' => [
            ['id' => 'dashboard', 'label' => 'Dashboard', 'href' => route('dashboard'), 'icon' => 'dashboard', 'active' => request()->routeIs('dashboard'), 'fx' => 'glow'],
        ]];

        if ($u->isAdmin() || $u->isPetugas()) {
            $menu[] = ['label' => 'Operasional', 'items' => [
                ['id' => 'transaksi', 'label' => 'Transaksi Parkir', 'href' => route('transaksi.index'), 'icon' => 'switch', 'active' => request()->routeIs('transaksi.*'), 'fx' => 'plane'],
            ]];
        }

        if ($u->isAdmin()) {
            $menu[] = ['label' => 'Manajemen', 'items' => [
                ['id' => 'users', 'label' => 'Pengguna', 'href' => route('admin.users.index'), 'icon' => 'users', 'active' => request()->routeIs('admin.users.*'), 'fx' => 'wave'],
                ['id' => 'tarif', 'label' => 'Tarif Parkir', 'href' => route('admin.tarif.index'), 'icon' => 'cash', 'active' => request()->routeIs('admin.tarif.*'), 'fx' => 'drop'],
                ['id' => 'area', 'label' => 'Area Parkir', 'href' => route('admin.area.index'), 'icon' => 'pin', 'active' => request()->routeIs('admin.area.*'), 'fx' => 'spin'],
                ['id' => 'kendaraan', 'label' => 'Kendaraan', 'href' => route('admin.kendaraan.index'), 'icon' => 'car', 'active' => request()->routeIs('admin.kendaraan.*'), 'fx' => 'pop'],
                ['id' => 'log', 'label' => 'Log Aktivitas', 'href' => route('admin.log.index'), 'icon' => 'log', 'active' => request()->routeIs('admin.log.*'), 'fx' => 'wave'],
            ]];
        }

        if ($u->isAdmin() || $u->isOwner()) {
            $menu[] = ['label' => 'Laporan', 'items' => [
                ['id' => 'laporan', 'label' => 'Rekap Pendapatan', 'href' => route('laporan.index'), 'icon' => 'chart', 'active' => request()->routeIs('laporan.*'), 'fx' => 'glow'],
            ]];
        }

        $activeId = null;

        foreach ($menu as $group) {
            foreach ($group['items'] as $item) {
                if ($item['active']) {
                    $activeId = $item['id'];
                }
            }
        }

        $sidebarConfig = [
            'brand' => 'Parkir Kantor',
            'brandDetail' => 'Sistem Informasi',
            'activeId' => $activeId,
            'groups' => array_map(fn ($group) => [
                'label' => $group['label'],
                'items' => array_map(fn ($item) => [
                    'id' => $item['id'],
                    'label' => $item['label'],
                    'href' => $item['href'],
                    'icon' => $item['icon'],
                    'fx' => $item['fx'] ?? null,
                ], $group['items']),
            ], $menu),
        ];
    @endphp

    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 mx-3 flex w-[312px] -translate-x-full flex-col gap-3 overflow-y-auto rounded-3xl border border-white/30 bg-white/40 p-3 shadow-2xl backdrop-blur-lg transition-all duration-500 ease-[cubic-bezier(0.4,0,0.2,1)] lg:translate-x-0 lg:mx-6 print:hidden">
        <div id="sidebar-root" data-config="{{ json_encode($sidebarConfig) }}"></div>

        <div class="mt-auto shrink-0 rounded-2xl border border-white/50 bg-white/45 p-4 shadow-sm backdrop-blur-md">
            <div class="flex items-center gap-3 rounded-xl bg-slate-50 px-3 py-2.5">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-sky-100 text-sm font-semibold text-sky-700">
                    {{ strtoupper(substr($u->nama_lengkap, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-slate-800">{{ $u->nama_lengkap }}</p>
                    <p class="text-xs capitalize text-slate-500">{{ $u->role }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="mt-3">
                @csrf
                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-xs font-medium text-slate-600 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600">
                    <x-icon name="logout" class="h-4 w-4" />
                    Keluar
                </button>
            </form>
            <p class="mt-3 text-center text-[11px] text-slate-400">&copy; {{ date('Y') }} Parkir Kantor</p>
        </div>
    </aside>

    <div class="lg:pl-[352px]">
        <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/85 backdrop-blur print:hidden">
            <div class="flex h-16 items-center gap-3 px-4 sm:px-6 lg:px-8">
                <button id="sidebar-toggle" type="button" class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700 lg:hidden" aria-label="Buka menu">
                    <x-icon name="menu" class="h-5 w-5" />
                </button>

                <h2 id="app-title" class="min-w-0 flex-1 truncate text-base font-semibold text-slate-900">@yield('title', 'Dashboard')</h2>

                <span class="hidden items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-500 sm:inline-flex">
                    <x-icon name="calendar" class="h-4 w-4 text-slate-400" />
                    {{ now()->locale('id')->translatedFormat('d M Y') }}
                </span>

                <div class="flex items-center gap-2">
                    <div class="hidden text-right sm:block">
                        <p class="text-sm font-semibold leading-tight text-slate-800">{{ $user->nama_lengkap }}</p>
                        <p class="text-xs capitalize leading-tight text-slate-500">{{ $user->role }}</p>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-sky-600 text-sm font-semibold text-white">
                        {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="rounded-lg p-2 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600" title="Logout">
                            <x-icon name="logout" class="h-5 w-5" />
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="page-enter px-4 py-6 sm:px-6 lg:px-8">
            <x-flash />
            @yield('content')
        </main>
    </div>
</body>
</html>