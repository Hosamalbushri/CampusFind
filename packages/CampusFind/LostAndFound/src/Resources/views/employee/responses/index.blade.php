<x-admin::layouts>
    <x-slot:title>@lang('lost_found::app.employee.responses.title')</x-slot>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
            <h1 class="text-xl font-bold text-gray-800 dark:text-white">@lang('lost_found::app.employee.responses.title')</h1>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left dark:bg-gray-800">
                    <tr>
                        <th class="p-3">@lang('lost_found::app.employee.responses.reference')</th>
                        <th class="p-3">@lang('lost_found::app.employee.responses.report')</th>
                        <th class="p-3">@lang('lost_found::app.employee.responses.responder')</th>
                        <th class="p-3">@lang('lost_found::app.employee.responses.status')</th>
                        <th class="p-3">@lang('lost_found::app.employee.responses.submitted_at')</th>
                        <th class="p-3">@lang('lost_found::app.employee.responses.actions')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($responses as $response)
                        <tr class="border-t border-gray-200 dark:border-gray-800">
                            <td class="p-3 font-semibold">{{ $response->public_reference }}</td>
                            <td class="p-3">{{ $response->lostReport->public_reference }} — {{ $response->lostReport->title }}</td>
                            <td class="p-3">{{ $response->responder->name }}</td>
                            <td class="p-3">@lang('lost_found::app.employee.responses.statuses.'.$response->status->value)</td>
                            <td class="p-3">{{ $response->submitted_at?->format('Y-m-d H:i') }}</td>
                            <td class="p-3">
                                <a class="font-semibold text-blue-600 hover:underline" href="{{ route('admin.lost_found.responses.show', $response->id) }}">@lang('lost_found::app.employee.responses.view')</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-8 text-center text-gray-500">@lang('lost_found::app.employee.responses.empty')</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $responses->links() }}
    </div>
</x-admin::layouts>
