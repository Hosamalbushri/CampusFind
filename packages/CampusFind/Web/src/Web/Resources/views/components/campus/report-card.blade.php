@props(['record'])

<x-web::card
    variant="interactive"
    padding="none"
    {{ $attributes->merge(['class' => 'group flex flex-col overflow-hidden']) }}
    data-public-id="{{ $record->publicId }}"
>
    <div class="relative flex h-52 items-center justify-center overflow-hidden bg-slate-100 dark:bg-slate-800/80">
        @if ($record->hasImage && $record->imageUrl)
            <img src="{{ $record->imageUrl }}" alt="{{ $record->title }}" class="h-full w-full object-cover transition-transform duration-300 motion-reduce:transition-none motion-reduce:transform-none group-hover:scale-105">
        @else
            <div class="flex flex-col items-center text-slate-400 dark:text-slate-600" aria-hidden="true">
                <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2v12a2 2 0 002 2z" /></svg>
                <span class="mt-1 text-xs font-medium">@lang('campusfind_web_web::app.web.browse.no_public_image')</span>
            </div>
        @endif

        <div class="absolute inset-x-3 top-3 flex items-start justify-between gap-2">
            <x-web::badge variant="mint" size="xs">@lang('campusfind_web_web::app.web.browse.record_'.$record->type)</x-web::badge>
            @if ($record->category)
                <x-web::badge variant="dark-blur" size="xs">{{ $record->category }}</x-web::badge>
            @endif
        </div>
        <x-web::badge variant="dark-blur" size="xs" class="absolute bottom-3 ltr:right-3 rtl:left-3">{{ $record->reference }}</x-web::badge>
    </div>

    <div class="flex flex-1 flex-col justify-between p-6">
        <div>
            <h2 class="line-clamp-1 text-lg font-bold text-slate-900 transition-colors group-hover:text-[#185c54] dark:text-white dark:group-hover:text-[#a3e4c8]">{{ $record->title }}</h2>
            @if ($record->description)
                <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-slate-500 dark:text-slate-400">{{ $record->description }}</p>
            @endif

            <dl class="mt-4 space-y-2 text-xs font-medium text-slate-500 dark:text-slate-400">
                @if ($record->location)
                    <div class="flex items-start gap-2"><dt class="font-bold text-slate-700 dark:text-slate-300">@lang('campusfind_web_web::app.web.browse.location_'.$record->type):</dt><dd>{{ $record->location }}</dd></div>
                @endif
                @if ($record->occurredAt)
                    <div class="flex items-start gap-2"><dt class="font-bold text-slate-700 dark:text-slate-300">@lang('campusfind_web_web::app.web.browse.date_'.$record->type):</dt><dd><time datetime="{{ $record->occurredAt->format('Y-m-d') }}">{{ $record->occurredAt->format('Y-m-d') }}</time></dd></div>
                @endif
                <div class="flex items-start gap-2"><dt class="font-bold text-slate-700 dark:text-slate-300">@lang('campusfind_web_web::app.web.browse.status_label'):</dt><dd>@lang('campusfind_web_web::app.web.browse.status_'.$record->status)</dd></div>
            </dl>
        </div>

        @if ($record->actionAvailable)
            <div class="mt-6 border-t border-slate-100 pt-4 dark:border-slate-800">
                @if ($record->type === 'found' && $record->detailDestination === 'found_item')
                    <div class="flex flex-wrap items-center gap-3">
                        <x-web::button href="{{ route('campusfind_web.web.items.show', ['reference' => $record->reference]) }}" variant="primary" size="sm">@lang('campusfind_web_web::app.web.browse.claim_ownership')</x-web::button>
                        <x-web::button href="{{ route('campusfind_web.web.items.show', ['reference' => $record->reference]) }}" variant="ghost" size="sm" class="text-[#185c54] dark:text-[#a3e4c8]">@lang('campusfind_web_web::app.web.browse.view_details')</x-web::button>
                    </div>
                @elseif ($record->type === 'lost' && $record->detailDestination === 'lost_report')
                    <x-web::button href="{{ route('campusfind_web.web.lost-reports.show', ['reference' => $record->reference]) }}" variant="primary" size="sm">@lang('campusfind_web_web::app.web.browse.i_found_this_item')</x-web::button>
                @endif
            </div>
        @endif
    </div>
</x-web::card>
