@props([
    'isActive' => false,
    'position' => 'center',
    'size'     => 'md',
    'id'       => null,
    'title'    => null,
])

<v-modal
    is-active="{{ filter_var($isActive, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false' }}"
    position="{{ $position }}"
    size="{{ $size }}"
    @if ($id) id="{{ $id }}" @endif
    {{ $attributes }}
>
    @isset($toggle)
        <template v-slot:toggle>
            {{ $toggle }}
        </template>
    @endisset

    @isset($header)
        <template v-slot:header="{ toggle, isOpen }">
            <div {{ $header->attributes->merge(['class' => 'flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-6']) }}>
                <div class="flex-1">
                    {{ $header }}
                </div>

                <button
                    type="button"
                    class="rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-white cursor-pointer bg-transparent border-0 focus:outline-none focus:ring-4 focus:ring-[#185c54]/20 transition-colors"
                    @click="toggle"
                    aria-label="{{ trans('campusfind_web_web::app.web.accessibility.close_modal') }}"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </template>
    @elseif($title)
        <template v-slot:header="{ toggle, isOpen }">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-6">
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    {{ $title }}
                </h3>

                <button
                    type="button"
                    class="rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-white cursor-pointer bg-transparent border-0 focus:outline-none focus:ring-4 focus:ring-[#185c54]/20 transition-colors"
                    @click="toggle"
                    aria-label="{{ trans('campusfind_web_web::app.web.accessibility.close_modal') }}"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </template>
    @endisset

    @isset($content)
        <template v-slot:content>
            <div {{ $content->attributes->merge(['class' => 'space-y-4']) }}>
                {{ $content }}
            </div>
        </template>
    @elseif(!empty((string) $slot))
        <template v-slot:content>
            <div class="space-y-4">
                {{ $slot }}
            </div>
        </template>
    @endisset

    @isset($footer)
        <template v-slot:footer>
            <div {{ $footer->attributes->merge(['class' => 'mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3']) }}>
                {{ $footer }}
            </div>
        </template>
    @endisset
</v-modal>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-modal-template"
    >
        <div>
            <div
                class="cursor-pointer select-none"
                @click="toggle"
            >
                <slot name="toggle"></slot>
            </div>

            <transition
                tag="div"
                name="modal-overlay"
                enter-active-class="duration-300 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="duration-200 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    class="fixed inset-0 z-[10002] bg-slate-950/60 backdrop-blur-sm transition-opacity"
                    v-show="isOpen"
                    @click="close"
                ></div>
            </transition>

            <transition
                tag="div"
                name="modal-content"
                enter-active-class="duration-300 ease-out"
                enter-from-class="translate-y-4 opacity-0 sm:translate-y-0 sm:scale-95"
                enter-to-class="translate-y-0 opacity-100 sm:scale-100"
                leave-active-class="duration-200 ease-in"
                leave-from-class="translate-y-0 opacity-100 sm:scale-100"
                leave-to-class="translate-y-4 opacity-0 sm:translate-y-0 sm:scale-95"
            >
                <div
                    class="fixed inset-0 z-[10003] transform overflow-y-auto transition"
                    v-if="isOpen"
                    role="dialog"
                    aria-modal="true"
                    tabindex="-1"
                >
                    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-6">
                        <div
                            class="relative w-full transform overflow-hidden rounded-3xl bg-white p-6 sm:p-8 text-start shadow-2xl transition-all dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 font-cairo"
                            :class="sizeClass"
                        >
                            <!-- Header Slot -->
                            <slot
                                name="header"
                                :toggle="toggle"
                                :is-open="isOpen"
                                :isOpen="isOpen"
                            ></slot>

                            <!-- Content Slot -->
                            <slot name="content"></slot>

                            <!-- Footer Slot -->
                            <slot name="footer"></slot>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </script>

    <script type="module">
        app.component('v-modal', {
            template: '#v-modal-template',

            props: {
                isActive: {
                    type: [Boolean, String],
                    default: false,
                },
                position: {
                    type: String,
                    default: 'center',
                },
                size: {
                    type: String,
                    default: 'md',
                },
            },

            emits: ['toggle', 'open', 'close'],

            data() {
                return {
                    isOpen: String(this.isActive) === 'true' || this.isActive === true,
                };
            },

            computed: {
                sizeClass() {
                    return {
                        'sm': 'max-w-md',
                        'normal': 'max-w-lg',
                        'md': 'max-w-lg',
                        'lg': 'max-w-2xl',
                        'large': 'max-w-2xl',
                        'xl': 'max-w-4xl',
                    }[this.size] || 'max-w-lg';
                },
            },

            mounted() {
                window.addEventListener('keydown', this.handleKeyDown);
            },

            beforeUnmount() {
                window.removeEventListener('keydown', this.handleKeyDown);
            },

            methods: {
                handleKeyDown(e) {
                    if ((e.key === 'Escape' || e.key === 'Esc') && this.isOpen) {
                        this.close();
                    }
                },

                toggle() {
                    this.isOpen = ! this.isOpen;

                    document.body.style.overflow = this.isOpen ? 'hidden' : 'auto';

                    this.$emit('toggle', { isActive: this.isOpen });
                },

                open() {
                    this.isOpen = true;

                    document.body.style.overflow = 'hidden';

                    this.$emit('open', { isActive: this.isOpen });
                },

                close() {
                    this.isOpen = false;

                    document.body.style.overflow = 'auto';

                    this.$emit('close', { isActive: this.isOpen });
                },
            },
        });
    </script>
@endPushOnce
