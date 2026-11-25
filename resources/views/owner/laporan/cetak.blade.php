@extends('layouts.print')

@section('title', 'Cetak Laporan')

@section('content')
    @php
        $kembaliUrl = route('laporan.index', [
            'start_date' => $mulai->toDateString(),
            'end_date' => $selesai->toDateString(),
            'basis' => $basis,
        ]);
    @endphp

    <div class="mb-5 flex flex-wrap items-center justify-between gap-3 print:hidden">
        <a href="{{ $kembaliUrl }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
            <x-icon name="arrow-right" class="h-4 w-4 rotate-180 text-slate-400" />
            Kembali ke Laporan
        </a>

        <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700">
            <x-icon name="printer" class="h-4 w-4" />
            Cetak
        </button>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm print:rounded-none print:border-0 print:p-0 print:shadow-none">
        <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-sky-600 text-lg font-bold text-white print:bg-slate-900">
                    P
                </div>
                <div>
                    <p class="text-base font-bold text-slate-900">Parkir Kantor</p>
                    <p class="text-xs text-slate-500">Sistem Informasi Parkir Perkantoran</p>
                </div>
            </div>

            <div class="text-left sm:text-right">
                <h1 class="text-lg font-bold tracking-tight text-slate-900">Laporan Rekap Pendapatan</h1>
                <p class="mt-1 text-xs text-slate-500">
                    Periode {{ $mulai->format('d/m/Y') }} — {{ $selesai->format('d/m/Y') }}
                </p>
                <p class="text-xs text-slate-500">
                    Berdasarkan {{ $basis === 'keluar' ? 'tanggal keluar' : 'tanggal masuk' }}
                </p>
            </div>
        </div>

        <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="rounded-lg border border-slate-200 px-3 py-2.5">
                <p class="text-[11px] uppercase tracking-wide text-slate-400">Total Transaksi</p>
                <p class="mt-0.5 text-lg font-semibold text-slate-900">{{ $ringkasan['total'] }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 px-3 py-2.5">
                <p class="text-[11px] uppercase tracking-wide text-slate-400">Selesai</p>
                <p class="mt-0.5 text-lg font-semibold text-slate-900">{{ $ringkasan['selesai'] }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 px-3 py-2.5">
                <p class="text-[11px] uppercase tracking-wide text-slate-400">Masih Parkir</p>
                <p class="mt-0.5 text-lg font-semibold text-slate-900">{{ $ringkasan['aktif'] }}</p>
            </div>
            <div class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2.5">
                <p class="text-[11px] uppercase tracking-wide text-sky-600">Total Pendapatan</p>
                <p class="mt-0.5 text-lg font-semibold text-slate-900">Rp {{ number_format($ringkasan['pendapatan'], 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="mt-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-xs">
                <thead class="bg-slate-50 text-left font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-2.5 py-2">No.</th>
                        <th class="px-2.5 py-2">Plat</th>
                        <th class="px-2.5 py-2">Jenis</th>
                        <th class="px-2.5 py-2">Area</th>
                        <th class="px-2.5 py-2">Masuk</th>
                        <th class="px-2.5 py-2">Keluar</th>
                        <th class="px-2.5 py-2">Durasi</th>
                        <th class="px-2.5 py-2 text-right">Tarif/Jam</th>
                        <th class="px-2.5 py-2 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($transaksi as $trx)
                        <tr>
                            <td class="px-2.5 py-2 font-mono text-slate-500">#{{ str_pad((string) $trx->id_parkir, 5, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-2.5 py-2 font-semibold tracking-wide text-slate-800">{{ $trx->kendaraan->plat_nomor ?? '-' }}</td>
                            <td class="px-2.5 py-2 capitalize text-slate-600">{{ $trx->kendaraan->jenis_kendaraan ?? '-' }}</td>
                            <td class="px-2.5 py-2 text-slate-600">{{ $trx->area->nama_area ?? '-' }}</td>
                            <td class="whitespace-nowrap px-2.5 py-2 text-slate-600">{{ $trx->waktu_masuk->format('d/m/Y H:i') }}</td>
                            <td class="whitespace-nowrap px-2.5 py-2 text-slate-600">{{ $trx->waktu_keluar?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-2.5 py-2 text-slate-600">{{ $trx->isMasuk() ? '-' : $trx->durasi_jam.' jam' }}</td>
                            <td class="whitespace-nowrap px-2.5 py-2 text-right text-slate-600">Rp {{ number_format((float) ($trx->tarif->tarif_per_jam ?? 0), 0, ',', '.') }}</td>
                            <td class="whitespace-nowrap px-2.5 py-2 text-right font-semibold text-slate-800">Rp {{ number_format((float) $trx->biaya_total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-2.5 py-8 text-center text-slate-500">Tidak ada transaksi pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($transaksi->isNotEmpty())
                    <tfoot>
                        <tr class="border-t-2 border-slate-300">
                            <td colspan="8" class="px-2.5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-600">Total Pendapatan</td>
                            <td class="whitespace-nowrap px-2.5 py-3 text-right text-sm font-bold text-slate-900">Rp {{ number_format($ringkasan['pendapatan'], 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <p class="mt-3 text-[11px] text-slate-400">
            Total pendapatan hanya menjumlahkan transaksi berstatus keluar. Transaksi yang masih parkir belum memiliki biaya final.
        </p>

        <div class="mt-10 grid grid-cols-2 gap-10 text-center text-xs">
            <div>
                <p class="text-slate-500">Dicetak oleh,</p>
                <div class="h-16"></div>
                <p class="border-t border-slate-400 pt-1 font-semibold text-slate-800">{{ auth()->user()->nama_lengkap }}</p>
                <p class="capitalize text-slate-400">{{ auth()->user()->role }}</p>
            </div>
            <div>
                <p class="text-slate-500">Mengetahui,</p>
                <div class="h-16"></div>
                <p class="border-t border-slate-400 pt-1 font-semibold text-slate-800">Owner</p>
                <p class="text-slate-400">Pengesahan</p>
            </div>
        </div>

        <p class="mt-8 text-right text-[10px] text-slate-400">
            Dicetak pada {{ now()->locale('id')->translatedFormat('d F Y, H:i') }} WIB
        </p>
    </div>
@endsection
