@extends('layouts.app')

@section('title', 'Rekap Pendapatan')

@section('content')
    @php
        $cetakUrl = route('laporan.cetak', [
            'start_date' => $mulai->toDateString(),
            'end_date' => $selesai->toDateString(),
            'basis' => $basis,
        ]);
    @endphp

    <x-page-header title="Rekap Pendapatan" description="Laporan transaksi parkir berdasarkan rentang tanggal.">
        <x-slot:actions>
            <a href="{{ $cetakUrl }}" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700">
                <x-icon name="printer" class="h-4 w-4" />
                Cetak Laporan
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <form method="GET" action="{{ route('laporan.index') }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label for="start_date" class="mb-1.5 block text-sm font-medium text-slate-700">Tanggal Mulai</label>
                <input
                    type="date"
                    name="start_date"
                    id="start_date"
                    value="{{ $mulai->toDateString() }}"
                    class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10"
                    required
                >
            </div>

            <div>
                <label for="end_date" class="mb-1.5 block text-sm font-medium text-slate-700">Tanggal Akhir</label>
                <input
                    type="date"
                    name="end_date"
                    id="end_date"
                    value="{{ $selesai->toDateString() }}"
                    class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10"
                    required
                >
            </div>

            <div>
                <label for="basis" class="mb-1.5 block text-sm font-medium text-slate-700">Berdasarkan Tanggal</label>
                <select
                    name="basis"
                    id="basis"
                    class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10"
                >
                    <option value="masuk" @selected($basis === 'masuk')>Tanggal Masuk</option>
                    <option value="keluar" @selected($basis === 'keluar')>Tanggal Keluar</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                    <x-icon name="search" class="h-4 w-4" />
                    Tampilkan
                </button>
                <a href="{{ route('laporan.index') }}" class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50">Reset</a>
            </div>
        </form>
    </div>

    <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Total Transaksi" :value="$ringkasan['total']" :hint="$mulai->format('d/m/Y').' — '.$selesai->format('d/m/Y')" />
        <x-stat-card label="Selesai" :value="$ringkasan['selesai']" hint="Kendaraan sudah keluar" />
        <x-stat-card label="Masih Parkir" :value="$ringkasan['aktif']" hint="Belum ada biaya final" />
        <x-stat-card label="Total Pendapatan" :value="'Rp '.number_format($ringkasan['pendapatan'], 0, ',', '.')" hint="Hanya transaksi selesai" />
    </div>

    <div class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-2 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-sm font-semibold text-slate-900">Detail Transaksi</h2>
                <p class="text-xs text-slate-500">
                    Periode {{ $mulai->format('d/m/Y') }} — {{ $selesai->format('d/m/Y') }},
                    berdasarkan {{ $basis === 'keluar' ? 'tanggal keluar' : 'tanggal masuk' }}.
                </p>
            </div>
            <span class="text-xs text-slate-500">{{ $transaksi->total() }} baris</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">No.</th>
                        <th class="px-4 py-3">Plat Nomor</th>
                        <th class="px-4 py-3">Area</th>
                        <th class="px-4 py-3">Masuk</th>
                        <th class="px-4 py-3">Keluar</th>
                        <th class="px-4 py-3">Durasi</th>
                        <th class="px-4 py-3">Tarif / Jam</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($transaksi as $trx)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">#{{ str_pad((string) $trx->id_parkir, 5, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-4 py-3">
                                <span class="font-semibold text-slate-800">{{ $trx->kendaraan->plat_nomor ?? '-' }}</span>
                                <span class="block text-xs capitalize text-slate-400">{{ $trx->kendaraan->jenis_kendaraan ?? '' }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $trx->area->nama_area ?? '-' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ $trx->waktu_masuk->format('d/m/Y H:i') }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ $trx->waktu_keluar?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $trx->isMasuk() ? '-' : $trx->durasi_jam.' jam' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-600">Rp {{ number_format((float) ($trx->tarif->tarif_per_jam ?? 0), 0, ',', '.') }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right font-semibold text-slate-800">Rp {{ number_format((float) $trx->biaya_total, 0, ',', '.') }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$trx->status" /></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-10 text-center text-sm text-slate-500">Tidak ada transaksi pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($transaksi->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">
                {{ $transaksi->links() }}
            </div>
        @endif
    </div>

    <p class="mt-3 text-xs text-slate-400">
        Total pendapatan hanya menjumlahkan transaksi berstatus keluar. Transaksi yang masih parkir belum memiliki biaya final.
    </p>
@endsection
