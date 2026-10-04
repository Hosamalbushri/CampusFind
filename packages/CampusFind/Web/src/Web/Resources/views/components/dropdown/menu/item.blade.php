@props([
    'href'   => null,
    'icon'   => null,
    'active' => false,
])

@php
    $baseClasses = 'flex items-center gap-2.5 w-full px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-[#e6f4ee] hover:text-[#185c54] dark:hover:bg-[#185c54]/20 dark:hover:text-[#a3e4c8] transition-colors cursor-pointer text-decoration-none';
    $activeClasses = $active ? 'bg-[#e6f4ee] text-[#185c54] dark:bg-[#185c54]/30 dark:text-[#a3e4c8]' : '';
@endphp

<li>
    @if ($href)
        <a href="{{ $href }}" role="menuitem" {{ $attributes->merge(['class' => "{$baseClasses} {$activeClasses}"]) }}>
            @if ($icon)
                <span class="shrink-0">{{ $icon }}</span>
            @endif
            <span>{{ $slot }}</span>
        </a>
    @else
        <button type="button" role="menuitem" {{ $attributes->merge(['class' => "{$baseClasses} {$activeClasses} border-0 bg-transparent text-start rtl:text-right"]) }}>
            @if ($icon)
                <span class="shrink-0">{{ $icon }}</span>
            @endif
            <span>{{ $slot }}</span>
        </button>
    @endif
</li>
