@extends('layouts.print')

@section('title', $transaksi->isMasuk() ? 'Tiket Parkir' : 'Struk Parkir')

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3 print:hidden">
        <a href="{{ route('transaksi.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
            <x-icon name="arrow-right" class="h-4 w-4 rotate-180 text-slate-400" />
            Kembali ke Transaksi
        </a>

        <div class="flex items-center gap-2">
            @if ($transaksi->isMasuk())
                <a href="{{ route('transaksi.keluar', $transaksi) }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    <x-icon name="switch" class="h-4 w-4 text-slate-400" />
                    Proses Keluar
                </a>
            @endif

            <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700">
                <x-icon name="printer" class="h-4 w-4" />
                Cetak
            </button>
        </div>
    </div>

    <div class="mx-auto w-full max-w-sm rounded-2xl border border-slate-200 bg-white p-6 shadow-sm print:max-w-xs print:rounded-none print:border-0 print:p-0 print:shadow-none">
        <div class="text-center">
            <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-xl bg-sky-600 text-lg font-bold text-white print:bg-slate-900">
                P
            </div>
            <h1 class="mt-3 text-base font-bold uppercase tracking-wide text-slate-900">Parkir Kantor</h1>
            <p class="text-[11px] text-slate-500">Sistem Informasi Parkir Perkantoran</p>

            <p class="mt-3 inline-flex rounded-full border border-slate-300 px-3 py-1 text-[11px] font-semibold uppercase tracking-widest text-slate-700">
                {{ $transaksi->isMasuk() ? 'Tiket Masuk' : 'Struk Pembayaran' }}
            </p>
        </div>

        <div class="mt-5 space-y-2 border-y border-dashed border-slate-300 py-4 text-sm">
            <div class="flex items-start justify-between gap-4">
                <span class="text-slate-500">No. Parkir</span>
                <span class="font-mono font-medium text-slate-900">#{{ str_pad((string) $transaksi->id_parkir, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="flex items-start justify-between gap-4">
                <span class="text-slate-500">Plat Nomor</span>
                <span class="font-mono text-base font-bold tracking-wide text-slate-900">{{ $transaksi->kendaraan->plat_nomor ?? '-' }}</span>
            </div>
            <div class="flex items-start justify-between gap-4">
                <span class="text-slate-500">Jenis</span>
                <span class="capitalize text-slate-800">{{ $transaksi->kendaraan->jenis_kendaraan ?? '-' }}</span>
            </div>
            <div class="flex items-start justify-between gap-4">
                <span class="text-slate-500">Area</span>
                <span class="text-right text-slate-800">{{ $transaksi->area->nama_area ?? '-' }}</span>
            </div>
            <div class="flex items-start justify-between gap-4">
                <span class="text-slate-500">Waktu Masuk</span>
                <span class="text-right font-mono text-slate-800">{{ $transaksi->waktu_masuk->format('d/m/Y H:i') }}</span>
            </div>

            @if (! $transaksi->isMasuk())
                <div class="flex items-start justify-between gap-4">
                    <span class="text-slate-500">Waktu Keluar</span>
                    <span class="text-right font-mono text-slate-800">{{ $transaksi->waktu_keluar?->format('d/m/Y H:i') ?? '-' }}</span>
                </div>
                <div class="flex items-start justify-between gap-4">
                    <span class="text-slate-500">Durasi</span>
                    <span class="text-right text-slate-800">{{ $transaksi->durasi_jam }} jam</span>
                </div>
                <div class="flex items-start justify-between gap-4">
                    <span class="text-slate-500">Tarif / Jam</span>
                    <span class="text-right text-slate-800">Rp {{ number_format((float) ($transaksi->tarif->tarif_per_jam ?? 0), 0, ',', '.') }}</span>
                </div>
            @endif

            <div class="flex items-start justify-between gap-4">
                <span class="text-slate-500">Petugas</span>
                <span class="text-right text-slate-800">{{ $transaksi->user->nama_lengkap ?? '-' }}</span>
            </div>
        </div>

        @if ($transaksi->isMasuk())
            <div class="mt-4 flex items-start gap-2 rounded-lg bg-amber-50 px-3 py-2.5 text-[11px] leading-relaxed text-amber-800">
                <x-icon name="clock" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-amber-600" />
                <p>Tiket ini dibawa saat keluar. Biaya dihitung dari durasi parkir, dibulatkan ke atas per jam dengan minimal 1 jam.</p>
            </div>
        @else
            <div class="mt-4 flex items-end justify-between gap-4 border-t-2 border-slate-900 pt-3">
                <span class="text-sm font-semibold uppercase tracking-wide text-slate-700">Total Bayar</span>
                <span class="text-xl font-bold text-slate-900">Rp {{ number_format((float) $transaksi->biaya_total, 0, ',', '.') }}</span>
            </div>

            <p class="mt-2 text-right text-[10px] uppercase tracking-wide text-slate-400">Lunas</p>
        @endif

        <div class="mt-5 text-center text-[11px] leading-relaxed text-slate-500">
            <p>{{ $transaksi->isMasuk() ? 'Simpan tiket ini sampai kendaraan keluar.' : 'Terima kasih atas kunjungan Anda.' }}</p>
            <p class="mt-2 text-[10px] uppercase tracking-wider text-slate-400">{{ config('app.name') }}</p>
        </div>
    </div>
@endsection
