<x-web::layouts.anonymous>
    <x-slot:title>
        @lang('campusfind_web_web::app.web.auth.login_title')
    </x-slot>

    <x-web::container size="sm" class="min-h-[75vh] flex items-center justify-center py-12">
        <x-web::card variant="elevated" padding="lg" class="w-full max-w-md">
            <!-- Header -->
            <div class="text-center">
                <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-[#e6f4ee] text-[#185c54] dark:bg-[#185c54]/30 dark:text-[#a3e4c8] mb-4 shadow-sm ring-4 ring-[#e6f4ee]/60 dark:ring-[#185c54]/10">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                    </svg>
                </div>

                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    @lang('campusfind_web_web::app.web.auth.login_title')
                </h2>

                <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                    @lang('campusfind_web_web::app.web.auth.login_subtitle')
                </p>
            </div>

            <!-- Flash Session Alerts -->
            <x-web::flash-group class="mt-6" />

            <!-- Vue Login Form Component -->
            <v-login-form>
                <div class="mt-8 space-y-5 animate-pulse">
                    <div class="h-10 bg-slate-200 dark:bg-slate-700 rounded-xl"></div>
                    <div class="h-10 bg-slate-200 dark:bg-slate-700 rounded-xl"></div>
                    <div class="h-10 bg-[#185c54]/20 rounded-xl"></div>
                </div>
            </v-login-form>
        </x-web::card>
    </x-web::container>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-login-form-template"
        >
            <x-web::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
            >
                <form
                    @submit="handleSubmit($event, login)"
                    ref="loginForm"
                    class="mt-8 space-y-5"
                >
                    <!-- University Card Number -->
                    <x-web::form.control-group>
                        <x-web::form.control-group.label for="university_card_number" :required="true">
                            @lang('campusfind_web_web::app.web.auth.card_number')
                        </x-web::form.control-group.label>

                        <div class="relative rounded-xl shadow-xs">
                            <div class="absolute inset-y-0 ltr:left-0 rtl:right-0 ltr:pl-3.5 rtl:pr-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                </svg>
                            </div>

                            <x-web::form.control-group.control
                                type="text"
                                name="university_card_number"
                                id="university_card_number"
                                rules="required"
                                :label="trans('campusfind_web_web::app.web.auth.card_number')"
                                autocomplete="username"
                                placeholder="{{ trans('campusfind_web_web::app.web.auth.card_number_placeholder') }}"
                                class="ltr:pl-10 rtl:pr-10"
                            />
                        </div>

                        <x-web::form.control-group.error name="university_card_number" />
                    </x-web::form.control-group>

                    <!-- Password -->
                    <x-web::form.control-group>
                        <x-web::form.control-group.label for="password" :required="true">
                            @lang('campusfind_web_web::app.web.auth.password')
                        </x-web::form.control-group.label>

                        <div class="relative rounded-xl shadow-xs">
                            <div class="absolute inset-y-0 ltr:left-0 rtl:right-0 ltr:pl-3.5 rtl:pr-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>

                            <x-web::form.control-group.control
                                type="password"
                                name="password"
                                id="password"
                                rules="required"
                                :label="trans('campusfind_web_web::app.web.auth.password')"
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="ltr:pl-10 rtl:pr-10"
                            />
                        </div>

                        <x-web::form.control-group.error name="password" />
                    </x-web::form.control-group>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <x-web::form.control-group.control
                            type="checkbox"
                            name="remember"
                            id="remember"
                        >
                            @lang('campusfind_web_web::app.web.auth.remember')
                        </x-web::form.control-group.control>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <x-web::button
                            type="submit"
                            variant="primary"
                            size="xl"
                            class="w-full shadow-md"
                            ::loading="isProcessing"
                        >
                            @lang('campusfind_web_web::app.web.auth.submit')
                        </x-web::button>
                    </div>

                    <!-- Back to Home -->
                    <div class="text-center pt-2">
                        <x-web::button
                            href="{{ route('campusfind_web.web.home') }}"
                            variant="ghost"
                            size="sm"
                            class="text-xs text-slate-500 hover:text-[#185c54] dark:text-slate-400 dark:hover:text-[#a3e4c8]"
                        >
                            <span class="rtl:rotate-180 inline-block">&larr;</span>
                            <span>@lang('campusfind_web_web::app.web.auth.back_to_portal')</span>
                        </x-web::button>
                    </div>
                </form>
            </x-web::form>
        </script>

        <script type="module">
            app.component('v-login-form', {
                template: '#v-login-form-template',

                data() {
                    return {
                        isProcessing: false,
                    };
                },

                methods: {
                    login(params, { resetForm, setErrors }) {
                        this.isProcessing = true;

                        const formData = new FormData(this.$refs.loginForm);

                        this.$axios.post("{{ route('campusfind_web.web.login.store') }}", formData)
                            .then((response) => {
                                this.isProcessing = false;

                                if (response.data.message) {
                                    this.$emitter.emit('add-flash', {
                                        type: 'success',
                                        message: response.data.message
                                    });
                                }

                                if (response.data.redirect_url) {
                                    window.location.href = response.data.redirect_url;
                                }
                            })
                            .catch((error) => {
                                this.isProcessing = false;

                                if (error.response && error.response.status === 422) {
                                    setErrors(error.response.data.errors);
                                } else if (error.response && error.response.data && error.response.data.message) {
                                    this.$emitter.emit('add-flash', {
                                        type: 'error',
                                        message: error.response.data.message
                                    });
                                }
                            });
                    },
                },
            });
        </script>
    @endPushOnce
</x-web::layouts.anonymous>
