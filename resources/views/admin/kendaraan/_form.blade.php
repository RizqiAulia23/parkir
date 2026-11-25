@php
    $editing = isset($kendaraan) && $kendaraan !== null;
    $inputClass = 'block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10';
@endphp

<form action="{{ $action }}" method="POST" class="space-y-5">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="plat_nomor" class="mb-1.5 block text-sm font-medium text-slate-700">Plat Nomor</label>
                <input
                    type="text"
                    name="plat_nomor"
                    id="plat_nomor"
                    value="{{ old('plat_nomor', $kendaraan->plat_nomor ?? '') }}"
                    maxlength="15"
                    class="{{ $inputClass }} uppercase @error('plat_nomor') border-rose-400 @enderror"
                    placeholder="Contoh: B 1234 XYZ"
                    required
                >
                @error('plat_nomor')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="jenis_kendaraan" class="mb-1.5 block text-sm font-medium text-slate-700">Jenis Kendaraan</label>
                <select name="jenis_kendaraan" id="jenis_kendaraan" class="{{ $inputClass }} @error('jenis_kendaraan') border-rose-400 @enderror" required>
                    @foreach (['motor' => 'Motor', 'mobil' => 'Mobil', 'lainnya' => 'Lainnya'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('jenis_kendaraan', $kendaraan->jenis_kendaraan ?? 'motor') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('jenis_kendaraan')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="warna" class="mb-1.5 block text-sm font-medium text-slate-700">Warna</label>
                <input
                    type="text"
                    name="warna"
                    id="warna"
                    value="{{ old('warna', $kendaraan->warna ?? '') }}"
                    maxlength="20"
                    class="{{ $inputClass }} @error('warna') border-rose-400 @enderror"
                    placeholder="Contoh: Hitam"
                >
                @error('warna')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="pemilik" class="mb-1.5 block text-sm font-medium text-slate-700">Nama Pemilik</label>
                <input
                    type="text"
                    name="pemilik"
                    id="pemilik"
                    value="{{ old('pemilik', $kendaraan->pemilik ?? '') }}"
                    maxlength="100"
                    class="{{ $inputClass }} @error('pemilik') border-rose-400 @enderror"
                    placeholder="Nama karyawan / tamu"
                >
                @error('pemilik')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="sm:col-span-2">
                <label for="id_user" class="mb-1.5 block text-sm font-medium text-slate-700">Akun Karyawan (opsional)</label>
                <select name="id_user" id="id_user" class="{{ $inputClass }} @error('id_user') border-rose-400 @enderror">
                    <option value="">Tidak terhubung ke akun</option>
                    @foreach ($users as $item)
                        <option value="{{ $item->id_user }}" @selected((string) old('id_user', $kendaraan->id_user ?? '') === (string) $item->id_user)>
                            {{ $item->nama_lengkap }} ({{ $item->username }})
                        </option>
                    @endforeach
                </select>
                @error('id_user')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
        <a href="{{ route('admin.kendaraan.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
            Batal
        </a>
        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700">
            <x-icon name="check" class="h-4 w-4" />
            {{ $submitLabel }}
        </button>
    </div>
</form>
