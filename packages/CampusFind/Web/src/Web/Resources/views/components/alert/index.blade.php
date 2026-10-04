@props([
    'variant' => 'info',
    'title'   => null,
    'role'    => null,
])

@php
    $variantClasses = match ($variant) {
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-900 dark:border-emerald-800/60 dark:bg-emerald-950/30 dark:text-emerald-200',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-900 dark:border-amber-800/60 dark:bg-amber-950/30 dark:text-amber-200',
        'danger'  => 'border-rose-200 bg-rose-50 text-rose-900 dark:border-rose-800/60 dark:bg-rose-950/30 dark:text-rose-200',
        'neutral' => 'border-slate-200 bg-slate-100 text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200',
        default   => 'border-[#185c54]/20 bg-[#e6f4ee] text-[#185c54] dark:border-[#a3e4c8]/20 dark:bg-[#185c54]/30 dark:text-[#a3e4c8]',
    };

    $resolvedRole = $role ?? (in_array($variant, ['danger', 'warning'], true) ? 'alert' : 'status');
@endphp

<div
    role="{{ $resolvedRole }}"
    {{ $attributes->merge(['class' => "rounded-xl border px-4 py-3 text-sm leading-relaxed {$variantClasses}"]) }}
>
    @if ($title)
        <p class="mb-1 font-extrabold">{{ $title }}</p>
    @endif

    {{ $slot }}
</div>
