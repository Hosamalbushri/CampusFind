@props([
    'variant'   => 'primary',
    'size'      => 'md',
    'href'      => null,
    'type'      => 'button',
    'icon'      => null,
    'iconRight' => null,
    'title'     => null,
    'loading'   => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-bold text-decoration-none transition-all duration-200 focus:outline-none focus:ring-4 cursor-pointer select-none active:scale-[0.98] disabled:opacity-50 disabled:pointer-events-none relative';

    $sizeClasses = match ($size) {
        'xs'    => 'px-3 py-1.5 text-xs gap-1.5 rounded-lg',
        'sm'    => 'px-3.5 py-2 text-xs gap-2 rounded-xl',
        'lg'    => 'px-6 py-3 text-base gap-2.5 rounded-xl',
        'xl'    => 'px-8 py-3.5 text-base sm:text-lg gap-3 rounded-2xl',
        default => 'px-5 py-2.5 text-sm gap-2 rounded-xl', // md
    };

    $variantClasses = match ($variant) {
        'secondary' => 'bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-100 border border-slate-200/90 dark:border-slate-700 shadow-xs hover:shadow focus:ring-slate-300 dark:focus:ring-slate-700',
        'mint'      => 'bg-[#e6f4ee] hover:bg-[#dff3ea] text-[#185c54] dark:bg-[#185c54]/30 dark:text-[#a3e4c8] dark:hover:bg-[#185c54]/40 border border-[#185c54]/10 dark:border-[#185c54]/30 shadow-xs focus:ring-[#185c54]/20',
        'outline'   => 'border border-slate-300 dark:border-slate-700 bg-transparent text-slate-700 dark:text-slate-200 hover:border-[#185c54] hover:text-[#185c54] dark:hover:text-[#a3e4c8] hover:bg-[#e6f4ee]/40 dark:hover:bg-[#185c54]/10 focus:ring-[#185c54]/20 shadow-none',
        'danger'    => 'bg-rose-600 hover:bg-rose-700 text-white shadow-xs focus:ring-rose-500/30 border border-transparent',
        'ghost'     => 'bg-transparent text-slate-600 dark:text-slate-400 hover:text-[#185c54] dark:hover:text-[#a3e4c8] hover:bg-slate-100 dark:hover:bg-slate-800 focus:ring-slate-300 shadow-none border border-transparent',
        default     => 'bg-[#185c54] hover:bg-[#134942] text-white shadow-sm hover:shadow-md focus:ring-[#185c54]/30 border border-transparent', // primary
    };

    $classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
    $hasVueBinding = $attributes->has(':loading') || $attributes->has('::loading') || $attributes->has(':title') || $attributes->has('::title');
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@elseif ($hasVueBinding || $attributes->has('loading'))
    <v-button
        button-type="{{ $type }}"
        button-class="{{ $classes }} {{ $attributes->get('class', '') }}"
        @if ($title)
            title="{{ $title }}"
        @endif
        {{ $attributes->except(['class', 'type']) }}
    >
        {{ $slot }}
    </v-button>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-button-template"
    >
        <button
            v-if="! loading"
            :type="buttonType || 'button'"
            :class="[buttonClass, '']"
            :disabled="disabled"
        >
            <slot>@{{ title }}</slot>
        </button>

        <button
            v-else
            :type="buttonType || 'button'"
            :class="[buttonClass, '']"
            disabled
            aria-busy="true"
        >
            <!-- Spinner -->
            <x-web::spinner class="absolute inset-0 m-auto" />

            <span class="relative h-full w-full opacity-0 select-none">
                <slot>@{{ title }}</slot>
            </span>
        </button>
    </script>

    <script type="module">
        app.component('v-button', {
            template: '#v-button-template',

            props: {
                loading: Boolean,
                buttonType: String,
                title: String,
                buttonClass: String,
                disabled: Boolean,
            },
        });
    </script>
@endPushOnce
