<x-admin::layouts>
    <x-slot:title>@lang('lost_found::app.employee.responses.detail_title', ['reference' => $response->public_reference])</x-slot>

    <div class="space-y-5">
        <div class="rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h1 class="text-xl font-bold dark:text-white">@lang('lost_found::app.employee.responses.detail_title', ['reference' => $response->public_reference])</h1>
            <dl class="mt-4 grid gap-4 text-sm md:grid-cols-2">
                <div><dt class="font-semibold">@lang('lost_found::app.employee.responses.report')</dt><dd>{{ $response->lostReport->public_reference }} — {{ $response->lostReport->title }}</dd></div>
                <div><dt class="font-semibold">@lang('lost_found::app.employee.responses.responder')</dt><dd>{{ $response->responder->name }} ({{ $response->responder->university_card_number }})</dd></div>
                <div><dt class="font-semibold">@lang('lost_found::app.employee.responses.status')</dt><dd>@lang('lost_found::app.employee.responses.statuses.'.$response->status->value)</dd></div>
                <div><dt class="font-semibold">@lang('lost_found::app.employee.responses.found_location')</dt><dd>{{ $response->found_location }}</dd></div>
                <div><dt class="font-semibold">@lang('lost_found::app.employee.responses.dropoff_location')</dt><dd>{{ $response->dropoff_location }}</dd></div>
                <div><dt class="font-semibold">@lang('lost_found::app.employee.responses.found_at')</dt><dd>{{ $response->found_at?->format('Y-m-d H:i') ?: trans('lost_found::app.employee.responses.none') }}</dd></div>
                <div class="md:col-span-2"><dt class="font-semibold">@lang('lost_found::app.employee.responses.message')</dt><dd class="whitespace-pre-line">{{ $response->message ?: trans('lost_found::app.employee.responses.none') }}</dd></div>
            </dl>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="font-bold dark:text-white">@lang('lost_found::app.employee.responses.images')</h2>
            <div class="mt-3 flex flex-wrap gap-3">
                @forelse ($response->images as $image)
                    <a class="font-semibold text-blue-600 hover:underline" href="{{ route('admin.lost_found.responses.images.show', [$response->id, $image->id]) }}" target="_blank" rel="noopener">@lang('lost_found::app.employee.responses.image_number', ['number' => $loop->iteration])</a>
                @empty
                    <span class="text-sm text-gray-500">@lang('lost_found::app.employee.responses.none')</span>
                @endforelse
            </div>
        </div>

        @if ($response->status->value === 'submitted')
            <form method="POST" action="{{ route('admin.lost_found.responses.review', $response->id) }}" class="rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                @csrf
                <label class="block text-sm font-semibold" for="review-notes">@lang('lost_found::app.employee.responses.notes')</label>
                <textarea id="review-notes" name="notes" maxlength="2000" class="mt-2 w-full rounded border p-3 dark:bg-gray-950"></textarea>
                <button class="mt-3 rounded bg-blue-600 px-4 py-2 font-semibold text-white" type="submit">@lang('lost_found::app.employee.responses.start_review')</button>
            </form>
        @endif

        @if ($response->status->value === 'under_review')
            <div class="grid gap-5 lg:grid-cols-2">
                <form method="POST" action="{{ route('admin.lost_found.responses.verify', $response->id) }}" class="rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                    @csrf
                    <h2 class="font-bold dark:text-white">@lang('lost_found::app.employee.responses.verify')</h2>
                    <label class="mt-3 block text-sm font-semibold" for="found-item-reference">@lang('lost_found::app.employee.responses.found_item_reference')</label>
                    <input id="found-item-reference" name="found_item_reference" required maxlength="64" class="mt-2 w-full rounded border p-3 dark:bg-gray-950">
                    <label class="mt-3 block text-sm font-semibold" for="verification-evidence">@lang('lost_found::app.employee.responses.verification_evidence')</label>
                    <textarea id="verification-evidence" name="verification_evidence" required maxlength="2000" class="mt-2 w-full rounded border p-3 dark:bg-gray-950"></textarea>
                    <button class="mt-3 rounded bg-[#185c54] px-4 py-2 font-semibold text-white" type="submit">@lang('lost_found::app.employee.responses.verify')</button>
                </form>

                <form method="POST" action="{{ route('admin.lost_found.responses.reject', $response->id) }}" class="rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                    @csrf
                    <h2 class="font-bold dark:text-white">@lang('lost_found::app.employee.responses.reject')</h2>
                    <label class="mt-3 block text-sm font-semibold" for="rejection-notes">@lang('lost_found::app.employee.responses.rejection_reason')</label>
                    <textarea id="rejection-notes" name="notes" required maxlength="2000" class="mt-2 w-full rounded border p-3 dark:bg-gray-950"></textarea>
                    <button class="mt-3 rounded bg-red-600 px-4 py-2 font-semibold text-white" type="submit">@lang('lost_found::app.employee.responses.reject')</button>
                </form>
            </div>
        @endif

        <div class="rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="font-bold dark:text-white">@lang('lost_found::app.employee.responses.review_history')</h2>
            <ol class="mt-3 space-y-3 text-sm">
                @forelse ($response->reviews as $review)
                    <li class="rounded border border-gray-200 p-3 dark:border-gray-800">
                        @lang('lost_found::app.employee.responses.statuses.'.$review->from_status->value)
                        &rarr;
                        @lang('lost_found::app.employee.responses.statuses.'.$review->to_status->value)
                        — {{ $review->reviewer->name }}
                        @if ($review->staff_notes)<p class="mt-2 whitespace-pre-line">{{ $review->staff_notes }}</p>@endif
                    </li>
                @empty
                    <li class="text-gray-500">@lang('lost_found::app.employee.responses.none')</li>
                @endforelse
            </ol>
        </div>
    </div>
</x-admin::layouts>
