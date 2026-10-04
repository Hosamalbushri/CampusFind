<?php

namespace CampusFind\LostAndFound\DataGrids\Employee;

use CampusFind\LostAndFound\Enums\ItemStatus;
use CampusFind\LostAndFound\Services\Application\LostAndFoundAuthorization;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Webkul\DataGrid\DataGrid;
use Webkul\User\Models\User;

class FoundItemDataGrid extends DataGrid
{
    private const SORT_COLUMNS = [
        'id' => 'lost_found_items.id',
        'public_reference' => 'lost_found_items.public_reference',
        'title' => 'lost_found_items.title',
        'found_at' => 'lost_found_items.found_at',
        'status' => 'lost_found_items.status',
        'category_code' => 'lost_found_categories.code',
    ];

    public function prepareQueryBuilder(): Builder
    {
        $actor = $this->actor();
        LostAndFoundAuthorization::authorizeUser($actor, 'lost_found.items.view');

        $query = DB::table('lost_found_items')
            ->leftJoin('lost_found_categories', 'lost_found_items.category_id', '=', 'lost_found_categories.id')
            ->select([
                'lost_found_items.id',
                'lost_found_items.public_reference',
                'lost_found_items.title',
                'lost_found_items.found_location',
                'lost_found_items.found_at',
                'lost_found_items.status',
                'lost_found_categories.code as category_code',
            ]);

        if ($this->can($actor, 'lost_found.claims.view')) {
            $query->selectSub(
                DB::table('lost_found_claims')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('lost_found_claims.found_item_id', 'lost_found_items.id'),
                'claim_count',
            )->selectRaw('CASE WHEN lost_found_items.approved_claim_id IS NULL THEN 0 ELSE 1 END as has_approved_claim');
        }

        if ($this->can($actor, 'lost_found.custody.manage')) {
            $query->leftJoin('users as current_custodian', 'lost_found_items.current_custodian_user_id', '=', 'current_custodian.id')
                ->addSelect('current_custodian.name as current_custodian_name');
        }

        return $query;
    }

    public function prepareColumns(): void
    {
        foreach ([
            ['id', 'integer', false, false, true],
            ['public_reference', 'string', true, false, true],
            ['title', 'string', true, false, true],
            ['category_code', 'string', false, false, true],
            ['found_location', 'string', true, false, false],
            ['found_at', 'date', false, false, true],
            ['status', 'string', false, true, true],
        ] as [$index, $type, $searchable, $filterable, $sortable]) {
            $column = [
                'index' => $index,
                'label' => trans('lost_found::app.employee.items.'.($index === 'title' ? 'title_column' : $index)),
                'type' => $type,
                'searchable' => $searchable,
                'filterable' => $filterable,
                'sortable' => $sortable,
            ];

            if ($index === 'status') {
                $column['filterable_type'] = 'dropdown';
                $column['closure'] = static fn ($row): string => match ($row->status) {
                    'draft' => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800">● '.e(trans("lost_found::app.employee.items.statuses.{$row->status}")).'</span>',
                    'reported' => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800">● '.e(trans("lost_found::app.employee.items.statuses.{$row->status}")).'</span>',
                    'in_custody' => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800">● '.e(trans("lost_found::app.employee.items.statuses.{$row->status}")).'</span>',
                    'returned' => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold text-teal-800 bg-teal-50 border border-teal-200 dark:bg-teal-950/40 dark:text-teal-300 dark:border-teal-800">● '.e(trans("lost_found::app.employee.items.statuses.{$row->status}")).'</span>',
                    'disposed' => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold text-rose-700 bg-rose-50 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800">● '.e(trans("lost_found::app.employee.items.statuses.{$row->status}")).'</span>',
                    default => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-200 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800">● '.e(trans("lost_found::app.employee.items.statuses.{$row->status}")).'</span>',
                };
                $column['filterable_options'] = array_map(
                    static fn (ItemStatus $status): array => [
                        'label' => trans("lost_found::app.employee.items.statuses.{$status->value}"),
                        'value' => $status->value,
                    ],
                    ItemStatus::cases(),
                );
            }

            $this->addColumn($column);

            if ($index === 'id') {
                $this->addColumn([
                    'index' => 'report_type',
                    'label' => trans('lost_found::app.employee.items.report_type'),
                    'type' => 'string',
                    'closure' => static fn ($row): string => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-[#185c54] border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800">● '.e(trans('lost_found::app.employee.items.types.found')).'</span>',
                ]);
            }
        }

        $actor = $this->actor();

        if ($this->can($actor, 'lost_found.claims.view')) {
            foreach (['claim_count' => 'integer', 'has_approved_claim' => 'boolean'] as $index => $type) {
                $this->addColumn([
                    'index' => $index,
                    'label' => trans("lost_found::app.employee.items.{$index}"),
                    'type' => $type,
                ]);
            }
        }

        if ($this->can($actor, 'lost_found.custody.manage')) {
            $this->addColumn([
                'index' => 'current_custodian_name',
                'label' => trans('lost_found::app.employee.items.current_custodian_name'),
                'type' => 'string',
            ]);
        }
    }

    public function prepareActions(): void
    {
        $actor = $this->actor();

        if ($this->can($actor, 'lost_found.matches.view')) {
            $this->addAction([
                'icon' => 'icon-search',
                'title' => trans('lost_found::app.employee.matches.view_for_item'),
                'method' => 'GET',
                'url' => fn ($row) => route('admin.lost_found.matches.index', ['found_item_id' => (int) $row->id]),
            ]);
        }

        if ($this->can($actor, 'lost_found.claims.view')) {
            $this->addAction([
                'icon' => 'icon-eye',
                'title' => trans('lost_found::app.employee.claims.view_claims'),
                'method' => 'GET',
                'url' => fn ($row) => route('admin.lost_found.items.claims.index', (int) $row->id),
            ]);
        }

        if ($this->can($actor, 'lost_found.items.edit')) {
            $this->addAction([
                'icon' => 'icon-tick',
                'title' => trans('lost_found::app.employee.items.approve'),
                'method' => 'POST',
                'url' => fn ($row) => route('admin.lost_found.items.approve', (int) $row->id),
            ]);
        }
    }

    protected function validatedRequest(): array
    {
        return request()->validate([
            'filters' => ['sometimes', 'array:all,status'],
            'filters.all' => ['sometimes', 'array', 'max:1'],
            'filters.all.*' => ['string', 'max:100'],
            'filters.status' => ['sometimes', 'array', 'max:5'],
            'filters.status.*' => ['string', Rule::in(array_column(ItemStatus::cases(), 'value'))],
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
            $this->queryBuilder->whereIn('lost_found_items.status', $requestedFilters['status']);
        }

        foreach ($requestedFilters['all'] ?? [] as $term) {
            $pattern = '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $term).'%';
            $referencePattern = '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], strtolower($term)).'%';

            $this->queryBuilder->where(function (Builder $query) use ($pattern, $referencePattern): void {
                $query->whereRaw("lost_found_items.public_reference_key LIKE ? ESCAPE '!'", [$referencePattern])
                    ->orWhereRaw("lost_found_items.title LIKE ? ESCAPE '!'", [$pattern])
                    ->orWhereRaw("lost_found_items.found_location LIKE ? ESCAPE '!'", [$pattern])
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
