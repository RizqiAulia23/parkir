@php
    $editing = isset($area) && $area !== null;
    $inputClass = 'block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10';
@endphp

<form action="{{ $action }}" method="POST" class="space-y-5">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label for="nama_area" class="mb-1.5 block text-sm font-medium text-slate-700">Nama Area</label>
                <input
                    type="text"
                    name="nama_area"
                    id="nama_area"
                    value="{{ old('nama_area', $area->nama_area ?? '') }}"
                    maxlength="50"
                    class="{{ $inputClass }} @error('nama_area') border-rose-400 @enderror"
                    required
                >
                @error('nama_area')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="kapasitas" class="mb-1.5 block text-sm font-medium text-slate-700">Kapasitas</label>
                <input
                    type="number"
                    name="kapasitas"
                    id="kapasitas"
                    value="{{ old('kapasitas', $area->kapasitas ?? '') }}"
                    min="{{ $editing ? max(1, $area->terisi) : 1 }}"
                    step="1"
                    class="{{ $inputClass }} @error('kapasitas') border-rose-400 @enderror"
                    required
                >
                @error('kapasitas')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            @if ($editing)
                <div>
                    <span class="mb-1.5 block text-sm font-medium text-slate-700">Terisi Saat Ini</span>
                    <div class="flex items-center gap-2 rounded-xl bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700">
                        <x-icon name="car" class="h-4 w-4 text-slate-400" />
                        {{ $area->terisi }} kendaraan
                    </div>
                </div>
            @endif
        </div>

        <p class="mt-4 rounded-xl bg-slate-50 px-4 py-3 text-xs text-slate-500">
            Kolom terisi bertambah otomatis saat kendaraan masuk dan berkurang saat kendaraan keluar. Kapasitas tidak boleh lebih kecil dari jumlah kendaraan yang sedang parkir.
        </p>
    </div>

    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
        <a href="{{ route('admin.area.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
            Batal
        </a>
        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700">
            <x-icon name="check" class="h-4 w-4" />
            {{ $submitLabel }}
        </button>
    </div>
</form>
