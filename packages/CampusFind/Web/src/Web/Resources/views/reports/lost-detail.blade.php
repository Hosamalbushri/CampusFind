<x-web::layouts>
    <x-web::section class="py-10">
        <!-- Flash Session Alerts -->
        <x-web::flash-group class="mb-8" />

        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_minmax(320px,440px)]">
            <x-web::card padding="lg">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <x-web::badge variant="mint">@lang('campusfind_web_web::app.web.browse.record_lost')</x-web::badge>
                    <span class="text-sm font-bold text-slate-500 dark:text-slate-400">{{ $report->reference }}</span>
                </div>
                @if ($report->hasImage && $report->imageUrl)
                    <div class="mt-5 relative h-72 w-full rounded-2xl bg-slate-100 dark:bg-slate-800 overflow-hidden border border-slate-200/80 dark:border-slate-700/80 flex items-center justify-center">
                        <img src="{{ $report->imageUrl }}" alt="{{ $report->title }}" class="h-full w-full object-contain p-2">
                    </div>
                @endif
                <h1 class="mt-5 text-3xl font-black text-slate-900 dark:text-white">{{ $report->title }}</h1>
                @if ($report->description)
                    <p class="mt-4 whitespace-pre-line text-slate-600 dark:text-slate-300">{{ $report->description }}</p>
                @endif
                <dl class="mt-6 grid gap-4 text-sm sm:grid-cols-2">
                    @if ($report->category)<div><dt class="font-bold">@lang('campusfind_web_web::app.web.browse.category_label')</dt><dd>{{ $report->category }}</dd></div>@endif
                    @if ($report->lostLocation)<div><dt class="font-bold">@lang('campusfind_web_web::app.web.browse.location_lost')</dt><dd>{{ $report->lostLocation }}</dd></div>@endif
                    @if ($report->lostAt)<div><dt class="font-bold">@lang('campusfind_web_web::app.web.browse.date_lost')</dt><dd>{{ $report->lostAt->format('Y-m-d') }}</dd></div>@endif
                </dl>
                <div class="mt-6 rounded-xl bg-slate-100 p-4 text-sm text-slate-600 dark:bg-slate-800 dark:text-slate-300" role="note">
                    @lang('campusfind_web_web::app.web.found_response.public_privacy_notice')
                </div>
            </x-web::card>

            <x-web::card padding="lg">
                <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">@lang('campusfind_web_web::app.web.found_response.title')</h2>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">@lang('campusfind_web_web::app.web.found_response.subtitle')</p>

                @if (! $student)
                    <x-web::alert variant="info" class="mt-6">
                        @lang('campusfind_web_web::app.web.found_response.login_required')
                    </x-web::alert>
                    <x-web::button class="mt-4 w-full" href="{{ route('campusfind_web.web.login') }}" variant="primary">@lang('campusfind_web_web::app.web.found_response.login_action')</x-web::button>
                @elseif ($isOwner)
                    <x-web::alert variant="warning" class="mt-6">
                        @lang('campusfind_web_web::app.web.found_response.self_response_policy_pending')
                    </x-web::alert>
                @elseif ($existingResponse)
                    <div class="mt-6 rounded-xl bg-slate-100 p-4 dark:bg-slate-800">
                        <p class="font-bold">@lang('campusfind_web_web::app.web.found_response.existing_response')</p>
                        <p class="mt-2 text-sm">@lang('campusfind_web_web::app.web.found_response.status_'.$existingResponse->status->value)</p>
                    </div>
                    @if (in_array($existingResponse->status->value, ['submitted', 'under_review'], true))
                        <v-cancel-response-form
                            action-url="{{ route('campusfind_web.web.lost-reports.responses.cancel', $report->reference) }}"
                            class="mt-4"
                        >
                            <x-web::button type="submit" variant="outline" class="w-full">
                                @lang('campusfind_web_web::app.web.found_response.cancel_response')
                            </x-web::button>
                        </v-cancel-response-form>
                    @endif
                @else
                    <v-found-response-form
                        action-url="{{ route('campusfind_web.web.lost-reports.responses.store', $report->reference) }}"
                        data-found-response-form
                    >
                        <div class="mt-6 space-y-4 animate-pulse">
                            <div class="h-10 bg-slate-200 dark:bg-slate-700 rounded-xl"></div>
                            <div class="h-10 bg-slate-200 dark:bg-slate-700 rounded-xl"></div>
                            <div class="h-24 bg-slate-200 dark:bg-slate-700 rounded-xl"></div>
                            <div class="h-10 bg-[#185c54]/20 rounded-xl"></div>
                        </div>
                    </v-found-response-form>
                @endif
            </x-web::card>
        </div>
    </x-web::section>

    @if ($student && ! $isOwner && ! $existingResponse)
        @pushOnce('scripts')
            <script
                type="text/x-template"
                id="v-found-response-form-template"
            >
                <x-web::form
                    v-slot="{ meta, errors, handleSubmit }"
                    as="div"
                >
                    <form
                        novalidate
                        @submit="handleSubmit($event, submitResponse)"
                        ref="responseForm"
                        data-found-response-form
                        class="mt-6 space-y-4"
                    >
                        <x-web::form.control-group>
                            <x-web::form.control-group.label for="found_location" :required="true">
                                @lang('campusfind_web_web::app.web.found_response.found_location')
                            </x-web::form.control-group.label>

                            <x-web::form.control-group.control
                                type="text"
                                name="found_location"
                                id="found_location"
                                rules="required|max:255"
                                :label="trans('campusfind_web_web::app.web.found_response.found_location')"
                                :required="true"
                            />

                            <x-web::form.control-group.error name="found_location" />
                        </x-web::form.control-group>

                        <x-web::form.control-group>
                            <x-web::form.control-group.label for="found_at">
                                @lang('campusfind_web_web::app.web.found_response.found_at')
                            </x-web::form.control-group.label>

                            <x-web::form.control-group.control
                                type="datetime-local"
                                name="found_at"
                                id="found_at"
                                :label="trans('campusfind_web_web::app.web.found_response.found_at')"
                            />

                            <x-web::form.control-group.error name="found_at" />
                        </x-web::form.control-group>

                        <x-web::form.control-group>
                            <x-web::form.control-group.label for="dropoff_location" :required="true">
                                @lang('campusfind_web_web::app.web.found_response.dropoff_location')
                            </x-web::form.control-group.label>

                            <x-web::form.control-group.control
                                type="select"
                                name="dropoff_location"
                                id="dropoff_location"
                                rules="required"
                                :label="trans('campusfind_web_web::app.web.found_response.dropoff_location')"
                                :required="true"
                            >
                                <option value="">@lang('campusfind_web_web::app.web.found_response.select_dropoff')</option>
                                @foreach ($dropoffLocations as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </x-web::form.control-group.control>

                            <x-web::form.control-group.error name="dropoff_location" />
                        </x-web::form.control-group>

                        <x-web::form.control-group>
                            <x-web::form.control-group.label for="message">
                                @lang('campusfind_web_web::app.web.found_response.message')
                            </x-web::form.control-group.label>

                            <x-web::form.control-group.control
                                type="textarea"
                                name="message"
                                id="message"
                                rows="4"
                                rules="max:2000"
                                :label="trans('campusfind_web_web::app.web.found_response.message')"
                            />

                            <x-web::form.control-group.error name="message" />
                        </x-web::form.control-group>

                        <x-web::form.control-group>
                            <x-web::form.control-group.label for="response_images">
                                @lang('campusfind_web_web::app.web.found_response.images')
                            </x-web::form.control-group.label>

                            <x-web::form.control-group.control
                                type="file"
                                name="images[]"
                                id="response_images"
                                multiple
                                accept="image/jpeg,image/png,image/webp"
                            />

                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                @lang('campusfind_web_web::app.web.found_response.images_hint')
                            </p>

                            <x-web::form.control-group.error name="images" />
                        </x-web::form.control-group>

                        <x-web::button
                            type="submit"
                            variant="primary"
                            class="w-full"
                            ::loading="isProcessing"
                        >
                            @lang('campusfind_web_web::app.web.found_response.submit')
                        </x-web::button>
                    </form>
                </x-web::form>
            </script>

            <script type="module">
                app.component('v-found-response-form', {
                    template: '#v-found-response-form-template',

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
                        submitResponse(params, { resetForm, setErrors }) {
                            this.isProcessing = true;
                            const formData = new FormData(this.$refs.responseForm);

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
                                    } else {
                                        window.location.reload();
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

    @if ($existingResponse && in_array($existingResponse->status->value, ['submitted', 'under_review'], true))
        @pushOnce('scripts')
            <script
                type="text/x-template"
                id="v-cancel-response-form-template"
            >
                <form novalidate @submit.prevent="cancelResponse">
                    <x-web::button
                        type="submit"
                        variant="outline"
                        class="w-full"
                        ::loading="isProcessing"
                    >
                        <slot></slot>
                    </x-web::button>
                </form>
            </script>

            <script type="module">
                app.component('v-cancel-response-form', {
                    template: '#v-cancel-response-form-template',

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
                        cancelResponse() {
                            this.isProcessing = true;

                            this.$axios.post(this.actionUrl)
                                .then((response) => {
                                    this.isProcessing = false;

                                    if (response.data.message) {
                                        this.$emitter.emit('add-flash', {
                                            type: 'success',
                                            message: response.data.message
                                        });
                                    }

                                    window.location.reload();
                                })
                                .catch((error) => {
                                    this.isProcessing = false;

                                    if (error.response && error.response.data && error.response.data.message) {
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
