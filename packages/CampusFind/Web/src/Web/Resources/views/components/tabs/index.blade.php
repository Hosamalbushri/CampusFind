@props([
    'position' => 'start',
])

<v-tabs
    position="{{ $position }}"
    {{ $attributes->merge(['class' => 'w-full font-cairo']) }}
>
    <x-web::shimmer.tabs class="hidden" />

    {{ $slot }}
</v-tabs>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-tabs-template"
    >
        <div class="w-full font-cairo">
            <div
                class="flex gap-2 border-b border-slate-200/80 dark:border-slate-800 pb-2 mb-6 overflow-x-auto whitespace-nowrap scrollbar-none"
                :class="positionClass"
                role="tablist"
            >
                <button
                    v-for="tab in tabs"
                    :key="tab.title"
                    type="button"
                    role="tab"
                    :aria-selected="tab.isActive ? 'true' : 'false'"
                    class="cursor-pointer px-3.5 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm font-bold rounded-xl transition-all select-none border-0 bg-transparent focus:outline-none focus:ring-2 focus:ring-[#185c54]/30 shrink-0"
                    :class="tab.isActive ? 'bg-[#e6f4ee] text-[#185c54] dark:bg-[#185c54]/30 dark:text-[#a3e4c8] shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/70 dark:hover:bg-slate-800/50'"
                    @click="change(tab)"
                >
                    @{{ tab.title }}
                </button>
            </div>

            <div class="tab-content">
                <slot></slot>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-tabs', {
            template: '#v-tabs-template',

            props: {
                position: {
                    type: String,
                    default: 'start',
                },
            },

            data() {
                return {
                    tabs: [],
                };
            },

            computed: {
                positionClass() {
                    return {
                        'center': 'justify-center',
                        'end': 'justify-end',
                    }[this.position] || 'justify-start';
                },
            },

            methods: {
                change(selectedTab) {
                    this.tabs.forEach((tab) => {
                        tab.isActive = (tab.title === selectedTab.title);
                    });
                },
            },
        });
    </script>
@endPushOnce
