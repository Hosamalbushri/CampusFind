<x-web::layouts>
    <x-web::container class="max-w-4xl py-10 sm:py-12">
        <!-- Breadcrumbs Navigation -->
        <x-web::breadcrumbs
            :items="[trans('campusfind_web_web::app.web.reports.report_lost_title') => '']"
            class="mb-8"
        />

        <!-- Flash Session Alerts -->
        <x-web::flash-group class="mb-8" />

        <!-- Header Section -->
        <div class="mb-10 text-start">
            <div class="mb-3">
                <x-web::badge variant="mint" size="md">
                    @lang('campusfind_web_web::app.web.reports.report_lost')
                </x-web::badge>
            </div>
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">
                @lang('campusfind_web_web::app.web.reports.report_lost_title')
            </h1>
            <p class="mt-2 text-base text-slate-600 dark:text-slate-400 leading-relaxed max-w-2xl">
                @lang('campusfind_web_web::app.web.reports.report_lost_subtitle')
            </p>
        </div>

        @if (! $isStudent)
            <!-- Guest Student Login Prompt -->
            <x-web::card variant="mint" padding="lg" class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                    <div class="flex items-start gap-4">
                        <div class="h-12 w-12 rounded-2xl bg-white dark:bg-slate-900 text-[#185c54] dark:text-[#a3e4c8] flex items-center justify-center shrink-0 shadow-xs">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-[#185c54] dark:text-[#a3e4c8]">
                                @lang('campusfind_web_web::app.web.reports.login_required_title')
                            </h3>
                            <p class="mt-1 text-sm text-[#185c54]/80 dark:text-emerald-300/80 leading-relaxed">
                                @lang('campusfind_web_web::app.web.reports.login_required_desc')
                            </p>
                        </div>
                    </div>

                    <x-web::button
                        href="{{ route('campusfind_web.web.login') }}"
                        variant="primary"
                        size="md"
                        class="shrink-0"
                    >
                        @lang('campusfind_web_web::app.web.reports.login_button')
                    </x-web::button>
                </div>
            </x-web::card>
        @endif

        <!-- Lost Item Form Card -->
        <x-web::card variant="elevated" padding="lg">
            <v-lost-report-form :is-student="{{ $isStudent ? 'true' : 'false' }}">
                <div class="space-y-6 animate-pulse">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="h-10 bg-slate-200 dark:bg-slate-700 rounded-xl"></div>
                        <div class="h-10 bg-slate-200 dark:bg-slate-700 rounded-xl"></div>
                    </div>
                    <div class="h-24 bg-slate-200 dark:bg-slate-700 rounded-xl"></div>
                    <div class="h-10 bg-[#185c54]/20 rounded-xl w-40"></div>
                </div>
            </v-lost-report-form>
        </x-web::card>
    </x-web::container>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-lost-report-form-template"
        >
            <x-web::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
            >
                <form
                    novalidate
                    @submit="handleSubmit($event, store)"
                    ref="lostReportForm"
                    class="space-y-6"
                >
                    <!-- Row 1: Category & Title -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Category -->
                        <x-web::form.control-group>
                            <x-web::form.control-group.label for="category_id" :required="true">
                                @lang('campusfind_web_web::app.web.reports.category')
                            </x-web::form.control-group.label>

                            <x-web::form.control-group.control
                                type="select"
                                name="category_id"
                                id="category_id"
                                rules="required"
                                :label="trans('campusfind_web_web::app.web.reports.category')"
                                :required="true"
                            >
                                <option value="">@lang('campusfind_web_web::app.web.reports.select_category')</option>
                                @foreach ($categories as $cat)
                                    @php
                                        $catId = is_object($cat) ? ($cat->id ?? '') : ($cat['id'] ?? '');
                                        $catName = is_object($cat) ? ($cat->name ?? $cat->code ?? '') : ($cat['name'] ?? $cat['code'] ?? '');
                                        if (empty($catName) && is_object($cat) && isset($cat->code)) {
                                            $catName = $cat->code;
                                        }
                                    @endphp
                                    <option value="{{ $catId }}">
                                        {{ ucwords(str_replace(['_', '-'], ' ', (string) $catName)) }}
                                    </option>
                                @endforeach
                            </x-web::form.control-group.control>

                            <x-web::form.control-group.error name="category_id" />
                        </x-web::form.control-group>

                        <!-- Title -->
                        <x-web::form.control-group>
                            <x-web::form.control-group.label for="title" :required="true">
                                @lang('campusfind_web_web::app.web.reports.title')
                            </x-web::form.control-group.label>

                            <x-web::form.control-group.control
                                type="text"
                                name="title"
                                id="title"
                                rules="required|max:160"
                                :label="trans('campusfind_web_web::app.web.reports.title')"
                                :required="true"
                                placeholder="{{ trans('campusfind_web_web::app.web.reports.title_lost_placeholder') }}"
                            />

                            <x-web::form.control-group.error name="title" />
                        </x-web::form.control-group>
                    </div>

                    <!-- Row 2: Location & Date -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Location -->
                        <x-web::form.control-group>
                            <x-web::form.control-group.label for="lost_location">
                                @lang('campusfind_web_web::app.web.reports.lost_location')
                            </x-web::form.control-group.label>

                            <x-web::form.control-group.control
                                type="text"
                                name="lost_location"
                                id="lost_location"
                                rules="max:255"
                                :label="trans('campusfind_web_web::app.web.reports.lost_location')"
                                placeholder="{{ trans('campusfind_web_web::app.web.reports.lost_location_placeholder') }}"
                            />

                            <x-web::form.control-group.error name="lost_location" />
                        </x-web::form.control-group>

                        <!-- Date & Time -->
                        <x-web::form.control-group>
                            <x-web::form.control-group.label for="lost_at">
                                @lang('campusfind_web_web::app.web.reports.lost_date')
                            </x-web::form.control-group.label>

                            <x-web::form.control-group.control
                                type="datetime-local"
                                name="lost_at"
                                id="lost_at"
                                :label="trans('campusfind_web_web::app.web.reports.lost_date')"
                            />

                            <x-web::form.control-group.error name="lost_at" />
                        </x-web::form.control-group>
                    </div>

                    <!-- Public Description -->
                    <x-web::form.control-group>
                        <x-web::form.control-group.label for="public_description">
                            @lang('campusfind_web_web::app.web.reports.public_description')
                        </x-web::form.control-group.label>

                        <x-web::form.control-group.control
                            type="textarea"
                            name="public_description"
                            id="public_description"
                            rows="3"
                            :label="trans('campusfind_web_web::app.web.reports.public_description')"
                            placeholder="{{ trans('campusfind_web_web::app.web.reports.public_description_placeholder') }}"
                        />

                        <x-web::form.control-group.error name="public_description" />
                    </x-web::form.control-group>

                    <!-- Private Distinctive Details -->
                    <x-web::form.control-group>
                        <div class="flex items-center justify-between mb-1.5">
                            <x-web::form.control-group.label for="private_description">
                                @lang('campusfind_web_web::app.web.reports.private_description')
                            </x-web::form.control-group.label>
                            <span class="text-xs text-emerald-700 dark:text-emerald-400 font-bold flex items-center gap-1">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span>@lang('campusfind_web_web::app.web.hero.trust_verify')</span>
                            </span>
                        </div>

                        <x-web::form.control-group.control
                            type="textarea"
                            name="private_description"
                            id="private_description"
                            rows="3"
                            :label="trans('campusfind_web_web::app.web.reports.private_description')"
                            placeholder="{{ trans('campusfind_web_web::app.web.reports.private_description_placeholder') }}"
                        />

                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 flex items-center gap-1.5">
                            <svg class="h-4 w-4 text-[#185c54] dark:text-[#a3e4c8] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>@lang('campusfind_web_web::app.web.reports.private_description_hint')</span>
                        </p>

                        <x-web::form.control-group.error name="private_description" />
                    </x-web::form.control-group>

                    <!-- Photo Upload -->
                    <x-web::form.control-group>
                        <x-web::form.control-group.label for="image">
                            @lang('campusfind_web_web::app.web.reports.item_image')
                        </x-web::form.control-group.label>

                        <x-web::form.control-group.control
                            type="file"
                            name="image"
                            id="image"
                            accept="image/jpeg,image/png,image/webp"
                        />

                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            @lang('campusfind_web_web::app.web.reports.image_hint')
                        </p>

                        <x-web::form.control-group.error name="image" />
                    </x-web::form.control-group>

                    <!-- Actions -->
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <x-web::button
                            type="submit"
                            variant="primary"
                            size="xl"
                            class="w-full sm:w-auto shadow-md"
                            ::disabled="! isStudent"
                            ::loading="isProcessing"
                        >
                            @lang('campusfind_web_web::app.web.reports.submit_lost_report')
                        </x-web::button>

                        <x-web::button
                            href="{{ route('campusfind_web.web.items.index') }}"
                            variant="ghost"
                            size="md"
                        >
                            @lang('campusfind_web_web::app.web.back_to_items')
                        </x-web::button>
                    </div>
                </form>
            </x-web::form>
        </script>

        <script type="module">
            app.component('v-lost-report-form', {
                template: '#v-lost-report-form-template',

                props: {
                    isStudent: {
                        type: Boolean,
                        default: false,
                    },
                },

                data() {
                    return {
                        isProcessing: false,
                    };
                },

                methods: {
                    store(params, { resetForm, setErrors }) {
                        if (! this.isStudent) {
                            window.location.href = "{{ route('campusfind_web.web.login') }}";
                            return;
                        }

                        this.isProcessing = true;
                        const formData = new FormData(this.$refs.lostReportForm);

                        this.$axios.post("{{ route('campusfind_web.web.reports.lost.store') }}", formData)
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
</x-web::layouts>
