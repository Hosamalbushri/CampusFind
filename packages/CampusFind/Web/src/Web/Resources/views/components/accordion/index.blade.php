@props([
    'isActive' => false,
    'title'    => null,
])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden font-cairo shadow-xs transition-all']) }}>
    <v-accordion
        is-active="{{ filter_var($isActive, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false' }}"
        {{ $attributes }}
    >
        <x-web::shimmer.accordion />

        @isset($header)
            <template v-slot:header="{ toggle, isOpen }">
                <div
                    {{ $header->attributes->merge(['class' => 'flex w-full items-center justify-between p-5 text-start cursor-pointer select-none hover:bg-slate-50/70 dark:hover:bg-slate-800/50 transition-colors focus:outline-none focus:ring-4 focus:ring-inset focus:ring-[#185c54]/20']) }}
                    @click="toggle"
                    role="button"
                    tabindex="0"
                    :aria-expanded="isOpen ? 'true' : 'false'"
                    @keydown.enter.prevent="toggle"
                    @keydown.space.prevent="toggle"
                >
                    <div class="flex-1">
                        {{ $header }}
                    </div>

                    <span
                        :class="`ltr:ml-4 rtl:mr-4 shrink-0 text-slate-400 transition-transform duration-200 ${isOpen ? 'rotate-180' : ''}`"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </span>
                </div>
            </template>
        @elseif($title)
            <template v-slot:header="{ toggle, isOpen }">
                <div
                    class="flex w-full items-center justify-between p-5 text-start cursor-pointer select-none hover:bg-slate-50/70 dark:hover:bg-slate-800/50 transition-colors focus:outline-none focus:ring-4 focus:ring-inset focus:ring-[#185c54]/20"
                    @click="toggle"
                    role="button"
                    tabindex="0"
                    :aria-expanded="isOpen ? 'true' : 'false'"
                    @keydown.enter.prevent="toggle"
                    @keydown.space.prevent="toggle"
                >
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white">
                        {{ $title }}
                    </h3>

                    <span
                        :class="`ltr:ml-4 rtl:mr-4 shrink-0 text-slate-400 transition-transform duration-200 ${isOpen ? 'rotate-180' : ''}`"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </span>
                </div>
            </template>
        @endisset

        @isset($content)
            <template v-slot:content="{ isOpen }">
                <div
                    {{ $content->attributes->merge(['class' => 'border-t border-slate-100 dark:border-slate-800/80 p-5 text-sm text-slate-600 dark:text-slate-400 leading-relaxed']) }}
                    v-show="isOpen"
                >
                    {{ $content }}
                </div>
            </template>
        @elseif(!empty((string) $slot))
            <template v-slot:content="{ isOpen }">
                <div
                    class="border-t border-slate-100 dark:border-slate-800/80 p-5 text-sm text-slate-600 dark:text-slate-400 leading-relaxed"
                    v-show="isOpen"
                >
                    {{ $slot }}
                </div>
            </template>
        @endisset
    </v-accordion>
</div>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-accordion-template"
    >
        <div>
            <slot
                name="header"
                :toggle="toggle"
                :is-open="isOpen"
                :isOpen="isOpen"
            ></slot>

            <slot
                name="content"
                :is-open="isOpen"
                :isOpen="isOpen"
            ></slot>
        </div>
    </script>

    <script type="module">
        app.component('v-accordion', {
            template: '#v-accordion-template',

            props: {
                isActive: {
                    type: [Boolean, String],
                    default: false,
                },
            },

            emits: ['toggle'],

            data() {
                return {
                    isOpen: String(this.isActive) === 'true' || this.isActive === true,
                };
            },

            methods: {
                toggle() {
                    this.isOpen = ! this.isOpen;

                    this.$emit('toggle', { isActive: this.isOpen });
                },
            },
        });
    </script>
@endPushOnce
