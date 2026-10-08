<?php

namespace CampusFind\LostAndFound\DataGrids\Employee;

use CampusFind\LostAndFound\Enums\ClaimStatus;
use CampusFind\LostAndFound\Enums\EvidenceType;
use CampusFind\LostAndFound\Services\Application\LostAndFoundAuthorization;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Webkul\DataGrid\DataGrid;

class ClaimDataGrid extends DataGrid
{
    private const SORT_COLUMNS = [
        'id' => 'lost_found_claims.id',
        'status' => 'lost_found_claims.status',
        'submitted_at' => 'lost_found_claims.submitted_at',
    ];

    public function prepareQueryBuilder(): Builder
    {
        LostAndFoundAuthorization::authorizeUser(auth('user')->user(), 'lost_found.claims.view');

        $itemId = (int) request()->route('id');

        return DB::table('lost_found_claims')
            ->join('students', 'lost_found_claims.claimant_student_id', '=', 'students.id')
            ->join('lost_found_items', 'lost_found_claims.found_item_id', '=', 'lost_found_items.id')
            ->where('lost_found_claims.found_item_id', $itemId)
            ->select([
                'lost_found_claims.id',
                'lost_found_claims.status',
                'lost_found_claims.submitted_at',
                'students.id as claimant_student_id',
                'students.name as claimant_name',
            ])
            ->selectRaw('CASE WHEN lost_found_items.approved_claim_id = lost_found_claims.id THEN 1 ELSE 0 END as is_approved_claim')
            ->selectSub(DB::table('lost_found_claim_evidence')
                ->selectRaw('COUNT(*)')
                ->whereColumn('claim_id', 'lost_found_claims.id')
                ->where('evidence_type', '!=', EvidenceType::IMAGE_ATTACHMENT->value), 'text_evidence_count')
            ->selectSub(DB::table('lost_found_claim_evidence')
                ->selectRaw('COUNT(*)')
                ->whereColumn('claim_id', 'lost_found_claims.id')
                ->where('evidence_type', EvidenceType::IMAGE_ATTACHMENT->value), 'image_evidence_count');
    }

    public function prepareColumns(): void
    {
        foreach (['id', 'status', 'claimant_student_id', 'claimant_name', 'submitted_at', 'text_evidence_count', 'image_evidence_count', 'is_approved_claim'] as $index) {
            $column = [
                'index' => $index,
                'label' => trans("lost_found::app.employee.claims.{$index}"),
                'type' => in_array($index, ['id', 'claimant_student_id', 'text_evidence_count', 'image_evidence_count'], true) ? 'integer' : 'string',
                'sortable' => isset(self::SORT_COLUMNS[$index]),
                'filterable' => $index === 'status',
            ];

            if ($index === 'status') {
                $column['filterable_type'] = 'dropdown';
                $column['filterable_options'] = array_map(static fn (ClaimStatus $status): array => [
                    'label' => trans("lost_found::app.employee.claims.statuses.{$status->value}"),
                    'value' => $status->value,
                ], ClaimStatus::cases());
                $column['closure'] = static fn ($row): string => match ($row->status) {
                    'submitted' => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800">● '.e(trans("lost_found::app.employee.claims.statuses.{$row->status}")).'</span>',
                    'under_review' => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800">● '.e(trans("lost_found::app.employee.claims.statuses.{$row->status}")).'</span>',
                    'needs_information' => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold text-orange-700 bg-orange-50 border border-orange-200 dark:bg-orange-950/40 dark:text-orange-300 dark:border-orange-800">● '.e(trans("lost_found::app.employee.claims.statuses.{$row->status}")).'</span>',
                    'approved' => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800">● '.e(trans("lost_found::app.employee.claims.statuses.{$row->status}")).'</span>',
                    'rejected' => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold text-rose-700 bg-rose-50 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800">● '.e(trans("lost_found::app.employee.claims.statuses.{$row->status}")).'</span>',
                    'withdrawn' => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-200 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800">● '.e(trans("lost_found::app.employee.claims.statuses.{$row->status}")).'</span>',
                    default => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-200">● '.e(trans("lost_found::app.employee.claims.statuses.{$row->status}")).'</span>',
                };
            }

            $this->addColumn($column);

            if ($index === 'id') {
                $this->addColumn([
                    'index'   => 'report_type',
                    'label'   => trans('lost_found::app.employee.items.report_type'),
                    'type'    => 'string',
                    'closure' => static fn ($row): string => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800">● '.e(trans('lost_found::app.employee.items.types.claim')).'</span>',
                ]);
            }
        }
    }

//    public function prepareActions(): void
//    {
//        $this->addAction([
//            'icon'   => 'icon-eye',
//            'title'  => trans('lost_found::app.employee.claims.view_detail'),
//            'method' => 'GET',
//            'url'    => fn ($row) => route('admin.lost_found.claims.show', (int) $row->id),
//        ]);
//    }

    protected function validatedRequest(): array
    {
        return request()->validate([
            'filters' => ['sometimes', 'array:status'],
            'filters.status' => ['sometimes', 'array', 'max:6'],
            'filters.status.*' => ['string', Rule::in(array_column(ClaimStatus::cases(), 'value'))],
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
            $this->queryBuilder->whereIn('lost_found_claims.status', $requestedFilters['status']);
        }

        return $this->queryBuilder;
    }

    protected function processRequestedSorting($requestedSort): Builder
    {
        return $this->queryBuilder->orderBy(self::SORT_COLUMNS[$requestedSort['column'] ?? 'id'], $requestedSort['order'] ?? 'desc');
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
}
