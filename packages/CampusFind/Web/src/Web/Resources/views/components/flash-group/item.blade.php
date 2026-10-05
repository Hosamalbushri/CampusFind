<v-flash-item
    v-for="flash in flashes"
    :key="flash.uid"
    :flash="flash"
    @onRemove="remove($event)"
>
</v-flash-item>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-flash-item-template"
    >
        <div
            class="pointer-events-auto flex w-full max-w-full sm:max-w-md items-start justify-between gap-3.5 rounded-2xl border p-4 shadow-xl backdrop-blur-md transition-all font-cairo bg-white/95 dark:bg-slate-900/95"
            :class="variantClasses[flash.type] || variantClasses.info"
            @mouseenter="pauseTimer"
            @mouseleave="resumeTimer"
            role="alert"
        >
            <!-- SVG Icon -->
            <div
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl shadow-2xs"
                :class="iconBgClasses[flash.type] || iconBgClasses.info"
            >
                <svg v-if="flash.type === 'success'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <svg v-else-if="flash.type === 'error' || flash.type === 'danger'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <svg v-else-if="flash.type === 'warning'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <div class="flex-1 min-w-0 pt-0.5">
                <p class="text-sm font-extrabold text-slate-900 dark:text-white">
                    @{{ typeHeadings[flash.type] || typeHeadings.info }}
                </p>

                <p class="text-xs font-semibold text-slate-600 dark:text-slate-300 mt-1 leading-relaxed break-words">
                    @{{ flash.message }}
                </p>
            </div>

            <!-- Dismiss Button with Animated Circular Timer -->
            <button
                type="button"
                class="relative shrink-0 p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer bg-transparent border-0"
                @click="remove"
                aria-label="{{ trans('campusfind_web_web::app.web.accessibility.dismiss_alert') }}"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12" />
                </svg>

                <svg class="absolute inset-0 h-full w-full -rotate-90 pointer-events-none" viewBox="0 0 24 24">
                    <circle
                        class="text-slate-200/50 dark:text-slate-700/50"
                        stroke-width="1.5"
                        stroke="currentColor"
                        fill="transparent"
                        r="10"
                        cx="12"
                        cy="12"
                    />
                    <circle
                        class="text-[#185c54] dark:text-[#a3e4c8] transition-all duration-100 ease-out"
                        stroke-width="1.5"
                        :stroke-dasharray="circumference"
                        :stroke-dashoffset="strokeDashoffset"
                        stroke-linecap="round"
                        stroke="currentColor"
                        fill="transparent"
                        r="10"
                        cx="12"
                        cy="12"
                    />
                </svg>
            </button>
        </div>
    </script>

    <script type="module">
        app.component('v-flash-item', {
            template: '#v-flash-item-template',

            props: ['flash'],

            data() {
                return {
                    variantClasses: {
                        success: 'border-emerald-300/80 dark:border-emerald-700/60 shadow-emerald-500/10',
                        error: 'border-rose-300/80 dark:border-rose-700/60 shadow-rose-500/10',
                        danger: 'border-rose-300/80 dark:border-rose-700/60 shadow-rose-500/10',
                        warning: 'border-amber-300/80 dark:border-amber-700/60 shadow-amber-500/10',
                        info: 'border-sky-300/80 dark:border-sky-700/60 shadow-sky-500/10',
                    },

                    iconBgClasses: {
                        success: 'bg-emerald-100/80 text-emerald-700 dark:bg-emerald-950/70 dark:text-emerald-300',
                        error: 'bg-rose-100/80 text-rose-700 dark:bg-rose-950/70 dark:text-rose-300',
                        danger: 'bg-rose-100/80 text-rose-700 dark:bg-rose-950/70 dark:text-rose-300',
                        warning: 'bg-amber-100/80 text-amber-700 dark:bg-amber-950/70 dark:text-amber-300',
                        info: 'bg-sky-100/80 text-sky-700 dark:bg-sky-950/70 dark:text-sky-300',
                    },

                    typeHeadings: {
                        success: "@lang('campusfind_web_web::app.web.success')",
                        error: "@lang('campusfind_web_web::app.web.error')",
                        danger: "@lang('campusfind_web_web::app.web.error')",
                        warning: "@lang('campusfind_web_web::app.web.warning')",
                        info: "@lang('campusfind_web_web::app.web.info')",
                    },

                    duration: 5000,
                    progress: 0,
                    circumference: 2 * Math.PI * 10,
                    timer: null,
                    isPaused: false,
                    remainingTime: 5000,
                };
            },

            computed: {
                strokeDashoffset() {
                    return this.circumference - (this.progress / 100) * this.circumference;
                },
            },

            created() {
                this.startTimer();
            },

            beforeUnmount() {
                this.stopTimer();
            },

            methods: {
                remove() {
                    this.$emit('onRemove', this.flash);
                },

                startTimer() {
                    const interval = 100;
                    const step = (100 / (this.duration / interval));

                    this.timer = setInterval(() => {
                        if (! this.isPaused) {
                            this.progress += step;
                            this.remainingTime -= interval;

                            if (this.progress >= 100) {
                                this.stopTimer();
                                this.remove();
                            }
                        }
                    }, interval);
                },

                stopTimer() {
                    if (this.timer) {
                        clearInterval(this.timer);
                    }
                },

                pauseTimer() {
                    this.isPaused = true;
                },

                resumeTimer() {
                    this.isPaused = false;
                },
            },
        });
    </script>
@endPushOnce
