@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <x-page-header
        :title="'Selamat datang, '.$user->nama_lengkap"
        :description="'Ringkasan operasional parkir per '.now()->locale('id')->translatedFormat('d F Y').'.'"
    >
        <x-slot:actions>
            @if ($user->isAdmin() || $user->isPetugas())
                <a href="{{ route('transaksi.masuk.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700">
                    <x-icon name="plus" class="h-4 w-4" />
                    Transaksi Masuk
                </a>
            @endif
            @if ($user->isAdmin() || $user->isOwner())
                <a href="{{ route('laporan.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    <x-icon name="chart" class="h-4 w-4 text-slate-400" />
                    Lihat Laporan
                </a>
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($cards as $card)
            <x-stat-card :label="$card['label']" :value="$card['value']" :hint="$card['hint'] ?? null" />
        @endforeach
    </div>

    @if ($user->isAdmin())
        <div class="mt-6 grid gap-6 xl:grid-cols-5">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-3">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">Okupansi Area Parkir</h2>
                        <p class="text-xs text-slate-500">Kapasitas terpakai saat ini.</p>
                    </div>
                    <a href="{{ route('admin.area.index') }}" class="text-xs font-medium text-sky-600 hover:text-sky-700">Kelola area</a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($areaList as $area)
                        @php($persen = $area->kapasitas > 0 ? min(100, (int) round($area->terisi / $area->kapasitas * 100)) : 0)
                        <div class="px-5 py-4">
                            <div class="flex items-center justify-between gap-3">
                                <p class="truncate text-sm font-medium text-slate-800">{{ $area->nama_area }}</p>
                                <p class="shrink-0 text-xs text-slate-500">{{ $area->terisi }} / {{ $area->kapasitas }}</p>
                            </div>
                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-2 rounded-full {{ $persen >= 90 ? 'bg-rose-500' : ($persen >= 70 ? 'bg-amber-500' : 'bg-sky-500') }}" style="width: {{ $persen }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="px-5 py-6 text-sm text-slate-500">Belum ada area parkir.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <h2 class="text-sm font-semibold text-slate-900">Log Aktivitas Terbaru</h2>
                    <a href="{{ route('admin.log.index') }}" class="text-xs font-medium text-sky-600 hover:text-sky-700">Lihat semua</a>
                </div>
                <ul class="divide-y divide-slate-100">
                    @forelse ($logTerbaru as $log)
                        <li class="px-5 py-3">
                            <p class="text-sm text-slate-700">{{ $log->aktivitas }}</p>
                            <p class="mt-0.5 text-xs text-slate-400">
                                {{ $log->user->username ?? 'Sistem' }} &middot; {{ $log->waktu_aktivitas?->format('d/m/Y H:i') }}
                            </p>
                        </li>
                    @empty
                        <li class="px-5 py-6 text-sm text-slate-500">Belum ada aktivitas.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    @endif

    @if ($user->isAdmin() || $user->isPetugas())
        <div class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">Kendaraan Sedang Parkir</h2>
                    <p class="text-xs text-slate-500">Transaksi dengan status masuk.</p>
                </div>
                <a href="{{ route('transaksi.index') }}" class="text-xs font-medium text-sky-600 hover:text-sky-700">Buka transaksi</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Plat Nomor</th>
                            <th class="px-5 py-3">Area</th>
                            <th class="px-5 py-3">Waktu Masuk</th>
                            <th class="px-5 py-3">Durasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($transaksiAktifList as $trx)
                            @php($detik = max(0, now()->getTimestamp() - $trx->waktu_masuk->getTimestamp()))
                            <tr>
                                <td class="px-5 py-3 font-medium text-slate-800">
                                    {{ $trx->kendaraan->plat_nomor ?? '-' }}
                                    <span class="ml-1 text-xs font-normal capitalize text-slate-400">{{ $trx->kendaraan->jenis_kendaraan ?? '' }}</span>
                                </td>
                                <td class="px-5 py-3 text-slate-600">{{ $trx->area->nama_area ?? '-' }}</td>
                                <td class="px-5 py-3 text-slate-600">{{ $trx->waktu_masuk->format('d/m/Y H:i') }}</td>
                                <td class="px-5 py-3 text-slate-600">{{ intdiv($detik, 3600) }} jam {{ intdiv($detik % 3600, 60) }} menit</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-sm text-slate-500">Tidak ada kendaraan yang sedang parkir.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if ($user->isOwner())
        <div class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">Transaksi Selesai Terakhir</h2>
                    <p class="text-xs text-slate-500">Pendapatan dari kendaraan yang sudah keluar.</p>
                </div>
                <a href="{{ route('laporan.index') }}" class="text-xs font-medium text-sky-600 hover:text-sky-700">Buka laporan</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Plat Nomor</th>
                            <th class="px-5 py-3">Area</th>
                            <th class="px-5 py-3">Keluar</th>
                            <th class="px-5 py-3">Durasi</th>
                            <th class="px-5 py-3 text-right">Biaya</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($transaksiTerakhir as $trx)
                            <tr>
                                <td class="px-5 py-3 font-medium text-slate-800">{{ $trx->kendaraan->plat_nomor ?? '-' }}</td>
                                <td class="px-5 py-3 text-slate-600">{{ $trx->area->nama_area ?? '-' }}</td>
                                <td class="px-5 py-3 text-slate-600">{{ $trx->waktu_keluar?->format('d/m/Y H:i') ?? '-' }}</td>
                                <td class="px-5 py-3 text-slate-600">{{ $trx->durasi_jam }} jam</td>
                                <td class="px-5 py-3 text-right font-medium text-slate-800">Rp {{ number_format((float) $trx->biaya_total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-sm text-slate-500">Belum ada transaksi selesai.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
