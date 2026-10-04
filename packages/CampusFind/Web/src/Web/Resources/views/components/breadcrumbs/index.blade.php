@props([
    'items' => [],
])

<nav aria-label="{{ trans('campusfind_web_web::app.web.accessibility.breadcrumbs') }}" {{ $attributes->merge(['class' => 'flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 font-cairo']) }}>
    <a href="{{ route('campusfind_web.web.home') }}" class="hover:text-[#185c54] dark:hover:text-[#a3e4c8] text-decoration-none transition-colors">
        @lang('campusfind_web_web::app.web.home')
    </a>

    @if (! empty($items))
        @foreach ($items as $key => $value)
            @php
                if (is_array($value)) {
                    $label = $value['title'] ?? $value['label'] ?? $key;
                    $url = $value['url'] ?? $value['href'] ?? null;
                } else {
                    $label = $key;
                    $url = $value;
                }
            @endphp
            <span class="text-slate-300 dark:text-slate-700">/</span>
            @if ($loop->last || empty($url))
                <span class="font-bold text-slate-900 dark:text-slate-200">
                    {{ $label }}
                </span>
            @else
                <a href="{{ $url }}" class="hover:text-[#185c54] dark:hover:text-[#a3e4c8] text-decoration-none transition-colors">
                    {{ $label }}
                </a>
            @endif
        @endforeach
    @else
        {{ $slot }}
    @endif
</nav>
