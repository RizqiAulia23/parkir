@extends('layouts.app')

@section('title', 'Kendaraan')

@section('content')
    <x-page-header title="Kendaraan" description="Pendataan kendaraan karyawan dan tamu kantor.">
        <x-slot:actions>
            <a href="{{ route('admin.kendaraan.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700">
                <x-icon name="plus" class="h-4 w-4" />
                Tambah Kendaraan
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" action="{{ route('admin.kendaraan.index') }}" class="w-full sm:max-w-xs">
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <x-icon name="search" class="h-4 w-4" />
                    </span>
                    <input
                        type="search"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Cari plat nomor atau pemilik..."
                        class="block w-full rounded-xl border border-slate-300 bg-white py-2 pl-9 pr-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10"
                    >
                </div>
            </form>
            <p class="text-xs text-slate-500">{{ $kendaraan->total() }} kendaraan terdaftar</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Plat Nomor</th>
                        <th class="px-4 py-3">Jenis</th>
                        <th class="px-4 py-3">Warna</th>
                        <th class="px-4 py-3">Pemilik</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($kendaraan as $item)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-4 py-3 font-semibold tracking-wide text-slate-800">{{ $item->plat_nomor }}</td>
                            <td class="px-4 py-3 capitalize text-slate-600">{{ $item->jenis_kendaraan }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $item->warna ?: '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">
                                {{ $item->pemilik ?: ($item->user->nama_lengkap ?? '-') }}
                                @if ($item->user)
                                    <span class="block text-xs text-slate-400">Akun: {{ $item->user->username }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if ($item->sedang_parkir)
                                    <span class="inline-flex rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-200">Sedang parkir</span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-200">Tidak parkir</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.kendaraan.edit', $item) }}" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-sky-50 hover:text-sky-600" title="Edit">
                                        <x-icon name="pencil" class="h-4 w-4" />
                                    </a>
                                    <form action="{{ route('admin.kendaraan.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus kendaraan ini?')">
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
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada kendaraan yang cocok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($kendaraan->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">
                {{ $kendaraan->links() }}
            </div>
        @endif
    </div>
@endsection
