@extends('layouts.app')

@section('title', 'Transaksi Masuk')

@section('content')
    <x-page-header title="Transaksi Parkir Masuk" description="Catat kendaraan yang baru masuk ke area parkir.">
        <x-slot:actions>
            <a href="{{ route('transaksi.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                <x-icon name="arrow-right" class="h-4 w-4 rotate-180 text-slate-400" />
                Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    @if ($areaList->isEmpty())
        <div class="mb-6 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            <x-icon name="pin" class="mt-0.5 h-5 w-5 shrink-0 text-amber-600" />
            <p>Semua area parkir penuh. Kendaraan tidak dapat masuk sampai ada kendaraan yang keluar.</p>
        </div>
    @endif

    <form action="{{ route('transaksi.masuk.store') }}" method="POST" class="space-y-5">
        @csrf

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900">Data Kendaraan</h2>
            <p class="mt-1 text-xs text-slate-500">Pilih kendaraan terdaftar atau input plat baru secara langsung.</p>

            <div class="mt-5 space-y-5">
                <div>
                    <label for="id_kendaraan" class="mb-1.5 block text-sm font-medium text-slate-700">Kendaraan Terdaftar</label>
                    <select
                        name="id_kendaraan"
                        id="id_kendaraan"
                        class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 @error('id_kendaraan') border-rose-400 @enderror"
                    >
                        <option value="">— Input kendaraan baru —</option>
                        @foreach ($kendaraanList as $item)
                            <option value="{{ $item->id_kendaraan }}" @selected((string) old('id_kendaraan') === (string) $item->id_kendaraan)>
                                {{ $item->plat_nomor }} — {{ ucfirst($item->jenis_kendaraan) }}{{ $item->pemilik ? ' — '.$item->pemilik : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_kendaraan')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-slate-400">Hanya kendaraan yang tidak sedang parkir yang muncul di daftar.</p>
                </div>

                <div id="kendaraan-baru" class="rounded-xl border border-dashed border-slate-300 bg-slate-50/60 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Data kendaraan baru</p>

                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="plat_nomor" class="mb-1.5 block text-sm font-medium text-slate-700">Plat Nomor</label>
                            <input
                                type="text"
                                name="plat_nomor"
                                id="plat_nomor"
                                value="{{ old('plat_nomor') }}"
                                maxlength="15"
                                placeholder="Contoh: B 1234 XYZ"
                                class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm uppercase text-slate-900 shadow-sm outline-none transition placeholder:normal-case placeholder:text-slate-400 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 @error('plat_nomor') border-rose-400 @enderror"
                            >
                            @error('plat_nomor')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="jenis_kendaraan" class="mb-1.5 block text-sm font-medium text-slate-700">Jenis Kendaraan</label>
                            <select
                                name="jenis_kendaraan"
                                id="jenis_kendaraan"
                                class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 @error('jenis_kendaraan') border-rose-400 @enderror"
                            >
                                <option value="">Pilih jenis</option>
                                @foreach (['motor' => 'Motor', 'mobil' => 'Mobil', 'lainnya' => 'Lainnya'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('jenis_kendaraan') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('jenis_kendaraan')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="warna" class="mb-1.5 block text-sm font-medium text-slate-700">Warna (opsional)</label>
                            <input
                                type="text"
                                name="warna"
                                id="warna"
                                value="{{ old('warna') }}"
                                maxlength="20"
                                class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10"
                                placeholder="Contoh: Hitam"
                            >
                        </div>

                        <div>
                            <label for="pemilik" class="mb-1.5 block text-sm font-medium text-slate-700">Pemilik (opsional)</label>
                            <input
                                type="text"
                                name="pemilik"
                                id="pemilik"
                                value="{{ old('pemilik') }}"
                                maxlength="100"
                                class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10"
                                placeholder="Nama karyawan / tamu"
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900">Area & Tarif</h2>
            <p class="mt-1 text-xs text-slate-500">Area yang sudah penuh tidak dapat dipilih.</p>

            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="id_area" class="mb-1.5 block text-sm font-medium text-slate-700">Area Parkir</label>
                    <select
                        name="id_area"
                        id="id_area"
                        class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 @error('id_area') border-rose-400 @enderror"
                        required
                    >
                        <option value="">Pilih area</option>
                        @foreach ($areaList as $item)
                            <option value="{{ $item->id_area }}" @selected((string) old('id_area') === (string) $item->id_area)>
                                {{ $item->nama_area }} — sisa {{ $item->sisaKapasitas() }} dari {{ $item->kapasitas }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_area')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="id_tarif" class="mb-1.5 block text-sm font-medium text-slate-700">Tarif</label>
                    <select
                        name="id_tarif"
                        id="id_tarif"
                        class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 @error('id_tarif') border-rose-400 @enderror"
                        required
                    >
                        <option value="">Pilih tarif</option>
                        @foreach ($tarifList as $item)
                            <option value="{{ $item->id_tarif }}" @selected((string) old('id_tarif') === (string) $item->id_tarif)>
                                {{ ucfirst($item->jenis_kendaraan) }} — Rp {{ number_format((float) $item->tarif_per_jam, 0, ',', '.') }} / jam
                            </option>
                        @endforeach
                    </select>
                    @error('id_tarif')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-5 flex items-start gap-3 rounded-xl bg-sky-50 px-4 py-3 text-xs text-sky-800">
                <x-icon name="clock" class="mt-0.5 h-4 w-4 shrink-0 text-sky-600" />
                <p>Waktu masuk dicatat otomatis. Biaya dihitung saat kendaraan keluar dengan pembulatan ke atas, minimal 1 jam.</p>
            </div>
        </div>

        <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
            <a href="{{ route('transaksi.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                Batal
            </a>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700">
                <x-icon name="ticket" class="h-4 w-4" />
                Simpan & Cetak Tiket
            </button>
        </div>
    </form>
@endsection
