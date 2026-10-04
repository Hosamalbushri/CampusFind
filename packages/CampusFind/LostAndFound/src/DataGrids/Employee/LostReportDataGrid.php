<?php

namespace CampusFind\LostAndFound\DataGrids\Employee;

use CampusFind\LostAndFound\Enums\ReportStatus;
use CampusFind\LostAndFound\Services\Application\LostAndFoundAuthorization;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Webkul\DataGrid\DataGrid;
use Webkul\User\Models\User;

class LostReportDataGrid extends DataGrid
{
    private const SORT_COLUMNS = [
        'id' => 'lost_found_reports.id',
        'public_reference' => 'lost_found_reports.public_reference',
        'title' => 'lost_found_reports.title',
        'lost_at' => 'lost_found_reports.lost_at',
        'status' => 'lost_found_reports.status',
        'category_code' => 'lost_found_categories.code',
        'student_name' => 'students.name',
        'created_at' => 'lost_found_reports.created_at',
    ];

    public function prepareQueryBuilder(): Builder
    {
        LostAndFoundAuthorization::authorizeUser($this->actor(), 'lost_found.items.view');

        return DB::table('lost_found_reports')
            ->leftJoin('students', 'lost_found_reports.student_id', '=', 'students.id')
            ->leftJoin('lost_found_categories', 'lost_found_reports.category_id', '=', 'lost_found_categories.id')
            ->select([
                'lost_found_reports.id',
                'lost_found_reports.public_reference',
                'lost_found_reports.title',
                'lost_found_reports.lost_location',
                'lost_found_reports.lost_at',
                'lost_found_reports.status',
                'lost_found_reports.created_at',
                'students.name as student_name',
                'lost_found_categories.code as category_code',
            ]);
    }

    public function prepareColumns(): void
    {
        $this->addColumn([
            'index' => 'id',
            'label' => trans('lost_found::app.employee.reports.id'),
            'type' => 'integer',
            'sortable' => true,
            'searchable' => false,
            'filterable' => false,
        ]);

        $this->addColumn([
            'index' => 'report_type',
            'label' => trans('lost_found::app.employee.items.report_type'),
            'type' => 'string',
            'closure' => static fn ($row): string => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800">● '.e(trans('lost_found::app.employee.items.types.lost')).'</span>',
        ]);

        $this->addColumn([
            'index' => 'public_reference',
            'label' => trans('lost_found::app.employee.reports.public_reference'),
            'type' => 'string',
            'sortable' => true,
            'searchable' => true,
            'filterable' => false,
        ]);

        $this->addColumn([
            'index' => 'title',
            'label' => trans('lost_found::app.employee.reports.title_column'),
            'type' => 'string',
            'sortable' => true,
            'searchable' => true,
            'filterable' => false,
        ]);

        $this->addColumn([
            'index' => 'category_code',
            'label' => trans('lost_found::app.employee.reports.category_code'),
            'type' => 'string',
            'sortable' => true,
            'searchable' => false,
            'filterable' => false,
        ]);

        $this->addColumn([
            'index' => 'student_name',
            'label' => trans('lost_found::app.employee.reports.student_name'),
            'type' => 'string',
            'sortable' => true,
            'searchable' => true,
            'filterable' => false,
        ]);

        $this->addColumn([
            'index' => 'lost_location',
            'label' => trans('lost_found::app.employee.reports.lost_location'),
            'type' => 'string',
            'sortable' => false,
            'searchable' => true,
            'filterable' => false,
        ]);

        $this->addColumn([
            'index' => 'lost_at',
            'label' => trans('lost_found::app.employee.reports.lost_at'),
            'type' => 'date',
            'sortable' => true,
            'searchable' => false,
            'filterable' => false,
        ]);

        $this->addColumn([
            'index' => 'status',
            'label' => trans('lost_found::app.employee.reports.status'),
            'type' => 'string',
            'sortable' => true,
            'searchable' => false,
            'filterable' => true,
            'filterable_type' => 'dropdown',
            'filterable_options' => array_map(
                static fn (ReportStatus $status): array => [
                    'label' => trans("lost_found::app.employee.reports.statuses.{$status->value}"),
                    'value' => $status->value,
                ],
                ReportStatus::cases(),
            ),
            'closure' => static fn ($row): string => match ($row->status) {
                'draft' => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800">● '.e(trans("lost_found::app.employee.reports.statuses.{$row->status}")).'</span>',
                'active' => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800">● '.e(trans("lost_found::app.employee.reports.statuses.{$row->status}")).'</span>',
                'resolved' => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800">● '.e(trans("lost_found::app.employee.reports.statuses.{$row->status}")).'</span>',
                'cancelled' => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold text-rose-700 bg-rose-50 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800">● '.e(trans("lost_found::app.employee.reports.statuses.{$row->status}")).'</span>',
                default => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-200 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800">● '.e(trans("lost_found::app.employee.reports.statuses.{$row->status}")).'</span>',
            },
        ]);

        $this->addColumn([
            'index' => 'created_at',
            'label' => trans('lost_found::app.employee.reports.created_at'),
            'type' => 'date',
            'sortable' => true,
            'searchable' => false,
            'filterable' => false,
        ]);
    }

    public function prepareActions(): void
    {
        $actor = $this->actor();

        if ($this->can($actor, 'lost_found.matches.view')) {
            $this->addAction([
                'icon' => 'icon-search',
                'title' => trans('lost_found::app.employee.matches.view_for_report'),
                'method' => 'GET',
                'url' => fn ($row) => route('admin.lost_found.matches.index', ['lost_report_id' => (int) $row->id]),
            ]);
        }

        if ($this->can($actor, 'lost_found.items.edit')) {
            $this->addAction([
                'icon' => 'icon-tick',
                'title' => trans('lost_found::app.employee.reports.approve'),
                'method' => 'POST',
                'url' => fn ($row) => route('admin.lost_found.reports.approve', (int) $row->id),
            ]);

            $this->addAction([
                'icon' => 'icon-cross',
                'title' => trans('lost_found::app.employee.reports.reject'),
                'method' => 'POST',
                'url' => fn ($row) => route('admin.lost_found.reports.reject', (int) $row->id),
            ]);
        }
    }

    protected function validatedRequest(): array
    {
        return request()->validate([
            'filters' => ['sometimes', 'array:all,status'],
            'filters.all' => ['sometimes', 'array', 'max:1'],
            'filters.all.*' => ['string', 'max:100'],
            'filters.status' => ['sometimes', 'array', 'max:4'],
            'filters.status.*' => ['string', Rule::in(array_column(ReportStatus::cases(), 'value'))],
            'sort' => ['sometimes', 'array:column,order'],
            'sort.column' => ['required_with:sort.order', Rule::in(array_keys(self::SORT_COLUMNS))],
            'sort.order' => ['required_with:sort.column', Rule::in(['asc', 'desc'])],
            'pagination' => ['sometimes', 'array:page,per_page'],
            'pagination.page' => ['sometimes', 'integer', 'min:1'],
            'pagination.per_page' => ['sometimes', 'integer', Rule::in($this->perPageOptions)],
            'export' => ['prohibited'],
            'format' => ['prohibited'],
        ]);
    }

    protected function processRequestedFilters(array $requestedFilters): Builder
    {
        if ($requestedFilters['status'] ?? []) {
            $this->queryBuilder->whereIn('lost_found_reports.status', $requestedFilters['status']);
        }

        foreach ($requestedFilters['all'] ?? [] as $term) {
            $pattern = '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $term).'%';
            $referencePattern = '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], strtolower($term)).'%';

            $this->queryBuilder->where(function (Builder $query) use ($pattern, $referencePattern): void {
                $query->whereRaw("lost_found_reports.public_reference_key LIKE ? ESCAPE '!'", [$referencePattern])
                    ->orWhereRaw("lost_found_reports.title LIKE ? ESCAPE '!'", [$pattern])
                    ->orWhereRaw("lost_found_reports.lost_location LIKE ? ESCAPE '!'", [$pattern])
                    ->orWhereRaw("students.name LIKE ? ESCAPE '!'", [$pattern])
                    ->orWhereRaw("lost_found_categories.code LIKE ? ESCAPE '!'", [$pattern]);
            });
        }

        return $this->queryBuilder;
    }

    protected function processRequestedSorting($requestedSort): Builder
    {
        return $this->queryBuilder->orderBy(
            self::SORT_COLUMNS[$requestedSort['column'] ?? 'id'],
            $requestedSort['order'] ?? 'desc',
        );
    }

    protected function sanitizeRow($row): \stdClass
    {
        foreach ($row as $field => $value) {
            if (is_string($value)) {
                $row->{$field} = htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            }
        }

        return $row;
    }

    private function actor(): User
    {
        return auth('user')->user();
    }

    private function can(User $actor, string $permission): bool
    {
        return $actor->role?->permission_type === 'all' || $actor->hasPermission($permission);
    }
}
