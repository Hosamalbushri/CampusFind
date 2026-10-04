<v-modal-confirm ref="confirmModal"></v-modal-confirm>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-modal-confirm-template"
    >
        <div>
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
                    class="fixed inset-0 z-[10003] bg-slate-950/60 backdrop-blur-sm transition-opacity"
                    v-show="isOpen"
                    @click="disagree"
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
                    class="fixed inset-0 z-[10004] transform overflow-y-auto transition font-cairo"
                    v-if="isOpen"
                    role="alertdialog"
                    aria-modal="true"
                    tabindex="-1"
                >
                    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-6">
                        <div class="relative w-full max-w-md transform overflow-hidden rounded-3xl bg-white p-6 sm:p-8 text-start shadow-2xl transition-all dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800">
                            <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-800 pb-4 mb-4">
                                <div class="h-10 w-10 rounded-xl bg-rose-50 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400 flex items-center justify-center shrink-0">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                                    @{{ title }}
                                </h3>
                            </div>

                            <div class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed mb-6">
                                @{{ message }}
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                                <button
                                    type="button"
                                    class="cursor-pointer rounded-xl px-4 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors border-0 bg-transparent focus:outline-none focus:ring-2 focus:ring-slate-300"
                                    @click="disagree"
                                >
                                    @{{ options.btnDisagree }}
                                </button>

                                <button
                                    type="button"
                                    class="cursor-pointer rounded-xl px-4 py-2.5 text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-sm transition-colors border-0 focus:outline-none focus:ring-4 focus:ring-rose-500/20"
                                    @click="agree"
                                >
                                    @{{ options.btnAgree }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </script>

    <script type="module">
        app.component('v-modal-confirm', {
            template: '#v-modal-confirm-template',

            data() {
                return {
                    isOpen: false,

                    title: '',

                    message: '',

                    options: {
                        btnDisagree: '',
                        btnAgree: '',
                    },

                    agreeCallback: null,

                    disagreeCallback: null,
                };
            },

            created() {
                this.registerGlobalEvents();
            },

            methods: {
                open({
                    title = "@lang('campusfind_web_web::app.web.confirm')",
                    message = "@lang('campusfind_web_web::app.web.confirm_action')",
                    options = {
                        btnDisagree: "@lang('campusfind_web_web::app.web.cancel')",
                        btnAgree: "@lang('campusfind_web_web::app.web.confirm')",
                    },
                    agree = () => {},
                    disagree = () => {},
                }) {
                    this.isOpen = true;

                    document.body.style.overflow = 'hidden';

                    this.title = title;

                    this.message = message;

                    this.options = options;

                    this.agreeCallback = agree;

                    this.disagreeCallback = disagree;
                },

                disagree() {
                    this.isOpen = false;

                    document.body.style.overflow = 'auto';

                    if (typeof this.disagreeCallback === 'function') {
                        this.disagreeCallback();
                    }
                },

                agree() {
                    this.isOpen = false;

                    document.body.style.overflow = 'auto';

                    if (typeof this.agreeCallback === 'function') {
                        this.agreeCallback();
                    }
                },

                registerGlobalEvents() {
                    if (this.$emitter) {
                        this.$emitter.on('open-confirm-modal', this.open);
                    }
                },
            },
        });
    </script>
@endPushOnce
