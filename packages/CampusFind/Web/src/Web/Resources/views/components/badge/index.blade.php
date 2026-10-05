@props([
    'variant' => 'mint',
    'size'    => 'md',
    'dot'     => false,
])

@php
    $baseClasses = 'inline-flex items-center gap-1.5 font-bold rounded-full transition-all duration-200 select-none tracking-tight';

    $sizeClasses = match ($size) {
        'xs'    => 'px-2.5 py-1 text-[11px] leading-none',
        'sm'    => 'px-3 py-1 text-xs leading-none',
        'lg'    => 'px-4 py-2 text-sm leading-none',
        default => 'px-3.5 py-1.5 text-xs leading-none', // md
    };

    $variantClasses = match ($variant) {
        'primary'   => 'bg-[#185c54] text-white border border-[#124640] shadow-2xs dark:bg-[#238378] dark:text-white dark:border-[#185c54]',
        'outline'   => 'bg-white text-slate-900 border border-slate-300 shadow-2xs dark:bg-slate-900 dark:text-slate-100 dark:border-slate-700',
        'neutral'   => 'bg-slate-200 text-slate-900 border border-slate-300 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700',
        'dark-blur' => 'bg-slate-900 text-white border border-slate-700 shadow-sm dark:bg-slate-950 dark:text-white dark:border-slate-800',
        'success'   => 'bg-emerald-50 text-emerald-950 border border-emerald-300 shadow-2xs dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-700',
        'danger'    => 'bg-rose-50 text-rose-950 border border-rose-300 shadow-2xs dark:bg-rose-950 dark:text-rose-200 dark:border-rose-700',
        'warning'   => 'bg-amber-50 text-amber-950 border border-amber-300 shadow-2xs dark:bg-amber-950 dark:text-amber-200 dark:border-amber-700',
        default     => 'bg-[#e6f4ee] text-[#124640] border border-[#185c54]/30 dark:bg-[#185c54]/40 dark:text-[#a3e4c8] dark:border-[#185c54]/60', // mint
    };

    $dotColorClasses = match ($variant) {
        'primary' => 'bg-white',
        'success' => 'bg-emerald-600 dark:bg-emerald-400',
        'danger'  => 'bg-rose-600 dark:bg-rose-400',
        'warning' => 'bg-amber-600 dark:bg-amber-400',
        'neutral' => 'bg-slate-600 dark:bg-slate-400',
        default   => 'bg-[#185c54] dark:bg-[#a3e4c8]',
    };

    $classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if ($dot)
        <span class="h-1.5 w-1.5 rounded-full shrink-0 {{ $dotColorClasses }}" aria-hidden="true"></span>
    @endif

    {{ $slot }}
</span>
