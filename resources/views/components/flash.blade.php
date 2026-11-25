@if (session('success'))
    <div data-flash class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
        <x-icon name="check" class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" />
        <p class="flex-1">{{ session('success') }}</p>
    </div>
@endif

@if (session('error'))
    <div data-flash class="mb-6 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
        <x-icon name="x" class="mt-0.5 h-5 w-5 shrink-0 text-rose-600" />
        <p class="flex-1">{{ session('error') }}</p>
    </div>
@endif

@if ($errors->any())
    <div data-flash class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
        <p class="font-semibold">Periksa kembali data berikut:</p>
        <ul class="mt-1 list-inside list-disc space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
