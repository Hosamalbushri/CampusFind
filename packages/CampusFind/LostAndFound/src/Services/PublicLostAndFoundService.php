<?php

namespace CampusFind\LostAndFound\Services;

use CampusFind\LostAndFound\Contracts\PublicLostAndFoundReadContract;
use CampusFind\LostAndFound\DataTransferObjects\PublicCategoryData;
use CampusFind\LostAndFound\DataTransferObjects\PublicFoundItemData;
use CampusFind\LostAndFound\DataTransferObjects\PublicFoundItemSearchCriteria;
use CampusFind\LostAndFound\DataTransferObjects\PublicFoundItemSearchResult;
use CampusFind\LostAndFound\DataTransferObjects\PublicLostReportData;
use CampusFind\LostAndFound\DataTransferObjects\PublicUnifiedRecordData;
use CampusFind\LostAndFound\DataTransferObjects\PublicUnifiedSearchCriteria;
use CampusFind\LostAndFound\DataTransferObjects\PublicUnifiedSearchResult;
use CampusFind\LostAndFound\Enums\FoundItemImageVisibility;
use CampusFind\LostAndFound\Enums\ItemStatus;
use CampusFind\LostAndFound\Models\FoundItem;
use CampusFind\LostAndFound\Models\LostFoundCategory;
use Carbon\CarbonImmutable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class PublicLostAndFoundService implements PublicLostAndFoundReadContract
{
    /**
     * Retrieve recent public-safe found items for public presentation.
     *
     * @param  int  $limit  Maximum number of items to return (clamped between 1 and 24)
     * @return list<PublicFoundItemData>
     */
    public function getRecentPublicFoundItems(int $limit = 6): array
    {
        $clampedLimit = max(1, min($limit, 24));

        $items = FoundItem::query()
            ->select([
                'id',
                'public_reference',
                'category_id',
                'title',
                'public_description',
                'found_location',
                'found_at',
                'status',
            ])
            ->whereIn('status', [ItemStatus::REPORTED, ItemStatus::IN_CUSTODY])
            ->with([
                'category:id,code,is_active',
                'coverImage',
            ])
            ->orderByDesc('found_at')
            ->orderByDesc('id')
            ->limit($clampedLimit)
            ->get();

        return $items->map(fn (FoundItem $item): PublicFoundItemData => $this->mapToDto($item))->values()->all();
    }

    /**
     * Search and filter public-safe found items with bounded pagination.
     */
    public function searchPublicFoundItems(PublicFoundItemSearchCriteria $criteria): PublicFoundItemSearchResult
    {
        $query = FoundItem::query()
            ->select([
                'id',
                'public_reference',
                'category_id',
                'title',
                'public_description',
                'found_location',
                'found_at',
                'status',
            ])
            ->whereIn('status', [ItemStatus::REPORTED, ItemStatus::IN_CUSTODY]);

        // Filter by category if specified
        if ($criteria->category !== null) {
            $category = LostFoundCategory::query()
                ->where('code', $criteria->category)
                ->where('is_active', true)
                ->first();

            if (! $category) {
                // Return empty result when category is unknown or inactive
                return new PublicFoundItemSearchResult(
                    items: [],
                    total: 0,
                    perPage: $criteria->perPage,
                    currentPage: $criteria->page,
                    lastPage: 1,
                );
            }

            $query->where('category_id', $category->id);
        }

        // Search text across public fields only
        if ($criteria->query !== null) {
            $pattern = '%'.$this->escapeLike($criteria->query).'%';
            $referencePattern = '%'.$this->escapeLike(strtolower($criteria->query)).'%';

            $query->where(function ($sub) use ($pattern, $referencePattern): void {
                $sub->whereRaw("title LIKE ? ESCAPE '!'", [$pattern])
                    ->orWhereRaw("public_description LIKE ? ESCAPE '!'", [$pattern])
                    ->orWhereRaw("found_location LIKE ? ESCAPE '!'", [$pattern])
                    ->orWhereRaw("public_reference_key LIKE ? ESCAPE '!'", [$referencePattern]);
            });
        }

        $total = $query->count();
        $lastPage = max(1, (int) ceil($total / $criteria->perPage));
        $currentPage = min($criteria->page, $lastPage);
        $offset = ($currentPage - 1) * $criteria->perPage;

        $items = $query->with([
            'category:id,code,is_active',
            'coverImage',
        ])
            ->orderByDesc('found_at')
            ->orderByDesc('id')
            ->skip($offset)
            ->take($criteria->perPage)
            ->get();

        $dtoItems = $items->map(fn (FoundItem $item): PublicFoundItemData => $this->mapToDto($item))->values()->all();

        return new PublicFoundItemSearchResult(
            items: $dtoItems,
            total: $total,
            perPage: $criteria->perPage,
            currentPage: $currentPage,
            lastPage: $lastPage,
        );
    }

    /**
     * Search both public LOST reports and FOUND items with one deterministic paginator.
     */
    public function searchPublicRecords(PublicUnifiedSearchCriteria $criteria): PublicUnifiedSearchResult
    {
        $categoryId = null;

        if ($criteria->category !== null) {
            $categoryId = LostFoundCategory::query()
                ->where('code', $criteria->category)
                ->where('is_active', true)
                ->value('id');

            if ($categoryId === null) {
                return $this->emptyUnifiedResult($criteria);
            }
        }

        $queries = [];
        $includeFound = $criteria->type !== 'lost'
            && ($criteria->status === null || in_array($criteria->status, ['reported', 'in_custody'], true));
        $includeLost = $criteria->type !== 'found'
            && ($criteria->status === null || $criteria->status === 'active');

        if ($includeFound) {
            $queries[] = $this->publicFoundRecordsQuery($criteria, $categoryId);
        }

        if ($includeLost) {
            $queries[] = $this->publicLostRecordsQuery($criteria, $categoryId);
        }

        if ($queries === []) {
            return $this->emptyUnifiedResult($criteria);
        }

        $union = array_shift($queries);
        foreach ($queries as $query) {
            $union->unionAll($query);
        }

        $records = DB::query()->fromSub($union, 'public_records');
        $total = (clone $records)->count();
        $lastPage = max(1, (int) ceil($total / $criteria->perPage));
        $currentPage = min($criteria->page, $lastPage);

        $this->applyUnifiedOrder($records, $criteria->sort);

        $rows = $records
            ->offset(($currentPage - 1) * $criteria->perPage)
            ->limit($criteria->perPage)
            ->get();
        $publicImages = $this->loadPublicFoundCoverImages($rows);

        $items = $rows->map(function (object $row) use ($publicImages): PublicUnifiedRecordData {
            $storageKey = $row->record_type === 'found'
                ? $publicImages->get((int) $row->internal_id)
                : null;

            return new PublicUnifiedRecordData(
                publicId: $row->record_type.':'.$row->public_reference,
                type: $row->record_type,
                reference: $row->public_reference,
                title: $row->title,
                status: $row->public_status,
                action: $row->record_type === 'found' ? 'claim_ownership' : 'i_found_this_item',
                actionAvailable: true,
                detailDestination: $row->record_type === 'found' ? 'found_item' : 'lost_report',
                category: $row->category_code,
                location: $row->public_location,
                occurredAt: $row->occurred_at !== null ? CarbonImmutable::parse($row->occurred_at) : null,
                description: $row->public_description,
                imageUrl: $storageKey !== null ? Storage::disk('public')->url($storageKey) : null,
                hasImage: $storageKey !== null,
            );
        })->values()->all();

        return new PublicUnifiedSearchResult(
            items: $items,
            total: $total,
            perPage: $criteria->perPage,
            currentPage: $currentPage,
            lastPage: $lastPage,
        );
    }

    /**
     * Find a single public-safe found item by its unique public reference.
     */
    public function findPublicFoundItemByReference(string $reference): ?PublicFoundItemData
    {
        try {
            $normalizedKey = PublicReference::normalize($reference);
        } catch (InvalidArgumentException) {
            return null;
        }

        $item = FoundItem::query()
            ->select([
                'id',
                'public_reference',
                'category_id',
                'title',
                'public_description',
                'found_location',
                'found_at',
                'status',
            ])
            ->where('public_reference_key', $normalizedKey)
            ->whereIn('status', [ItemStatus::REPORTED, ItemStatus::IN_CUSTODY])
            ->with([
                'category:id,code,is_active',
                'coverImage',
                'images' => function ($query): void {
                    $query->where('visibility', FoundItemImageVisibility::PUBLIC_SAFE->value)
                        ->orderBy('sort_order')
                        ->orderBy('id')
                        ->limit(6);
                },
            ])
            ->first();

        if (! $item) {
            return null;
        }

        return $this->mapToDto($item, loadAdditionalImages: true);
    }

    public function findPublicLostReportByReference(string $reference): ?PublicLostReportData
    {
        try {
            $normalizedKey = PublicReference::normalize($reference);
        } catch (InvalidArgumentException) {
            return null;
        }

        $report = DB::table('lost_found_reports as report')
            ->leftJoin('lost_found_categories as category', function ($join): void {
                $join->on('category.id', '=', 'report.category_id')
                    ->where('category.is_active', true);
            })
            ->where('report.public_reference_key', $normalizedKey)
            ->where('report.status', 'active')
            ->first([
                'report.public_reference',
                'report.title',
                'report.public_description',
                'report.lost_location',
                'report.lost_at',
                'category.code as category_code',
            ]);

        if (! $report) {
            return null;
        }

        return new PublicLostReportData(
            reference: $report->public_reference,
            title: $report->title,
            category: $report->category_code,
            lostLocation: $report->lost_location,
            lostAt: $report->lost_at !== null ? CarbonImmutable::parse($report->lost_at) : null,
            description: $report->public_description,
        );
    }

    /**
     * Retrieve active categories for public search filtering.
     *
     * @return list<PublicCategoryData>
     */
    public function getPublicCategories(): array
    {
        return LostFoundCategory::query()
            ->select(['id', 'code'])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get()
            ->map(fn (LostFoundCategory $cat): PublicCategoryData => new PublicCategoryData(
                code: $cat->code,
                name: $cat->code,
            ))
            ->values()
            ->all();
    }

    /**
     * Map a FoundItem model to an immutable PublicFoundItemData DTO.
     */
    protected function mapToDto(FoundItem $item, bool $loadAdditionalImages = false): PublicFoundItemData
    {
        $cover = $item->coverImage;
        $imageUrl = null;
        $hasImage = false;

        if ($cover && $cover->visibility === FoundItemImageVisibility::PUBLIC_SAFE) {
            $hasImage = true;
            $imageUrl = Storage::disk('public')->url($cover->storage_key);
        }

        $categoryCode = null;
        if ($item->category && $item->category->is_active) {
            $categoryCode = $item->category->code;
        }

        $additionalImages = [];
        if ($loadAdditionalImages && $item->relationLoaded('images')) {
            foreach ($item->images as $img) {
                if ($img->visibility === FoundItemImageVisibility::PUBLIC_SAFE) {
                    $additionalImages[] = Storage::disk('public')->url($img->storage_key);
                }
            }
        }

        return new PublicFoundItemData(
            reference: $item->public_reference,
            title: $item->title,
            category: $categoryCode,
            foundLocation: $item->found_location,
            foundAt: $item->found_at,
            description: $item->public_description,
            imageUrl: $imageUrl,
            hasImage: $hasImage,
            additionalImages: $additionalImages,
        );
    }

    /**
     * Escape special SQL LIKE wildcards using ! as the escape character.
     */
    protected function escapeLike(string $value): string
    {
        return str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $value);
    }

    private function publicFoundRecordsQuery(
        PublicUnifiedSearchCriteria $criteria,
        ?int $categoryId,
    ): Builder {
        $query = DB::table('lost_found_items as source')
            ->leftJoin('lost_found_categories as category', function ($join): void {
                $join->on('category.id', '=', 'source.category_id')
                    ->where('category.is_active', true);
            })
            ->whereIn('source.status', [ItemStatus::REPORTED->value, ItemStatus::IN_CUSTODY->value])
            ->select([
                DB::raw("'found' as record_type"),
                'source.id as internal_id',
                'source.public_reference',
                'source.title',
                'source.public_description',
                'source.found_location as public_location',
                DB::raw('COALESCE(source.found_at, source.reported_at, source.created_at) as occurred_at'),
                'source.status as public_status',
                'category.code as category_code',
            ]);

        if ($criteria->status !== null) {
            $query->where('source.status', $criteria->status);
        }

        $this->applyUnifiedFilters(
            query: $query,
            criteria: $criteria,
            categoryId: $categoryId,
            locationColumn: 'source.found_location',
            dateExpression: 'COALESCE(source.found_at, source.reported_at, source.created_at)',
        );

        return $query;
    }

    private function publicLostRecordsQuery(
        PublicUnifiedSearchCriteria $criteria,
        ?int $categoryId,
    ): Builder {
        $query = DB::table('lost_found_reports as source')
            ->leftJoin('lost_found_categories as category', function ($join): void {
                $join->on('category.id', '=', 'source.category_id')
                    ->where('category.is_active', true);
            })
            ->where('source.status', 'active')
            ->select([
                DB::raw("'lost' as record_type"),
                'source.id as internal_id',
                'source.public_reference',
                'source.title',
                'source.public_description',
                'source.lost_location as public_location',
                DB::raw('COALESCE(source.lost_at, source.submitted_at, source.created_at) as occurred_at'),
                'source.status as public_status',
                'category.code as category_code',
            ]);

        $this->applyUnifiedFilters(
            query: $query,
            criteria: $criteria,
            categoryId: $categoryId,
            locationColumn: 'source.lost_location',
            dateExpression: 'COALESCE(source.lost_at, source.submitted_at, source.created_at)',
        );

        return $query;
    }

    private function applyUnifiedFilters(
        Builder $query,
        PublicUnifiedSearchCriteria $criteria,
        ?int $categoryId,
        string $locationColumn,
        string $dateExpression,
    ): void {
        if ($categoryId !== null) {
            $query->where('source.category_id', $categoryId);
        }

        if ($criteria->query !== null) {
            $pattern = '%'.$this->escapeLike($criteria->query).'%';
            $referencePattern = '%'.$this->escapeLike(strtolower($criteria->query)).'%';

            $query->where(function (Builder $sub) use ($pattern, $referencePattern, $locationColumn): void {
                $sub->whereRaw("source.title LIKE ? ESCAPE '!'", [$pattern])
                    ->orWhereRaw("source.public_description LIKE ? ESCAPE '!'", [$pattern])
                    ->orWhereRaw("{$locationColumn} LIKE ? ESCAPE '!'", [$pattern])
                    ->orWhereRaw("source.public_reference_key LIKE ? ESCAPE '!'", [$referencePattern]);
            });
        }

        if ($criteria->location !== null) {
            $locationPattern = '%'.$this->escapeLike($criteria->location).'%';
            $query->whereRaw("{$locationColumn} LIKE ? ESCAPE '!'", [$locationPattern]);
        }

        if ($criteria->dateFrom !== null) {
            $query->whereDate(DB::raw($dateExpression), '>=', $criteria->dateFrom->format('Y-m-d'));
        }

        if ($criteria->dateTo !== null) {
            $query->whereDate(DB::raw($dateExpression), '<=', $criteria->dateTo->format('Y-m-d'));
        }
    }

    private function applyUnifiedOrder(Builder $query, string $sort): void
    {
        match ($sort) {
            'oldest' => $query->orderBy('occurred_at')->orderBy('record_type')->orderBy('internal_id'),
            'title_asc' => $query->orderBy('title')->orderBy('record_type')->orderBy('internal_id'),
            'title_desc' => $query->orderByDesc('title')->orderBy('record_type')->orderByDesc('internal_id'),
            default => $query->orderByDesc('occurred_at')->orderBy('record_type')->orderByDesc('internal_id'),
        };
    }

    /** @param Collection<int, object> $rows */
    private function loadPublicFoundCoverImages(Collection $rows): Collection
    {
        $foundItemIds = $rows
            ->where('record_type', 'found')
            ->pluck('internal_id')
            ->map(static fn ($id): int => (int) $id)
            ->values();

        if ($foundItemIds->isEmpty()) {
            return collect();
        }

        return DB::table('lost_found_item_images')
            ->whereIn('found_item_id', $foundItemIds)
            ->where('visibility', FoundItemImageVisibility::PUBLIC_SAFE->value)
            ->orderBy('found_item_id')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['found_item_id', 'storage_key'])
            ->unique('found_item_id')
            ->pluck('storage_key', 'found_item_id');
    }

    private function emptyUnifiedResult(PublicUnifiedSearchCriteria $criteria): PublicUnifiedSearchResult
    {
        return new PublicUnifiedSearchResult(
            items: [],
            total: 0,
            perPage: $criteria->perPage,
            currentPage: 1,
            lastPage: 1,
        );
    }
}
