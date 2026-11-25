@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
    <x-page-header title="Manajemen Pengguna" description="Kelola akun admin, petugas, dan owner.">
        <x-slot:actions>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700">
                <x-icon name="plus" class="h-4 w-4" />
                Tambah User
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" action="{{ route('admin.users.index') }}" class="w-full sm:max-w-xs">
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <x-icon name="search" class="h-4 w-4" />
                    </span>
                    <input
                        type="search"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Cari nama atau username..."
                        class="block w-full rounded-xl border border-slate-300 bg-white py-2 pl-9 pr-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10"
                    >
                </div>
            </form>
            <p class="text-xs text-slate-500">{{ $users->total() }} user terdaftar</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Username</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $item)
                        @php
                            $roleClass = match ($item->role) {
                                'admin' => 'bg-sky-50 text-sky-700 ring-sky-200',
                                'petugas' => 'bg-amber-50 text-amber-700 ring-amber-200',
                                default => 'bg-violet-50 text-violet-700 ring-violet-200',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $item->nama_lengkap }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $item->username }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium capitalize ring-1 ring-inset {{ $roleClass }}">
                                    {{ $item->role }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if ($item->isAktif())
                                    <span class="inline-flex rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200">Aktif</span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-200">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.users.edit', $item) }}" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-sky-50 hover:text-sky-600" title="Edit">
                                        <x-icon name="pencil" class="h-4 w-4" />
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus user ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600" title="Hapus">
                                            <x-icon name="trash" class="h-4 w-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada user yang cocok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection
