@props([
    'badge'        => null,
    'badgeVariant' => 'mint',
    'title'        => null,
    'subtitle'     => null,
    'description'  => null,
    'align'        => 'center',
    'container'    => true,
])

@php
    $sub = $subtitle ?? $description;
    $isCenter = $align === 'center';
@endphp

<section {{ $attributes->merge(['class' => 'py-12 sm:py-16 lg:py-20 font-cairo']) }}>
    @if ($container)
        <x-web::container>
            @if ($badge || $title || $sub)
                <div class="{{ $isCenter ? 'mx-auto max-w-3xl text-center' : 'max-w-2xl text-start' }} mb-10 sm:mb-12">
                    @if ($badge)
                        <div class="mb-4">
                            <x-web::badge :variant="$badgeVariant" size="md">
                                {{ $badge }}
                            </x-web::badge>
                        </div>
                    @endif

                    @if ($title)
                        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white sm:text-4xl">
                            {{ $title }}
                        </h2>
                    @endif

                    @if ($sub)
                        <p class="mt-3 text-base sm:text-lg text-slate-600 dark:text-slate-400 leading-relaxed">
                            {{ $sub }}
                        </p>
                    @endif
                </div>
            @endif

            {{ $slot }}
        </x-web::container>
    @else
        {{ $slot }}
    @endif
</section>
