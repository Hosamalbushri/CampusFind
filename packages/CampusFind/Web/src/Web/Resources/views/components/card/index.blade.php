@props([
    'title'        => null,
    'subtitle'     => null,
    'description'  => null,
    'variant'      => 'default',
    'padding'      => 'md',
    'badge'        => null,
    'badgeVariant' => 'mint',
])

@php
    $sub = $subtitle ?? $description;

    $paddingClasses = match ($padding) {
        'none'  => 'p-0',
        'sm'    => 'p-4',
        'lg'    => 'p-8 sm:p-10',
        'xl'    => 'p-10 sm:p-12',
        default => 'p-6 sm:p-8', // md
    };

    $variantClasses = match ($variant) {
        'interactive' => 'bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-xl hover:-translate-y-1 hover:border-[#185c54]/40 transition-all duration-300',
        'elevated'    => 'bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xl shadow-slate-200/40 dark:shadow-none',
        'mint'        => 'bg-[#e6f4ee] dark:bg-[#185c54]/20 border border-[#185c54]/15 dark:border-[#185c54]/30 shadow-xs',
        'flat'        => 'bg-slate-50/80 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800/80 shadow-none',
        default       => 'bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-md transition-shadow duration-200',
    };
@endphp

<div {{ $attributes->merge(['class' => "rounded-3xl {$paddingClasses} {$variantClasses}"]) }}>
    @if ($title || $sub || $badge)
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                @if ($title)
                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        {{ $title }}
                    </h3>
                @endif

                @if ($sub)
                    <p class="mt-1.5 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        {{ $sub }}
                    </p>
                @endif
            </div>

            @if ($badge)
                <x-web::badge :variant="$badgeVariant">
                    {{ $badge }}
                </x-web::badge>
            @endif
        </div>
    @endif

    {{ $slot }}
</div>
