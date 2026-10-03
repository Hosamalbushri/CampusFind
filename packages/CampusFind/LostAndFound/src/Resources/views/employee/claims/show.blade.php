<x-admin::layouts>
    <x-slot:title>
        @lang('lost_found::app.employee.claims.detail_title', ['id' => $detail['id']])
    </x-slot>

    <div class="flex flex-col gap-4">
        <!-- Top Toolbar -->
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="flex cursor-pointer items-center">
                    <x-admin::breadcrumbs name="admin.lost_found.claims.show" :entity="$detail['id']" />
                </div>

                <div class="text-xl font-bold dark:text-white">
                    @lang('lost_found::app.employee.claims.detail_title', ['id' => $detail['id']])
                </div>
            </div>

            <div class="flex items-center gap-x-2.5">
                <a
                    href="{{ route('admin.lost_found.items.claims.index', $detail['found_item']['id']) }}"
                    class="transparent-button"
                >
                    @lang('lost_found::app.employee.claims.view_claims')
                </a>
            </div>
        </div>

        <v-claim-actions
            :claim='@json($detail)'
            review-url="{{ route('admin.lost_found.claims.review', $detail['id']) }}"
            approve-url="{{ route('admin.lost_found.claims.approve', $detail['id']) }}"
            reject-url="{{ route('admin.lost_found.claims.reject', $detail['id']) }}"
            revoke-url="{{ route('admin.lost_found.claims.revoke', $detail['id']) }}"
            handover-url="{{ route('admin.lost_found.handover.complete', $detail['found_item']['id']) }}"
        >
            <div class="flex flex-col gap-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <span class="text-xs text-gray-500">@lang('lost_found::app.employee.claims.item'):</span>
                        <p class="font-medium">
                            <a href="{{ route('admin.lost_found.items.claims.index', $detail['found_item']['id']) }}" class="text-blue-600 hover:underline">
                                {{ $detail['found_item']['public_reference'] }} — {{ $detail['found_item']['title'] }}
                            </a>
                        </p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500">@lang('lost_found::app.employee.claims.item_status'):</span>
                        <p class="font-medium">{{ trans('lost_found::app.employee.items.statuses.'.$detail['found_item']['status']) }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500">@lang('lost_found::app.employee.claims.status'):</span>
                        <p class="font-medium">{{ trans('lost_found::app.employee.claims.statuses.'.$detail['status']) }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500">@lang('lost_found::app.employee.claims.claimant_name'):</span>
                        <p class="font-medium">{{ $detail['claimant']['name'] }} (#{{ $detail['claimant']['id'] }})</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500">@lang('lost_found::app.employee.claims.submitted_at'):</span>
                        <p class="font-medium">{{ $detail['submitted_at'] }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500">@lang('lost_found::app.employee.claims.is_approved_claim'):</span>
                        <p class="font-medium">{{ $detail['is_approved_claim'] ? trans('lost_found::app.employee.claims.yes') : trans('lost_found::app.employee.claims.no') }}</p>
                    </div>
                </div>

                <!-- Evidence Section -->
                <div class="mt-4">
                    <h2 class="text-lg font-bold border-b border-gray-200 pb-2 dark:border-gray-800">
                        @lang('lost_found::app.employee.claims.evidence')
                    </h2>
                    <div class="flex flex-col gap-3 pt-3">
                        @forelse ($detail['evidence'] as $evidence)
                            <div class="rounded-md border border-gray-100 bg-gray-50 p-3 dark:border-gray-800 dark:bg-gray-800">
                                <div class="flex justify-between text-xs text-gray-500">
                                    <span class="font-semibold text-gray-700 dark:text-gray-300">{{ trans('lost_found::app.employee.claims.evidence_types.'.$evidence['type']) }}</span>
                                    <span>{{ $evidence['submitted_at'] }}</span>
                                </div>
                                @if ($evidence['text'] !== null)
                                    <p class="mt-2 whitespace-pre-wrap text-sm">{{ $evidence['text'] }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">@lang('lost_found::app.employee.claims.none')</p>
                        @endforelse
                    </div>
                </div>

                <!-- Review History -->
                <div class="mt-4">
                    <h2 class="text-lg font-bold border-b border-gray-200 pb-2 dark:border-gray-800">
                        @lang('lost_found::app.employee.claims.reviews')
                    </h2>
                    <div class="flex flex-col gap-3 pt-3">
                        @forelse ($detail['reviews'] as $review)
                            <div class="rounded-md border border-gray-100 bg-gray-50 p-3 dark:border-gray-800 dark:bg-gray-800">
                                <div class="flex justify-between text-xs text-gray-500">
                                    <span>
                                        {{ trans('lost_found::app.employee.claims.statuses.'.$review['from_status']) }}
                                        &rarr;
                                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ trans('lost_found::app.employee.claims.statuses.'.$review['to_status']) }}</span>
                                    </span>
                                    <span>{{ $review['reviewed_at'] }} (@lang('lost_found::app.employee.claims.reviewer'): {{ $review['reviewer_name'] }})</span>
                                </div>
                                @if ($review['staff_notes'] !== null)
                                    <p class="mt-2 whitespace-pre-wrap text-sm text-gray-600 dark:text-gray-300">{{ $review['staff_notes'] }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">@lang('lost_found::app.employee.claims.none')</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </v-claim-actions>
    </div>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="claim-actions-template"
        >
            <div class="flex flex-col gap-4">
                <!-- Action Buttons Banner -->
                <div class="flex flex-wrap items-center gap-3 rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-800 dark:bg-gray-900">
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        @lang('lost_found::app.employee.items.actions'):
                    </span>

                    <!-- Review button -->
                    @if (bouncer()->hasPermission('lost_found.claims.review'))
                        <button
                            v-if="['submitted', 'needs_information'].includes(claim.status)"
                            type="button"
                            class="secondary-button"
                            @click="$refs.reviewModal.open()"
                        >
                            @lang('lost_found::app.employee.claims.review_btn')
                        </button>
                    @endif

                    <!-- Approve button -->
                    @if (bouncer()->hasPermission('lost_found.claims.approve'))
                        <button
                            v-if="['submitted', 'under_review', 'needs_information'].includes(claim.status)"
                            type="button"
                            class="primary-button"
                            @click="$refs.approveModal.open()"
                        >
                            @lang('lost_found::app.employee.claims.approve_btn')
                        </button>
                    @endif

                    <!-- Reject button -->
                    @if (bouncer()->hasPermission('lost_found.claims.reject'))
                        <button
                            v-if="['submitted', 'under_review', 'needs_information'].includes(claim.status)"
                            type="button"
                            class="danger-button"
                            @click="$refs.rejectModal.open()"
                        >
                            @lang('lost_found::app.employee.claims.reject_btn')
                        </button>
                    @endif

                    <!-- Revoke button -->
                    @if (bouncer()->hasPermission('lost_found.claims.approve'))
                        <button
                            v-if="claim.status === 'approved' && claim.found_item.status !== 'returned'"
                            type="button"
                            class="warning-button"
                            @click="$refs.revokeModal.open()"
                        >
                            @lang('lost_found::app.employee.claims.revoke_btn')
                        </button>
                    @endif

                    <!-- Handover button -->
                    @if (bouncer()->hasPermission('lost_found.handover.complete'))
                        <button
                            v-if="claim.status === 'approved' && claim.found_item.status === 'in_custody'"
                            type="button"
                            class="primary-button"
                            @click="$refs.handoverModal.open()"
                        >
                            @lang('lost_found::app.employee.claims.handover_btn')
                        </button>
                    @endif
                </div>

                <slot></slot>

                <!-- Review Modal -->
                <x-admin::form
                    v-slot="{ meta, values, errors, handleSubmit }"
                    as="div"
                >
                    <form
                        @submit="handleSubmit($event, performReview)"
                        ref="reviewForm"
                    >
                        <x-admin::modal ref="reviewModal">
                            <x-slot:header>
                                <p class="text-lg font-bold text-gray-800 dark:text-white">
                                    @lang('lost_found::app.employee.claims.review_title', ['id' => $detail['id']])
                                </p>
                            </x-slot>

                            <x-slot:content>
                                <div class="grid gap-4">
                                    <input type="hidden" name="to_status" value="under_review" />

                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.label>
                                            @lang('lost_found::app.employee.claims.staff_notes')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="textarea"
                                            name="staff_notes"
                                            :label="trans('lost_found::app.employee.claims.staff_notes')"
                                            rows="3"
                                        />

                                        <x-admin::form.control-group.error control-name="staff_notes" />
                                    </x-admin::form.control-group>
                                </div>
                            </x-slot>

                            <x-slot:footer>
                                <x-admin::button
                                    button-type="submit"
                                    class="primary-button justify-center"
                                    :title="trans('lost_found::app.employee.claims.confirm_action')"
                                    ::loading="isProcessing"
                                    ::disabled="isProcessing"
                                />
                            </x-slot>
                        </x-admin::modal>
                    </form>
                </x-admin::form>

                <!-- Approve Modal -->
                <x-admin::form
                    v-slot="{ meta, values, errors, handleSubmit }"
                    as="div"
                >
                    <form
                        @submit="handleSubmit($event, performApprove)"
                        ref="approveForm"
                    >
                        <x-admin::modal ref="approveModal">
                            <x-slot:header>
                                <p class="text-lg font-bold text-gray-800 dark:text-white">
                                    @lang('lost_found::app.employee.claims.approve_title', ['id' => $detail['id']])
                                </p>
                            </x-slot>

                            <x-slot:content>
                                <div class="grid gap-4">
                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.label>
                                            @lang('lost_found::app.employee.claims.staff_notes')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="textarea"
                                            name="staff_notes"
                                            :label="trans('lost_found::app.employee.claims.staff_notes')"
                                            rows="3"
                                        />

                                        <x-admin::form.control-group.error control-name="staff_notes" />
                                    </x-admin::form.control-group>
                                </div>
                            </x-slot>

                            <x-slot:footer>
                                <x-admin::button
                                    button-type="submit"
                                    class="primary-button justify-center"
                                    :title="trans('lost_found::app.employee.claims.approve_btn')"
                                    ::loading="isProcessing"
                                    ::disabled="isProcessing"
                                />
                            </x-slot>
                        </x-admin::modal>
                    </form>
                </x-admin::form>

                <!-- Reject Modal -->
                <x-admin::form
                    v-slot="{ meta, values, errors, handleSubmit }"
                    as="div"
                >
                    <form
                        @submit="handleSubmit($event, performReject)"
                        ref="rejectForm"
                    >
                        <x-admin::modal ref="rejectModal">
                            <x-slot:header>
                                <p class="text-lg font-bold text-gray-800 dark:text-white">
                                    @lang('lost_found::app.employee.claims.reject_title', ['id' => $detail['id']])
                                </p>
                            </x-slot>

                            <x-slot:content>
                                <div class="grid gap-4">
                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.label class="required">
                                            @lang('lost_found::app.employee.claims.rejection_reason')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="textarea"
                                            name="staff_notes"
                                            rules="required|max:1000"
                                            :label="trans('lost_found::app.employee.claims.rejection_reason')"
                                            rows="3"
                                        />

                                        <x-admin::form.control-group.error control-name="staff_notes" />
                                    </x-admin::form.control-group>
                                </div>
                            </x-slot>

                            <x-slot:footer>
                                <x-admin::button
                                    button-type="submit"
                                    class="danger-button justify-center"
                                    :title="trans('lost_found::app.employee.claims.reject_btn')"
                                    ::loading="isProcessing"
                                    ::disabled="isProcessing"
                                />
                            </x-slot>
                        </x-admin::modal>
                    </form>
                </x-admin::form>

                <!-- Revoke Modal -->
                <x-admin::form
                    v-slot="{ meta, values, errors, handleSubmit }"
                    as="div"
                >
                    <form
                        @submit="handleSubmit($event, performRevoke)"
                        ref="revokeForm"
                    >
                        <x-admin::modal ref="revokeModal">
                            <x-slot:header>
                                <p class="text-lg font-bold text-gray-800 dark:text-white">
                                    @lang('lost_found::app.employee.claims.revoke_title', ['id' => $detail['id']])
                                </p>
                            </x-slot>

                            <x-slot:content>
                                <div class="grid gap-4">
                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.label class="required">
                                            @lang('lost_found::app.employee.claims.status')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="select"
                                            name="to_status"
                                            rules="required"
                                            :label="trans('lost_found::app.employee.claims.status')"
                                        >
                                            <option value="submitted">@lang('lost_found::app.employee.claims.statuses.submitted')</option>
                                            <option value="rejected">@lang('lost_found::app.employee.claims.statuses.rejected')</option>
                                        </x-admin::form.control-group.control>

                                        <x-admin::form.control-group.error control-name="to_status" />
                                    </x-admin::form.control-group>

                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.label class="required">
                                            @lang('lost_found::app.employee.claims.revocation_reason')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="textarea"
                                            name="staff_notes"
                                            rules="required|max:1000"
                                            :label="trans('lost_found::app.employee.claims.revocation_reason')"
                                            rows="3"
                                        />

                                        <x-admin::form.control-group.error control-name="staff_notes" />
                                    </x-admin::form.control-group>
                                </div>
                            </x-slot>

                            <x-slot:footer>
                                <x-admin::button
                                    button-type="submit"
                                    class="warning-button justify-center"
                                    :title="trans('lost_found::app.employee.claims.revoke_btn')"
                                    ::loading="isProcessing"
                                    ::disabled="isProcessing"
                                />
                            </x-slot>
                        </x-admin::modal>
                    </form>
                </x-admin::form>

                <!-- Handover Modal -->
                <x-admin::form
                    v-slot="{ meta, values, errors, handleSubmit }"
                    as="div"
                >
                    <form
                        @submit="handleSubmit($event, performHandover)"
                        ref="handoverForm"
                    >
                        <x-admin::modal ref="handoverModal">
                            <x-slot:header>
                                <p class="text-lg font-bold text-gray-800 dark:text-white">
                                    @lang('lost_found::app.employee.claims.handover_title', ['id' => $detail['id']])
                                </p>
                            </x-slot>

                            <x-slot:content>
                                <div class="grid gap-4">
                                    <input type="hidden" name="recipient_student_id" :value="claim.claimant.id" />

                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.label class="required">
                                            @lang('lost_found::app.employee.items.form.identity_verification_ref')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="text"
                                            name="identity_verification_reference"
                                            rules="required|max:255"
                                            :label="trans('lost_found::app.employee.items.form.identity_verification_ref')"
                                            :placeholder="trans('lost_found::app.employee.items.form.identity_verification_ref')"
                                        />

                                        <x-admin::form.control-group.error control-name="identity_verification_reference" />
                                    </x-admin::form.control-group>

                                    <x-admin::form.control-group>
                                        <x-admin::form.control-group.label>
                                            @lang('lost_found::app.employee.items.form.notes')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="textarea"
                                            name="notes"
                                            :label="trans('lost_found::app.employee.items.form.notes')"
                                            rows="3"
                                        />

                                        <x-admin::form.control-group.error control-name="notes" />
                                    </x-admin::form.control-group>
                                </div>
                            </x-slot>

                            <x-slot:footer>
                                <x-admin::button
                                    button-type="submit"
                                    class="primary-button justify-center"
                                    :title="trans('lost_found::app.employee.claims.handover_btn')"
                                    ::loading="isProcessing"
                                    ::disabled="isProcessing"
                                />
                            </x-slot>
                        </x-admin::modal>
                    </form>
                </x-admin::form>
            </div>
        </script>

        <script type="module">
            app.component('v-claim-actions', {
                template: '#claim-actions-template',

                props: {
                    claim: {
                        type: Object,
                        required: true
                    },
                    reviewUrl: String,
                    approveUrl: String,
                    rejectUrl: String,
                    revokeUrl: String,
                    handoverUrl: String,
                },

                data() {
                    return {
                        isProcessing: false,
                    };
                },

                methods: {
                    performReview(params, { resetForm, setErrors }) {
                        this.executeAction(this.reviewUrl, new FormData(this.$refs.reviewForm), this.$refs.reviewModal, resetForm, setErrors);
                    },

                    performApprove(params, { resetForm, setErrors }) {
                        this.executeAction(this.approveUrl, new FormData(this.$refs.approveForm), this.$refs.approveModal, resetForm, setErrors);
                    },

                    performReject(params, { resetForm, setErrors }) {
                        this.executeAction(this.rejectUrl, new FormData(this.$refs.rejectForm), this.$refs.rejectModal, resetForm, setErrors);
                    },

                    performRevoke(params, { resetForm, setErrors }) {
                        this.executeAction(this.revokeUrl, new FormData(this.$refs.revokeForm), this.$refs.revokeModal, resetForm, setErrors);
                    },

                    performHandover(params, { resetForm, setErrors }) {
                        this.executeAction(this.handoverUrl, new FormData(this.$refs.handoverForm), this.$refs.handoverModal, resetForm, setErrors);
                    },

                    executeAction(url, formData, modalRef, resetForm, setErrors) {
                        this.isProcessing = true;

                        this.$axios.post(url, formData)
                            .then(response => {
                                this.isProcessing = false;
                                modalRef.close();
                                this.$emitter.emit('add-flash', {
                                    type: 'success',
                                    message: response.data.message
                                });
                                resetForm();
                                setTimeout(() => {
                                    window.location.reload();
                                }, 500);
                            })
                            .catch(error => {
                                this.isProcessing = false;
                                if (error.response && error.response.status === 422) {
                                    setErrors(error.response.data.errors);
                                } else {
                                    this.$emitter.emit('add-flash', {
                                        type: 'error',
                                        message: error.response?.data?.message || 'Action failed'
                                    });
                                }
                            });
                    }
                }
            });
        </script>
    @endPushOnce
</x-admin::layouts>
