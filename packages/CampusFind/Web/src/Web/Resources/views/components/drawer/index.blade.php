@props([
    'isActive' => false,
    'position' => 'right',
    'width'    => '450px',
    'id'       => null,
    'title'    => null,
])

<v-drawer
    is-active="{{ filter_var($isActive, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false' }}"
    position="{{ $position }}"
    width="{{ $width }}"
    @if ($id) id="{{ $id }}" @endif
    {{ $attributes }}
>
    @isset($toggle)
        <template v-slot:toggle>
            {{ $toggle }}
        </template>
    @endisset

    @isset($header)
        <template v-slot:header="{ close }">
            <div {{ $header->attributes->merge(['class' => 'flex items-center justify-between p-5 border-b border-slate-100 dark:border-slate-800']) }}>
                <div class="flex-1">
                    {{ $header }}
                </div>

                <button
                    type="button"
                    class="rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-white cursor-pointer bg-transparent border-0 focus:outline-none transition-colors"
                    @click="close"
                    aria-label="{{ trans('campusfind_web_web::app.web.accessibility.close_modal') }}"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </template>
    @elseif($title)
        <template v-slot:header="{ close }">
            <div class="flex items-center justify-between p-5 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-lg font-extrabold text-slate-900 dark:text-white tracking-tight">
                    {{ $title }}
                </h3>

                <button
                    type="button"
                    class="rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-white cursor-pointer bg-transparent border-0 focus:outline-none transition-colors"
                    @click="close"
                    aria-label="{{ trans('campusfind_web_web::app.web.accessibility.close_modal') }}"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </template>
    @endisset

    @isset($content)
        <template v-slot:content>
            <div {{ $content->attributes->merge(['class' => 'flex-1 overflow-y-auto p-5 space-y-4']) }}>
                {{ $content }}
            </div>
        </template>
    @elseif(!empty((string) $slot))
        <template v-slot:content>
            <div class="flex-1 overflow-y-auto p-5 space-y-4">
                {{ $slot }}
            </div>
        </template>
    @endisset

    @isset($footer)
        <template v-slot:footer>
            <div {{ $footer->attributes->merge(['class' => 'p-5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-end gap-3']) }}>
                {{ $footer }}
            </div>
        </template>
    @endisset
</v-drawer>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-drawer-template"
    >
        <div class="font-cairo">
            <!-- Toggle Trigger -->
            <div
                class="cursor-pointer select-none"
                @click="open"
            >
                <slot name="toggle"></slot>
            </div>

            <!-- Overlay Backdrop -->
            <transition
                tag="div"
                name="drawer-overlay"
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

            <!-- Drawer Content Panel -->
            <transition
                tag="div"
                name="drawer-panel"
                enter-active-class="transform transition duration-300 ease-out"
                :enter-from-class="enterClass"
                enter-to-class="translate-x-0"
                leave-active-class="transform transition duration-200 ease-in"
                leave-from-class="translate-x-0"
                :leave-to-class="enterClass"
            >
                <div
                    v-if="isOpen"
                    class="fixed z-[10003] top-0 bottom-0 flex flex-col bg-white dark:bg-slate-900 shadow-2xl border-slate-200 dark:border-slate-800 max-w-[calc(100%-1.5rem)]"
                    :class="[
                        position === 'left' ? 'left-0 border-r' : 'right-0 border-l'
                    ]"
                    :style="{ width: width }"
                    role="dialog"
                    aria-modal="true"
                >
                    <slot name="header" :close="close"></slot>
                    <slot name="content"></slot>
                    <slot name="footer"></slot>
                </div>
            </transition>
        </div>
    </script>

    <script type="module">
        app.component('v-drawer', {
            template: '#v-drawer-template',

            props: {
                isActive: {
                    type: [Boolean, String],
                    default: false,
                },
                position: {
                    type: String,
                    default: 'right',
                },
                width: {
                    type: String,
                    default: '450px',
                },
            },

            emits: ['open', 'close', 'toggle'],

            data() {
                return {
                    isOpen: String(this.isActive) === 'true' || this.isActive === true,
                };
            },

            computed: {
                enterClass() {
                    return this.position === 'left' ? '-translate-x-full' : 'translate-x-full';
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

                open() {
                    this.isOpen = true;
                    document.body.style.overflow = 'hidden';
                    this.$emit('open');
                },

                close() {
                    this.isOpen = false;
                    document.body.style.overflow = 'auto';
                    this.$emit('close');
                },

                toggle() {
                    if (this.isOpen) {
                        this.close();
                    } else {
                        this.open();
                    }
                },
            },
        });
    </script>
@endPushOnce
