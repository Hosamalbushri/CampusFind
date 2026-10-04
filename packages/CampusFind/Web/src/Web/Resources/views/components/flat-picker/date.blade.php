@props([
    'name'        => '',
    'value'       => null,
    'placeholder' => null,
])

<v-date-picker {{ $attributes }}>
    @if ($slot->isNotEmpty())
        {{ $slot }}
    @else
        <div class="relative w-full">
            <input
                type="date"
                name="{{ $name }}"
                value="{{ old($name, $value) }}"
                placeholder="{{ $placeholder }}"
                class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 px-4 py-3 text-sm text-slate-900 dark:text-white placeholder-slate-400 transition-all focus:outline-none focus:border-[#185c54] focus:ring-4 focus:ring-[#185c54]/15"
            />
            <div class="pointer-events-none absolute inset-y-0 ltr:right-3.5 rtl:left-3.5 flex items-center text-slate-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>
    @endif
</v-date-picker>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-date-picker-template"
    >
        <span class="relative inline-block w-full font-cairo">
            <slot></slot>
        </span>
    </script>

    <script type="module">
        app.component('v-date-picker', {
            template: '#v-date-picker-template',

            props: {
                name: String,
                value: String,
                allowInput: {
                    type: Boolean,
                    default: true,
                },
                disable: Array,
                minDate: String,
                maxDate: String,
            },

            emits: ['onChange'],

            data() {
                return {
                    datepicker: null,
                };
            },

            mounted() {
                const options = this.setOptions();
                this.activate(options);
            },

            methods: {
                setOptions() {
                    const self = this;

                    return {
                        allowInput: this.allowInput ?? true,
                        disable: this.disable ?? [],
                        minDate: this.minDate ?? '',
                        maxDate: this.maxDate ?? '',
                        altFormat: "Y-m-d",
                        dateFormat: "Y-m-d",
                        weekNumbers: true,

                        onChange(selectedDates, dateStr, instance) {
                            self.$emit("onChange", dateStr);
                        },
                    };
                },

                activate(options) {
                    const element = this.$el.getElementsByTagName("input")[0];

                    if (element && typeof window.Flatpickr === 'function') {
                        this.datepicker = new window.Flatpickr(element, options);
                    }
                },

                clear() {
                    if (this.datepicker) {
                        this.datepicker.clear();
                    }
                },
            },
        });
    </script>
@endPushOnce
