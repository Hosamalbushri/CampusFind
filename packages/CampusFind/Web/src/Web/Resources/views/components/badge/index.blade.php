@props([
    'variant' => 'mint',
    'size'    => 'md',
    'dot'     => false,
])

@php
    $baseClasses = 'inline-flex items-center gap-1.5 font-bold rounded-full transition-all duration-150 select-none';

    $sizeClasses = match ($size) {
        'xs'    => 'px-2 py-0.5 text-[10px]',
        'sm'    => 'px-2.5 py-0.5 text-[11px]',
        'lg'    => 'px-4 py-1.5 text-sm',
        default => 'px-3 py-1 text-xs',
    };

    $variantClasses = match ($variant) {
        'primary'   => 'bg-[#185c54] text-white shadow-2xs',
        'outline'   => 'border border-slate-300/80 dark:border-slate-700 bg-white/90 dark:bg-slate-900/90 text-slate-700 dark:text-slate-300 backdrop-blur-xs',
        'neutral'   => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200/60 dark:border-slate-700/60',
        'dark-blur' => 'bg-black/60 text-white backdrop-blur-xs border border-white/10 font-mono',
        'success'   => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/40',
        'danger'    => 'bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300 border border-rose-200/60 dark:border-rose-800/40',
        'warning'   => 'bg-amber-50 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/40',
        default     => 'bg-[#e6f4ee] text-[#185c54] dark:bg-[#185c54]/30 dark:text-[#a3e4c8] border border-[#185c54]/10 dark:border-[#185c54]/30', // mint
    };

    $dotColorClasses = match ($variant) {
        'primary' => 'bg-white',
        'success' => 'bg-emerald-500',
        'danger'  => 'bg-rose-500',
        'warning' => 'bg-amber-500',
        'neutral' => 'bg-slate-400',
        default   => 'bg-[#185c54] dark:bg-[#a3e4c8]',
    };

    $classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if ($dot)
        <span class="h-1.5 w-1.5 rounded-full {{ $dotColorClasses }}" aria-hidden="true"></span>
    @endif

    {{ $slot }}
</span>
