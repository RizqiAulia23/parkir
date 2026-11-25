@props(['label', 'value', 'hint' => null])

<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
    <p class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">{{ $value }}</p>
    @if ($hint)
        <p class="mt-1 text-xs text-slate-400">{{ $hint }}</p>
    @endif
</div>
