@props([
    'name'  => '',
    'src'   => null,
    'size'  => 'md', // xs, sm, md, lg, xl
    'shape' => 'rounded', // rounded (2xl) or circle (full)
])

@php
    $sizeClasses = match ($size) {
        'xs' => 'h-7 w-7 text-xs',
        'sm' => 'h-9 w-9 text-xs',
        'lg' => 'h-14 w-14 text-xl',
        'xl' => 'h-16 w-16 text-2xl',
        default => 'h-11 w-11 text-sm font-bold', // md
    };

    $shapeClasses = $shape === 'circle' ? 'rounded-full' : 'rounded-2xl';

    $initials = '';
    if (! empty($name)) {
        $words = preg_split('/\s+/', trim($name));
        $initials = mb_substr($words[0] ?? '', 0, 1) . (isset($words[1]) ? mb_substr($words[1], 0, 1) : '');
    }
@endphp

<div {{ $attributes->merge(['class' => "relative inline-flex items-center justify-center font-black select-none {$sizeClasses} {$shapeClasses} bg-[#e6f4ee] text-[#185c54] dark:bg-[#185c54]/30 dark:text-[#a3e4c8] ring-4 ring-[#e6f4ee]/60 dark:ring-[#185c54]/10 shadow-2xs overflow-hidden"]) }}>
    @if ($src)
        <img src="{{ $src }}" alt="{{ $name }}" class="h-full w-full object-cover">
    @else
        <span>{{ strtoupper($initials ?: 'U') }}</span>
    @endif
</div>
