@props(['href', 'active' => false, 'icon' => 'dashboard'])

<a href="{{ $href }}" @class([
    'group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition',
    'bg-sky-50 text-sky-700' => $active,
    'text-slate-600 hover:bg-slate-50 hover:text-slate-900' => ! $active,
])>
    <x-icon
        :name="$icon"
        class="h-5 w-5 shrink-0 {{ $active ? 'text-sky-600' : 'text-slate-400 group-hover:text-slate-500' }}"
    />
    <span class="truncate">{{ $slot }}</span>
</a>
