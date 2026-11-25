@extends('layouts.app')

@section('title', 'Proses Keluar')

@section('content')
    <x-page-header title="Proses Parkir Keluar" description="Periksa detail kendaraan sebelum memproses pembayaran.">
        <x-slot:actions>
            <a href="{{ route('transaksi.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                <x-icon name="arrow-right" class="h-4 w-4 rotate-180 text-slate-400" />
                Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h2 class="text-sm font-semibold text-slate-900">Detail Kendaraan</h2>
                </div>
                <dl class="grid gap-x-6 gap-y-4 px-6 py-5 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Plat Nomor</dt>
                        <dd class="mt-1 text-lg font-semibold tracking-wide text-slate-900">{{ $transaksi->kendaraan->plat_nomor ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Jenis Kendaraan</dt>
                        <dd class="mt-1 text-sm capitalize text-slate-700">{{ $transaksi->kendaraan->jenis_kendaraan ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Area Parkir</dt>
                        <dd class="mt-1 text-sm text-slate-700">{{ $transaksi->area->nama_area ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Waktu Masuk</dt>
                        <dd class="mt-1 text-sm text-slate-700">{{ $transaksi->waktu_masuk->format('d/m/Y H:i:s') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Tarif per Jam</dt>
                        <dd class="mt-1 text-sm text-slate-700">Rp {{ number_format($preview['tarif_per_jam'], 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Petugas Masuk</dt>
                        <dd class="mt-1 text-sm text-slate-700">{{ $transaksi->user->nama_lengkap ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-sm font-semibold text-slate-900">Perhitungan Biaya</h2>
                <p class="mt-1 text-xs text-slate-500">Nilai di bawah adalah perkiraan saat halaman ini dibuka. Biaya final dihitung ulang saat tombol proses ditekan.</p>

                <div class="mt-5 space-y-3 border-t border-dashed border-slate-200 pt-5 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Durasi</span>
                        <span class="font-medium text-slate-800">{{ $preview['durasi_jam'] }} jam</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Tarif per jam</span>
                        <span class="font-medium text-slate-800">Rp {{ number_format($preview['tarif_per_jam'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between border-t border-slate-200 pt-3">
                        <span class="font-semibold text-slate-700">Total Bayar</span>
                        <span class="text-xl font-semibold text-slate-900">Rp {{ number_format($preview['biaya_total'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-sm font-semibold text-slate-900">Konfirmasi</h2>
                <p class="mt-1 text-xs text-slate-500">Pastikan kendaraan sudah berada di pos keluar.</p>

                <form action="{{ route('transaksi.keluar.store', $transaksi) }}" method="POST" class="mt-5 space-y-3">
                    @csrf
                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700">
                        <x-icon name="check" class="h-4 w-4" />
                        Proses Keluar & Cetak Struk
                    </button>
                    <a href="{{ route('transaksi.index') }}" class="flex w-full items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Batalkan
                    </a>
                </form>

                <div class="mt-5 flex items-start gap-3 rounded-xl bg-amber-50 px-4 py-3 text-xs text-amber-800">
                    <x-icon name="clock" class="mt-0.5 h-4 w-4 shrink-0 text-amber-600" />
                    <p>Durasi dibulatkan ke atas per jam dan minimal 1 jam, sesuai ketentuan parkir.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
