@props([
    'size' => 'lg',
])

@php
    $sizeClasses = match ($size) {
        'sm'    => 'max-w-3xl',
        'md'    => 'max-w-5xl',
        'xl'    => 'max-w-screen-2xl',
        'full'  => 'w-full',
        default => 'max-w-7xl', // lg
    };
@endphp

<div {{ $attributes->merge(['class' => "mx-auto w-full {$sizeClasses} px-4 sm:px-6 lg:px-8"]) }}>
    {{ $slot }}
</div>
