@extends('layouts.app')

@section('title', 'Area Parkir')

@section('content')
    <x-page-header title="Area Parkir" description="Kelola lokasi parkir dan kapasitasnya.">
        <x-slot:actions>
            <a href="{{ route('admin.area.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700">
                <x-icon name="plus" class="h-4 w-4" />
                Tambah Area
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" action="{{ route('admin.area.index') }}" class="w-full sm:max-w-xs">
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <x-icon name="search" class="h-4 w-4" />
                    </span>
                    <input
                        type="search"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Cari nama area..."
                        class="block w-full rounded-xl border border-slate-300 bg-white py-2 pl-9 pr-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10"
                    >
                </div>
            </form>
            <p class="text-xs text-slate-500">{{ $area->total() }} area</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Nama Area</th>
                        <th class="px-4 py-3">Okupansi</th>
                        <th class="px-4 py-3">Terisi / Kapasitas</th>
                        <th class="px-4 py-3">Sisa</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($area as $item)
                        @php($persen = $item->kapasitas > 0 ? min(100, (int) round($item->terisi / $item->kapasitas * 100)) : 0)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $item->nama_area }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-2 w-28 overflow-hidden rounded-full bg-slate-100">
                                        <div class="h-2 rounded-full {{ $persen >= 90 ? 'bg-rose-500' : ($persen >= 70 ? 'bg-amber-500' : 'bg-sky-500') }}" style="width: {{ $persen }}%"></div>
                                    </div>
                                    <span class="text-xs text-slate-500">{{ $persen }}%</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $item->terisi }} / {{ $item->kapasitas }}</td>
                            <td class="px-4 py-3">
                                @if ($item->isPenuh())
                                    <span class="inline-flex rounded-full bg-rose-50 px-2 py-0.5 text-xs font-medium text-rose-700 ring-1 ring-inset ring-rose-200">Penuh</span>
                                @else
                                    <span class="inline-flex rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200">{{ $item->sisaKapasitas() }} slot</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.area.edit', $item) }}" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-sky-50 hover:text-sky-600" title="Edit">
                                        <x-icon name="pencil" class="h-4 w-4" />
                                    </a>
                                    <form action="{{ route('admin.area.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus area ini?')">
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
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada area yang cocok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($area->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">
                {{ $area->links() }}
            </div>
        @endif
    </div>
@endsection
