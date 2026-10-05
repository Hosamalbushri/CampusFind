<x-web::layouts>
    <x-web::section class="py-10">
        <div class="mb-10">
            <x-web::page-header
                :title="trans('campusfind_web_web::app.web.browse.title')"
                :description="trans('campusfind_web_web::app.web.browse.subtitle')"
            />

            @if ($hasFilterErrors)
                <x-web::alert variant="warning" class="mt-6">
                    @lang('campusfind_web_web::app.web.browse.invalid_filters')
                </x-web::alert>
            @endif

            <x-web::campus.report-filters
                class="mt-8"
                :filters="$filters"
                :categories="$categories"
                :action="route('campusfind_web.web.items.index')"
                :reset-url="route('campusfind_web.web.items.index', request()->filled('locale') ? ['locale' => request()->query('locale')] : [])"
            />

            <div class="mt-5 hidden items-center gap-3 rounded-xl bg-[#e6f4ee] px-4 py-3 text-sm font-bold text-[#185c54] dark:bg-[#185c54]/30 dark:text-[#a3e4c8]" role="status" aria-live="polite" aria-hidden="true" data-unified-loading>
                <x-web::spinner size="sm" />
                <span>@lang('campusfind_web_web::app.web.browse.loading')</span>
            </div>
        </div>

        <div id="items-shimmer-grid" class="mt-6 hidden grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 mb-10" aria-hidden="true">
            @for ($i = 0; $i < 6; $i++)
                <x-web::shimmer.card />
            @endfor
        </div>

        <div id="items-catalog-results">
            <p class="mb-5 text-sm font-semibold text-slate-500 dark:text-slate-400" aria-live="polite">
                {{ trans_choice('campusfind_web_web::app.web.browse.results_count', $searchResult->total, ['count' => $searchResult->total]) }}
            </p>

            @if ($searchResult->isNotEmpty())
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3" data-items-grid>
                    @foreach ($searchResult->items as $record)
                        <x-web::campus.report-card :record="$record" />
                    @endforeach
                </div>

                @if ($searchResult->hasPages())
                    <div id="items-lazy-sentinel" class="py-6 flex justify-center" data-lazy-sentinel data-next-url="{{ $searchResult->nextPage() ? route('campusfind_web.web.items.index', array_merge(request()->query(), ['page' => $searchResult->nextPage()])) : '' }}">
                        <div class="hidden items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400" data-lazy-infinite-spinner>
                            <x-web::spinner size="sm" />
                            <span>@lang('campusfind_web_web::app.web.browse.loading')</span>
                        </div>
                    </div>

                    <x-web::pagination
                        :current="$searchResult->currentPage"
                        :last="$searchResult->lastPage"
                        :previous-url="$searchResult->previousPage() ? route('campusfind_web.web.items.index', array_merge(request()->query(), ['page' => $searchResult->previousPage()])) : null"
                        :next-url="$searchResult->nextPage() ? route('campusfind_web.web.items.index', array_merge(request()->query(), ['page' => $searchResult->nextPage()])) : null"
                        :label="trans('campusfind_web_web::app.web.browse.pagination_label')"
                        :page-label="trans('campusfind_web_web::app.web.browse.page_of', ['current' => $searchResult->currentPage, 'last' => $searchResult->lastPage])"
                        :previous-label="trans('campusfind_web_web::app.web.browse.previous')"
                        :next-label="trans('campusfind_web_web::app.web.browse.next')"
                    />
                @endif
            @else
                <x-web::empty-state
                    :title="trans('campusfind_web_web::app.web.browse.no_results')"
                    :description="trans('campusfind_web_web::app.web.browse.no_results_hint')"
                />
            @endif
        </div>
    </x-web::section>
</x-web::layouts>

@pushOnce('scripts')
    <script>
        (function initUnifiedSearchAJAX() {
            let abortController = null;
            let debounceTimer = null;
            let lazyItemsObserver = null;
            let infiniteSentinelObserver = null;
            let isAppendLoading = false;

            function initLazyItemsObserver() {
                if (!('IntersectionObserver' in window)) return;
                if (lazyItemsObserver) lazyItemsObserver.disconnect();

                lazyItemsObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('opacity-100');
                            entry.target.classList.remove('opacity-0');
                            lazyItemsObserver.unobserve(entry.target);
                        }
                    });
                }, { rootMargin: '100px' });

                document.querySelectorAll('[data-lazy-item]').forEach(el => {
                    lazyItemsObserver.observe(el);
                });
            }

            function initInfiniteSentinel() {
                if (!('IntersectionObserver' in window)) return;
                const sentinel = document.querySelector('[data-lazy-sentinel]');
                if (!sentinel) return;

                const nextUrl = sentinel.getAttribute('data-next-url');
                if (!nextUrl) return;

                if (infiniteSentinelObserver) infiniteSentinelObserver.disconnect();

                infiniteSentinelObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting && !isAppendLoading) {
                            performAppendNextPage(nextUrl);
                        }
                    });
                }, { rootMargin: '250px' });

                infiniteSentinelObserver.observe(sentinel);
            }

            function performAppendNextPage(nextUrl) {
                if (isAppendLoading || !nextUrl) return;
                isAppendLoading = true;

                const spinner = document.querySelector('[data-lazy-infinite-spinner]');
                if (spinner) spinner.classList.remove('hidden');

                fetch(nextUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html, application/xhtml+xml',
                    }
                })
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newGrid = doc.querySelector('[data-items-grid]');
                    const newSentinel = doc.querySelector('[data-lazy-sentinel]');
                    const newPagination = doc.querySelector('#items-catalog-results nav, #items-catalog-results .pagination');
                    
                    const currentGrid = document.querySelector('[data-items-grid]');
                    const currentPagination = document.querySelector('#items-catalog-results nav, #items-catalog-results .pagination');
                    const currentSentinel = document.querySelector('[data-lazy-sentinel]');

                    if (newGrid && currentGrid) {
                        Array.from(newGrid.children).forEach(child => {
                            currentGrid.appendChild(child.cloneNode(true));
                        });
                    }

                    if (currentPagination && newPagination) {
                        currentPagination.replaceWith(newPagination.cloneNode(true));
                    } else if (currentPagination && !newPagination) {
                        currentPagination.remove();
                    }

                    if (currentSentinel && newSentinel) {
                        currentSentinel.setAttribute('data-next-url', newSentinel.getAttribute('data-next-url') || '');
                    } else if (currentSentinel) {
                        currentSentinel.remove();
                    }

                    if (window.location.href !== nextUrl) {
                        history.pushState({}, '', nextUrl);
                    }

                    initLazyItemsObserver();
                    initInfiniteSentinel();
                })
                .catch(err => {
                    console.error('Lazy scroll append error:', err);
                })
                .finally(() => {
                    isAppendLoading = false;
                    const spinner = document.querySelector('[data-lazy-infinite-spinner]');
                    if (spinner) spinner.classList.add('hidden');
                });
            }

            function performSearch(targetUrl) {
                const form = document.querySelector('[data-unified-search-form]');
                const resultsContainer = document.querySelector('#items-catalog-results');
                const loadingEl = document.querySelector('[data-unified-loading]');
                const shimmerGrid = document.querySelector('#items-shimmer-grid');
                if (!form || !resultsContainer) return;

                let url;
                if (targetUrl) {
                    url = new URL(targetUrl, window.location.origin);
                } else {
                    url = new URL(form.action || window.location.href, window.location.origin);
                    const formData = new FormData(form);
                    const params = new URLSearchParams();

                    for (const [key, value] of formData.entries()) {
                        if (value !== '' && value !== null) {
                            params.set(key, value);
                        }
                    }
                    url.search = params.toString();
                }

                if (abortController) {
                    abortController.abort();
                }
                abortController = new AbortController();

                if (loadingEl) {
                    loadingEl.classList.remove('hidden');
                    loadingEl.classList.add('flex');
                }

                if (shimmerGrid && !targetUrl) {
                    shimmerGrid.classList.remove('hidden');
                }

                resultsContainer.classList.add('opacity-50', 'transition-opacity', 'pointer-events-none');

                fetch(url.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html, application/xhtml+xml',
                    },
                    signal: abortController.signal
                })
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newResults = doc.querySelector('#items-catalog-results');
                    const currentResults = document.querySelector('#items-catalog-results');

                    if (newResults && currentResults) {
                        currentResults.innerHTML = newResults.innerHTML;
                    }

                    if (window.location.href !== url.toString()) {
                        history.pushState({}, '', url.toString());
                    }

                    initLazyItemsObserver();
                    initInfiniteSentinel();
                })
                .catch(err => {
                    if (err.name !== 'AbortError') {
                        console.error('AJAX Search error:', err);
                    }
                })
                .finally(() => {
                    if (loadingEl) {
                        loadingEl.classList.add('hidden');
                        loadingEl.classList.remove('flex');
                    }
                    if (shimmerGrid) {
                        shimmerGrid.classList.add('hidden');
                    }
                    const currentResults = document.querySelector('#items-catalog-results');
                    if (currentResults) {
                        currentResults.classList.remove('opacity-50', 'transition-opacity', 'pointer-events-none');
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', function() {
                initLazyItemsObserver();
                initInfiniteSentinel();
            });
            initLazyItemsObserver();
            initInfiniteSentinel();

            document.addEventListener('click', function(e) {
                const clearFiltersBtn = e.target.closest('[data-unified-search-form] a[href*="items"], [data-reset-filters]');
                if (clearFiltersBtn && !e.target.closest('#items-catalog-results')) {
                    e.preventDefault();
                    const form = document.querySelector('[data-unified-search-form]');
                    if (form) {
                        const inputs = form.querySelectorAll('input[type="text"], input[type="search"], input[type="date"], select');
                        inputs.forEach(input => {
                            if (input.tagName.toLowerCase() === 'select') {
                                input.selectedIndex = 0;
                            } else {
                                input.value = '';
                            }
                        });
                        const radioAll = form.querySelector('input[type="radio"][value="all"]');
                        if (radioAll) radioAll.checked = true;
                    }
                    clearTimeout(debounceTimer);
                    performSearch(clearFiltersBtn.href);
                    return;
                }

                const paginationLink = e.target.closest('#items-catalog-results nav a, #items-catalog-results .pagination a');
                if (paginationLink) {
                    e.preventDefault();
                    clearTimeout(debounceTimer);
                    performSearch(paginationLink.href);
                }
            });

            document.addEventListener('submit', function(e) {
                const form = e.target.closest('[data-unified-search-form]');
                if (form) {
                    e.preventDefault();
                    clearTimeout(debounceTimer);
                    performSearch();
                }
            });

            document.addEventListener('input', function(e) {
                const input = e.target.closest('[data-unified-search-form] input[type="search"], [data-unified-search-form] input[type="text"]');
                if (input) {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        performSearch();
                    }, 300);
                }
            });

            document.addEventListener('change', function(e) {
                const control = e.target.closest('[data-unified-search-form] select, [data-unified-search-form] input[type="radio"], [data-unified-search-form] input[type="date"]');
                if (control) {
                    performSearch();
                }
            });

            window.addEventListener('popstate', function() {
                performSearch(window.location.href);
            });
        })();
    </script>
@endPushOnce
