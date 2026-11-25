@php
    $editing = isset($tarif) && $tarif !== null;
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
                <label for="jenis_kendaraan" class="mb-1.5 block text-sm font-medium text-slate-700">Jenis Kendaraan</label>
                <select name="jenis_kendaraan" id="jenis_kendaraan" class="{{ $inputClass }} @error('jenis_kendaraan') border-rose-400 @enderror" required>
                    @foreach (['motor' => 'Motor', 'mobil' => 'Mobil', 'lainnya' => 'Lainnya'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('jenis_kendaraan', $tarif->jenis_kendaraan ?? 'motor') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('jenis_kendaraan')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="tarif_per_jam" class="mb-1.5 block text-sm font-medium text-slate-700">Tarif per Jam (Rp)</label>
                <input
                    type="number"
                    name="tarif_per_jam"
                    id="tarif_per_jam"
                    value="{{ old('tarif_per_jam', $tarif->tarif_per_jam ?? '') }}"
                    min="0"
                    step="1"
                    class="{{ $inputClass }} @error('tarif_per_jam') border-rose-400 @enderror"
                    required
                >
                @error('tarif_per_jam')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <p class="mt-4 rounded-xl bg-slate-50 px-4 py-3 text-xs text-slate-500">
            Tarif dipakai saat kendaraan keluar. Biaya dihitung dari durasi jam (dibulatkan ke atas, minimal 1 jam) dikali tarif per jam.
        </p>
    </div>

    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
        <a href="{{ route('admin.tarif.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
            Batal
        </a>
        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700">
            <x-icon name="check" class="h-4 w-4" />
            {{ $submitLabel }}
        </button>
    </div>
</form>
