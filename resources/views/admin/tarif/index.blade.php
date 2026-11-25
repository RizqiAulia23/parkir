@extends('layouts.app')

@section('title', 'Tarif Parkir')

@section('content')
    <x-page-header title="Tarif Parkir" description="Atur tarif per jam untuk setiap jenis kendaraan.">
        <x-slot:actions>
            <a href="{{ route('admin.tarif.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700">
                <x-icon name="plus" class="h-4 w-4" />
                Tambah Tarif
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" action="{{ route('admin.tarif.index') }}" class="w-full sm:max-w-xs">
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <x-icon name="search" class="h-4 w-4" />
                    </span>
                    <input
                        type="search"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Cari jenis kendaraan..."
                        class="block w-full rounded-xl border border-slate-300 bg-white py-2 pl-9 pr-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10"
                    >
                </div>
            </form>
            <p class="text-xs text-slate-500">{{ $tarif->total() }} tarif</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Jenis Kendaraan</th>
                        <th class="px-4 py-3">Tarif per Jam</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($tarif as $item)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-2 font-medium capitalize text-slate-800">
                                    <x-icon name="car" class="h-4 w-4 text-slate-400" />
                                    {{ $item->jenis_kendaraan }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-700">Rp {{ number_format((float) $item->tarif_per_jam, 0, ',', '.') }} / jam</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.tarif.edit', $item) }}" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-sky-50 hover:text-sky-600" title="Edit">
                                        <x-icon name="pencil" class="h-4 w-4" />
                                    </a>
                                    <form action="{{ route('admin.tarif.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus tarif ini?')">
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
                            <td colspan="3" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada tarif yang cocok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($tarif->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">
                {{ $tarif->links() }}
            </div>
        @endif
    </div>
@endsection
