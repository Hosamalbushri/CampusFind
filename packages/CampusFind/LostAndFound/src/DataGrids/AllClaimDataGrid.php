<?php

namespace CampusFind\LostAndFound\DataGrids;

use CampusFind\LostAndFound\Enums\ClaimStatus;
use CampusFind\LostAndFound\Services\Application\LostAndFoundAuthorization;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class AllClaimDataGrid extends DataGrid
{
    protected $primaryColumn = 'id';

    public function prepareQueryBuilder(): Builder
    {
        LostAndFoundAuthorization::authorizeUser(auth('user')->user(), 'lost_found.claims.view');

        return DB::table('lost_found_claims')
            ->join('students', 'lost_found_claims.claimant_student_id', '=', 'students.id')
            ->join('lost_found_items', 'lost_found_claims.found_item_id', '=', 'lost_found_items.id')
            ->select([
                'lost_found_claims.id',
                'lost_found_claims.status',
                'lost_found_claims.submitted_at',
                'students.name as claimant_name',
                'lost_found_items.public_reference as item_reference',
                'lost_found_items.title as item_title',
            ]);
    }

    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => trans('lost_found::app.employee.claims.id'),
            'type'       => 'integer',
            'sortable'   => true,
            'searchable' => false,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'   => 'report_type',
            'label'   => trans('lost_found::app.employee.items.report_type'),
            'type'    => 'string',
            'closure' => static fn ($row): string => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800">● '.e(trans('lost_found::app.employee.items.types.claim')).'</span>',
        ]);

        $this->addColumn([
            'index'      => 'item_reference',
            'label'      => trans('lost_found::app.employee.items.public_reference'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'item_title',
            'label'      => trans('lost_found::app.employee.items.title_column'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'claimant_name',
            'label'      => trans('lost_found::app.employee.claims.claimant_name'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'              => 'status',
            'label'              => trans('lost_found::app.employee.claims.status'),
            'type'               => 'string',
            'sortable'           => true,
            'searchable'         => false,
            'filterable'         => true,
            'filterable_type'    => 'dropdown',
            'filterable_options' => array_map(static fn (ClaimStatus $status): array => [
                'label' => trans("lost_found::app.employee.claims.statuses.{$status->value}"),
                'value' => $status->value,
            ], ClaimStatus::cases()),
            'closure'            => static fn ($row): string => match ($row->status) {
                'submitted' => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800">● '.e(trans("lost_found::app.employee.claims.statuses.{$row->status}")).'</span>',
                'under_review' => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800">● '.e(trans("lost_found::app.employee.claims.statuses.{$row->status}")).'</span>',
                'needs_information' => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold text-orange-700 bg-orange-50 border border-orange-200 dark:bg-orange-950/40 dark:text-orange-300 dark:border-orange-800">● '.e(trans("lost_found::app.employee.claims.statuses.{$row->status}")).'</span>',
                'approved' => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800">● '.e(trans("lost_found::app.employee.claims.statuses.{$row->status}")).'</span>',
                'rejected' => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold text-rose-700 bg-rose-50 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800">● '.e(trans("lost_found::app.employee.claims.statuses.{$row->status}")).'</span>',
                'withdrawn' => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-200 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800">● '.e(trans("lost_found::app.employee.claims.statuses.{$row->status}")).'</span>',
                default => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-200">● '.e(trans("lost_found::app.employee.claims.statuses.{$row->status}")).'</span>',
            },
        ]);

        $this->addColumn([
            'index'      => 'submitted_at',
            'label'      => trans('lost_found::app.employee.claims.submitted_at'),
            'type'       => 'date',
            'sortable'   => true,
            'searchable' => false,
            'filterable' => true,
        ]);
    }

    public function prepareActions(): void
    {
        if (bouncer()->hasPermission('lost_found.claims.view')) {
            $this->addAction([
                'icon'   => 'icon-eye',
                'title'  => trans('lost_found::app.employee.claims.view_detail'),
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.lost_found.claims.show', (int) $row->id),
            ]);
        }
    }
}
