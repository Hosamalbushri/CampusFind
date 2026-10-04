@props([
    'title',
    'description' => null,
    'align'       => 'start',
])

@php
    $alignment = $align === 'center' ? 'mx-auto max-w-3xl text-center' : 'max-w-3xl text-start';
@endphp

<header {{ $attributes->merge(['class' => $alignment]) }}>
    @isset($eyebrow)
        <div class="mb-4">{{ $eyebrow }}</div>
    @endisset

    <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white sm:text-4xl">{{ $title }}</h1>

    @if ($description)
        <p class="mt-2 text-sm leading-relaxed text-slate-500 dark:text-slate-400 sm:text-base">{{ $description }}</p>
    @endif

    @isset($actions)
        <div class="mt-5 flex flex-wrap items-center gap-3 {{ $align === 'center' ? 'justify-center' : '' }}">{{ $actions }}</div>
    @endisset
</header>
