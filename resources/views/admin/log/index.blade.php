@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('content')
    <x-page-header title="Log Aktivitas" description="Riwayat aktivitas penting pengguna sistem, urut dari yang terbaru." />

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-end sm:justify-between">
            <form method="GET" action="{{ route('admin.log.index') }}" class="flex w-full flex-col gap-2 sm:flex-row sm:items-end">
                <div class="w-full sm:max-w-xs">
                    <label for="q" class="mb-1 block text-xs font-medium text-slate-500">Cari aktivitas / user</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <x-icon name="search" class="h-4 w-4" />
                        </span>
                        <input
                            type="search"
                            name="q"
                            id="q"
                            value="{{ $search }}"
                            placeholder="Contoh: login, transaksi..."
                            class="block w-full rounded-xl border border-slate-300 bg-white py-2 pl-9 pr-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10"
                        >
                    </div>
                </div>

                <div class="w-full sm:w-44">
                    <label for="tanggal" class="mb-1 block text-xs font-medium text-slate-500">Tanggal</label>
                    <input
                        type="date"
                        name="tanggal"
                        id="tanggal"
                        value="{{ $tanggal }}"
                        class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10"
                    >
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                        <x-icon name="search" class="h-4 w-4" />
                        Filter
                    </button>
                    @if ($search !== '' || $tanggal !== '')
                        <a href="{{ route('admin.log.index') }}" class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50">Reset</a>
                    @endif
                </div>
            </form>

            <p class="text-xs text-slate-500">{{ $log->total() }} aktivitas</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3">Pengguna</th>
                        <th class="px-4 py-3">Aktivitas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($log as $item)
                        <tr class="hover:bg-slate-50/60">
                            <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ $item->waktu_aktivitas?->format('d/m/Y H:i:s') ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @if ($item->user)
                                    <span class="font-medium text-slate-800">{{ $item->user->nama_lengkap }}</span>
                                    <span class="block text-xs capitalize text-slate-400">{{ $item->user->username }} &middot; {{ $item->user->role }}</span>
                                @else
                                    <span class="text-slate-500">Sistem</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $item->aktivitas }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada log aktivitas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($log->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">
                {{ $log->links() }}
            </div>
        @endif
    </div>
@endsection
