@extends('layouts.app')

@section('title', 'Transaksi Parkir')

@section('content')
    <x-page-header title="Transaksi Parkir" description="Catat kendaraan masuk dan keluar, lalu cetak struk.">
        <x-slot:actions>
            <a href="{{ route('transaksi.masuk.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700">
                <x-icon name="plus" class="h-4 w-4" />
                Transaksi Masuk
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Sedang Parkir" :value="$transaksiAktif" hint="Kendaraan di dalam area" />
        <x-stat-card label="Kapasitas Terisi" :value="$totalTerisi.' / '.$totalKapasitas" hint="Seluruh area" />
        <x-stat-card label="Selesai Hari Ini" :value="$selesaiHariIni" hint="Transaksi keluar" />
        <x-stat-card label="Pendapatan Hari Ini" :value="'Rp '.number_format($pendapatanHariIni, 0, ',', '.')" hint="Dari transaksi selesai" />
    </div>

    <div class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <div>
                <h2 class="text-sm font-semibold text-slate-900">Kendaraan Sedang Parkir</h2>
                <p class="text-xs text-slate-500">Pilih kendaraan untuk diproses keluar.</p>
            </div>
            <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-200">{{ $transaksiAktif }} aktif</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Plat Nomor</th>
                        <th class="px-5 py-3">Area</th>
                        <th class="px-5 py-3">Masuk</th>
                        <th class="px-5 py-3">Durasi</th>
                        <th class="px-5 py-3">Tarif</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($aktif as $trx)
                        @php($detik = max(0, now()->getTimestamp() - $trx->waktu_masuk->getTimestamp()))
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3">
                                <span class="font-semibold text-slate-800">{{ $trx->kendaraan->plat_nomor ?? '-' }}</span>
                                <span class="block text-xs capitalize text-slate-400">{{ $trx->kendaraan->jenis_kendaraan ?? '' }}</span>
                            </td>
                            <td class="px-5 py-3 text-slate-600">{{ $trx->area->nama_area ?? '-' }}</td>
                            <td class="whitespace-nowrap px-5 py-3 text-slate-600">{{ $trx->waktu_masuk->format('d/m/Y H:i') }}</td>
                            <td class="whitespace-nowrap px-5 py-3 text-slate-600">{{ intdiv($detik, 3600) }} jam {{ intdiv($detik % 3600, 60) }} menit</td>
                            <td class="whitespace-nowrap px-5 py-3 text-slate-600">Rp {{ number_format((float) ($trx->tarif->tarif_per_jam ?? 0), 0, ',', '.') }}/jam</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('transaksi.struk', $trx) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-slate-50">
                                        <x-icon name="ticket" class="h-4 w-4" />
                                        Tiket
                                    </a>
                                    <a href="{{ route('transaksi.keluar', $trx) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 px-2.5 py-1.5 text-xs font-medium text-white transition hover:bg-slate-800">
                                        Proses Keluar
                                        <x-icon name="arrow-right" class="h-4 w-4" />
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-sm text-slate-500">Tidak ada kendaraan yang sedang parkir.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($aktif->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">
                {{ $aktif->links() }}
            </div>
        @endif
    </div>

    <div class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <div>
                <h2 class="text-sm font-semibold text-slate-900">Riwayat Transaksi</h2>
                <p class="text-xs text-slate-500">Transaksi yang sudah selesai.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Plat Nomor</th>
                        <th class="px-5 py-3">Area</th>
                        <th class="px-5 py-3">Masuk</th>
                        <th class="px-5 py-3">Keluar</th>
                        <th class="px-5 py-3">Durasi</th>
                        <th class="px-5 py-3 text-right">Total</th>
                        <th class="px-5 py-3 text-right">Struk</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($riwayat as $trx)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3 font-medium text-slate-800">{{ $trx->kendaraan->plat_nomor ?? '-' }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $trx->area->nama_area ?? '-' }}</td>
                            <td class="whitespace-nowrap px-5 py-3 text-slate-600">{{ $trx->waktu_masuk->format('d/m/Y H:i') }}</td>
                            <td class="whitespace-nowrap px-5 py-3 text-slate-600">{{ $trx->waktu_keluar?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $trx->durasi_jam }} jam</td>
                            <td class="whitespace-nowrap px-5 py-3 text-right font-semibold text-slate-800">Rp {{ number_format((float) $trx->biaya_total, 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('transaksi.struk', $trx) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-slate-50">
                                    <x-icon name="printer" class="h-4 w-4" />
                                    Cetak
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-sm text-slate-500">Belum ada transaksi selesai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($riwayat->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">
                {{ $riwayat->links() }}
            </div>
        @endif
    </div>
@endsection
