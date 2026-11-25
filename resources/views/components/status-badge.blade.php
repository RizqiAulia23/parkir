@props(['status'])

@php
    $kelas = $status === 'masuk'
        ? 'bg-amber-50 text-amber-700 ring-amber-200'
        : 'bg-emerald-50 text-emerald-700 ring-emerald-200';
@endphp

<span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $kelas }}">
    <span class="h-1.5 w-1.5 rounded-full {{ $status === 'masuk' ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
    {{ $status === 'masuk' ? 'Masuk' : 'Keluar' }}
</span>
