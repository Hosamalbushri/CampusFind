<x-web::layouts>
    <x-web::section class="py-10">
        <!-- Back Link using Button Component -->
        <div class="mb-8">
            <x-web::button
                href="{{ route('campusfind_web.web.items.index') }}"
                variant="ghost"
                size="sm"
                class="inline-flex items-center gap-2 text-slate-600 hover:text-[#185c54] dark:text-slate-400 dark:hover:text-[#a3e4c8] -ms-3"
            >
                <svg class="h-4 w-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
                <span>@lang('campusfind_web_web::app.web.back_to_items')</span>
            </x-web::button>
        </div>

        <!-- Flash Session Alerts -->
        <x-web::flash-group class="mb-8" />

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12">
            <!-- Images Column -->
            <div class="lg:col-span-6 space-y-4">
                <div class="relative h-96 w-full rounded-3xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-800 overflow-hidden flex items-center justify-center shadow-xs">
                    @if ($item->hasImage && $item->imageUrl)
                        <img src="{{ $item->imageUrl }}" alt="{{ $item->title }}" class="h-full w-full object-contain p-4">
                    @else
                        <div class="text-slate-400 dark:text-slate-600 flex flex-col items-center">
                            <svg class="h-16 w-16 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm mt-2 font-medium">@lang('campusfind_web_web::app.web.images')</span>
                        </div>
                    @endif
                </div>

                @if (! empty($item->additionalImages))
                    <div class="grid grid-cols-4 gap-3">
                        @foreach ($item->additionalImages as $additionalUrl)
                            <div class="h-24 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-800 overflow-hidden">
                                <img src="{{ $additionalUrl }}" alt="Additional image" class="h-full w-full object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Details Column -->
            <div class="lg:col-span-6 flex flex-col justify-between">
                <div>
                    <!-- Badges -->
                    <div class="flex flex-wrap items-center gap-2.5 mb-4">
                        @if ($item->category)
                            <x-web::badge variant="mint" size="md">
                                {{ $item->category }}
                            </x-web::badge>
                        @endif

                        <x-web::badge variant="neutral" size="md" class="font-mono">
                            {{ $item->reference }}
                        </x-web::badge>
                    </div>

                    <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">
                        {{ $item->title }}
                    </h1>

                    @if ($item->description)
                        <div class="mt-4 text-base text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                            {{ $item->description }}
                        </div>
                    @endif

                    <!-- Details Box using Card Component -->
                    <x-web::card variant="flat" padding="md" class="mt-8 space-y-3.5">
                        @if ($item->foundLocation)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-500 dark:text-slate-400 font-medium">@lang('campusfind_web_web::app.web.found_location'):</span>
                                <span class="font-bold text-slate-900 dark:text-white">{{ $item->foundLocation }}</span>
                            </div>
                        @endif

                        @if ($item->foundAt)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-500 dark:text-slate-400 font-medium">@lang('campusfind_web_web::app.web.found_date'):</span>
                                <span class="font-bold text-slate-900 dark:text-white">{{ $item->foundAt->format('Y-m-d H:i') }}</span>
                            </div>
                        @endif

                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-500 dark:text-slate-400 font-medium">@lang('campusfind_web_web::app.web.reference'):</span>
                            <span class="font-mono font-bold text-slate-900 dark:text-white">{{ $item->reference }}</span>
                        </div>
                    </x-web::card>
                </div>

                <!-- Claim Action Box using Card & Button Components -->
                <x-web::card variant="mint" padding="lg" class="mt-8">
                    @if ($existingClaim)
                        <div class="space-y-4">
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                                <h3 class="text-xl font-extrabold text-[#185c54] dark:text-[#a3e4c8] tracking-tight">
                                    @lang('campusfind_web_web::app.web.claims.already_claimed')
                                </h3>
                            </div>
                            <p class="text-sm text-[#185c54]/80 dark:text-emerald-300/80 leading-relaxed">
                                @lang('campusfind_web_web::app.web.claims.already_claimed_desc')
                            </p>
                            <div class="flex items-center gap-3 pt-2">
                                <x-web::badge variant="warning" size="md">
                                    {{ is_object($existingClaim->status) ? $existingClaim->status->value : $existingClaim->status }}
                                </x-web::badge>
                                <x-web::button
                                    href="{{ route('campusfind_web.web.account.dashboard') }}"
                                    variant="secondary"
                                    size="sm"
                                >
                                    @lang('campusfind_web_web::app.web.auth.dashboard')
                                </x-web::button>
                            </div>
                        </div>
                    @elseif ($isStudent)
                        <div>
                            <h3 class="text-xl font-extrabold text-[#185c54] dark:text-[#a3e4c8] tracking-tight">
                                @lang('campusfind_web_web::app.web.claim_item')
                            </h3>
                            <p class="mt-1 text-sm text-[#185c54]/80 dark:text-emerald-300/80 leading-relaxed">
                                @lang('campusfind_web_web::app.web.claims.statement_help')
                            </p>

                            <v-claim-form
                                action-url="{{ route('campusfind_web.web.items.claim', $item->reference) }}"
                            >
                                <div class="mt-5 space-y-4 animate-pulse">
                                    <div class="h-24 bg-slate-200 dark:bg-slate-700 rounded-xl"></div>
                                    <div class="h-10 bg-[#185c54]/20 rounded-xl"></div>
                                </div>
                            </v-claim-form>
                        </div>
                    @else
                        <div>
                            <h3 class="text-xl font-extrabold text-[#185c54] dark:text-[#a3e4c8] tracking-tight">
                                @lang('campusfind_web_web::app.web.claim_item')
                            </h3>
                            <p class="mt-2 text-sm text-[#185c54]/80 dark:text-emerald-300/80 leading-relaxed">
                                @lang('campusfind_web_web::app.web.claims.login_prompt')
                            </p>

                            <div class="mt-6">
                                <x-web::button
                                    href="{{ route('campusfind_web.web.login') }}"
                                    variant="primary"
                                    size="lg"
                                    class="w-full sm:w-auto shadow-sm"
                                >
                                    @lang('campusfind_web_web::app.web.auth.login')
                                </x-web::button>
                            </div>
                        </div>
                    @endif
                </x-web::card>
            </div>
        </div>
    </x-web::section>

    @if ($isStudent && ! $existingClaim)
        @pushOnce('scripts')
            <script
                type="text/x-template"
                id="v-claim-form-template"
            >
                <x-web::form
                    v-slot="{ meta, errors, handleSubmit }"
                    as="div"
                >
                    <form
                        novalidate
                        @submit="handleSubmit($event, storeClaim)"
                        ref="claimForm"
                        class="mt-5 space-y-4"
                    >
                        <x-web::form.control-group>
                            <x-web::form.control-group.label for="statement" :required="true">
                                @lang('campusfind_web_web::app.web.claims.statement_label')
                            </x-web::form.control-group.label>

                            <x-web::form.control-group.control
                                type="textarea"
                                name="statement"
                                id="statement"
                                rows="4"
                                rules="required|max:2000"
                                :label="trans('campusfind_web_web::app.web.claims.statement_label')"
                                :required="true"
                                placeholder="{{ trans('campusfind_web_web::app.web.claims.statement_placeholder') }}"
                            />

                            <x-web::form.control-group.error name="statement" />
                        </x-web::form.control-group>

                        <x-web::form.control-group>
                            <x-web::form.control-group.label for="claim_image">
                                @lang('campusfind_web_web::app.web.claims.proof_image_label')
                            </x-web::form.control-group.label>

                            <x-web::form.control-group.control
                                type="file"
                                name="image"
                                id="claim_image"
                                accept="image/jpeg,image/png,image/webp"
                            />

                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                @lang('campusfind_web_web::app.web.reports.image_hint')
                            </p>

                            <x-web::form.control-group.error name="image" />
                        </x-web::form.control-group>

                        <div class="pt-2">
                            <x-web::button
                                type="submit"
                                variant="primary"
                                size="md"
                                class="w-full shadow-sm"
                                ::loading="isProcessing"
                            >
                                @lang('campusfind_web_web::app.web.claims.submit_claim_button')
                            </x-web::button>
                        </div>
                    </form>
                </x-web::form>
            </script>

            <script type="module">
                app.component('v-claim-form', {
                    template: '#v-claim-form-template',

                    props: {
                        actionUrl: {
                            type: String,
                            required: true,
                        },
                    },

                    data() {
                        return {
                            isProcessing: false,
                        };
                    },

                    methods: {
                        storeClaim(params, { resetForm, setErrors }) {
                            this.isProcessing = true;
                            const formData = new FormData(this.$refs.claimForm);

                            this.$axios.post(this.actionUrl, formData)
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
    @endif
</x-web::layouts>
