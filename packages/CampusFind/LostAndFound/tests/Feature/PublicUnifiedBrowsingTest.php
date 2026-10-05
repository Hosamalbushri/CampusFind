<?php

namespace CampusFind\LostAndFound\Tests\Feature;

use CampusFind\LostAndFound\Contracts\PublicLostAndFoundReadContract;
use CampusFind\LostAndFound\DataTransferObjects\PublicUnifiedRecordData;
use CampusFind\LostAndFound\DataTransferObjects\PublicUnifiedSearchCriteria;
use CampusFind\LostAndFound\DataTransferObjects\PublicUnifiedSearchResult;
use CampusFind\LostAndFound\Enums\FoundItemImageVisibility;
use CampusFind\LostAndFound\Enums\ItemStatus;
use CampusFind\LostAndFound\Enums\ReportStatus;
use CampusFind\LostAndFound\Models\FoundItem;
use CampusFind\LostAndFound\Models\FoundItemImage;
use CampusFind\LostAndFound\Models\LostFoundCategory;
use CampusFind\LostAndFound\Models\LostReport;
use CampusFind\LostAndFound\Tests\TestCase;
use CampusFind\Student\Models\Student;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Webkul\User\Models\Role;
use Webkul\User\Models\User;

class PublicUnifiedBrowsingTest extends TestCase
{
    use DatabaseTransactions;

    private User $staff;

    private Student $student;

    private LostFoundCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Storage::fake('lost_found_private');

        $role = Role::create([
            'name' => 'Unified Public Role '.Str::random(8),
            'permission_type' => 'all',
        ]);
        $this->staff = User::create([
            'name' => 'Unified Public Staff',
            'email' => Str::random(12).'@example.test',
            'password' => Hash::make(Str::random(32)),
            'status' => true,
            'role_id' => $role->id,
        ]);
        $this->student = Student::create([
            'university_card_number' => 'UNIFIED-'.Str::random(10),
            'password' => Hash::make(Str::random(32)),
            'name' => 'Unified Public Student',
        ]);
        $this->category = LostFoundCategory::create([
            'code' => 'unified-'.Str::random(8),
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }

    public function test_unified_results_contain_lost_and_found_with_unambiguous_public_ids(): void
    {
        $item = $this->createFound(['title' => 'Found Graphing Calculator']);
        $report = $this->createLost(['title' => 'Lost Graphing Calculator']);

        $this->assertSame($item->id, $report->id, 'Fixture must prove overlapping internal identifiers.');

        $result = $this->reader()->searchPublicRecords(new PublicUnifiedSearchCriteria);
        $records = collect($result->items)->keyBy('type');

        $this->assertInstanceOf(PublicUnifiedSearchResult::class, $result);
        $this->assertSame(2, $result->total);
        $this->assertSame('found:'.$item->public_reference, $records['found']->publicId);
        $this->assertSame('lost:'.$report->public_reference, $records['lost']->publicId);
        $this->assertNotSame($records['found']->publicId, $records['lost']->publicId);
        $this->assertSame('found_item', $records['found']->detailDestination);
        $this->assertSame('lost_report', $records['lost']->detailDestination);
        $this->assertSame('claim_ownership', $records['found']->action);
        $this->assertTrue($records['found']->actionAvailable);
        $this->assertSame('i_found_this_item', $records['lost']->action);
        $this->assertTrue($records['lost']->actionAvailable);
    }

    public function test_type_category_location_date_status_and_search_filters_apply_to_both_types(): void
    {
        $otherCategory = LostFoundCategory::create([
            'code' => 'other-'.Str::random(8),
            'is_active' => true,
        ]);
        $found = $this->createFound([
            'title' => 'Found Azure Bottle',
            'public_description' => 'Reusable bottle with astronomy sticker',
            'found_location' => 'North Library',
            'found_at' => CarbonImmutable::parse('2026-09-10 12:00:00'),
            'status' => ItemStatus::IN_CUSTODY,
        ]);
        $lost = $this->createLost([
            'title' => 'Lost Azure Notebook',
            'public_description' => 'Astronomy lecture notes',
            'lost_location' => 'North Library',
            'lost_at' => CarbonImmutable::parse('2026-09-11 12:00:00'),
        ]);
        $this->createFound([
            'category_id' => $otherCategory->id,
            'title' => 'Other Category Item',
            'found_location' => 'South Hall',
        ]);

        $reader = $this->reader();

        $this->assertSame([$found->public_reference], $this->references($reader->searchPublicRecords(
            new PublicUnifiedSearchCriteria(type: 'found'),
        ), includeOtherCategories: false));
        $this->assertSame([$lost->public_reference], $this->references($reader->searchPublicRecords(
            new PublicUnifiedSearchCriteria(type: 'lost'),
        )));

        $filtered = $reader->searchPublicRecords(new PublicUnifiedSearchCriteria(
            query: 'Astronomy',
            category: $this->category->code,
            location: 'North',
            dateFrom: '2026-09-10',
            dateTo: '2026-09-11',
        ));
        $this->assertEqualsCanonicalizing(
            [$found->public_reference, $lost->public_reference],
            $this->references($filtered),
        );

        $inCustody = $reader->searchPublicRecords(new PublicUnifiedSearchCriteria(status: 'in_custody'));
        $this->assertSame([$found->public_reference], $this->references($inCustody));
        $active = $reader->searchPublicRecords(new PublicUnifiedSearchCriteria(status: 'active'));
        $this->assertSame([$lost->public_reference], $this->references($active));
    }

    public function test_sorting_and_pagination_are_global_deterministic_and_mixed(): void
    {
        $timestamp = CarbonImmutable::parse('2026-09-20 10:00:00');
        for ($index = 1; $index <= 3; $index++) {
            $this->createFound([
                'title' => "Found Batch {$index}",
                'found_at' => $timestamp,
            ]);
            $this->createLost([
                'title' => "Lost Batch {$index}",
                'lost_at' => $timestamp,
            ]);
        }

        $reader = $this->reader();
        $pageOne = $reader->searchPublicRecords(new PublicUnifiedSearchCriteria(page: 1, perPage: 2));
        $pageTwo = $reader->searchPublicRecords(new PublicUnifiedSearchCriteria(page: 2, perPage: 2));
        $pageThree = $reader->searchPublicRecords(new PublicUnifiedSearchCriteria(page: 3, perPage: 2));
        $repeat = $reader->searchPublicRecords(new PublicUnifiedSearchCriteria(page: 1, perPage: 2));

        $this->assertSame(6, $pageOne->total);
        $this->assertSame(3, $pageOne->lastPage);
        $this->assertSame(2, $pageOne->nextPage());
        $this->assertSame(1, $pageTwo->previousPage());
        $this->assertNull($pageThree->nextPage());
        $this->assertSame($this->publicIds($pageOne), $this->publicIds($repeat));
        $this->assertCount(6, array_unique(array_merge(
            $this->publicIds($pageOne),
            $this->publicIds($pageTwo),
            $this->publicIds($pageThree),
        )));

        $titleSorted = $reader->searchPublicRecords(new PublicUnifiedSearchCriteria(sort: 'title_asc'));
        $titles = array_map(static fn (PublicUnifiedRecordData $record): string => $record->title, $titleSorted->items);
        $expected = $titles;
        sort($expected);
        $this->assertSame($expected, $titles);
    }

    public function test_restricted_records_private_fields_and_non_public_images_never_leak(): void
    {
        $publicFound = $this->createFound([
            'title' => 'Visible Found Camera',
            'public_description' => 'Public camera description',
        ]);
        $publicLost = $this->createLost([
            'title' => 'Visible Lost Camera',
            'public_description' => 'Public lost camera description',
            'private_description' => 'PRIVATE_LOST_SERIAL_7788',
        ]);
        $publicFound->privateDetail()->create([
            'identifying_details' => 'PRIVATE_FOUND_MARK_9911',
            'serial_fragment' => 'SECRET-SERIAL',
            'staff_notes' => 'PRIVATE_STAFF_NOTE',
        ]);
        FoundItemImage::create([
            'found_item_id' => $publicFound->id,
            'created_by_user_id' => $this->staff->id,
            'visibility' => FoundItemImageVisibility::STAFF_ONLY,
            'storage_key' => 'lost-found/items/private/not-public.jpg',
            'mime_type' => 'image/jpeg',
            'byte_size' => 1024,
            'sort_order' => 0,
        ]);
        $draftFound = $this->createFound(['status' => ItemStatus::DRAFT, 'title' => 'Public Found Draft']);
        $this->createFound(['status' => ItemStatus::RETURNED, 'title' => 'Hidden Returned Item']);
        $draftLost = $this->createLost(['status' => ReportStatus::DRAFT, 'title' => 'Public Lost Draft']);
        $this->createLost(['status' => ReportStatus::CANCELLED, 'title' => 'Hidden Cancelled Report']);

        $reader = $this->reader();
        $result = $reader->searchPublicRecords(new PublicUnifiedSearchCriteria);
        $serialized = json_encode($result, JSON_THROW_ON_ERROR);

        $this->assertSame(4, $result->total);
        $this->assertStringContainsString($publicFound->public_reference, $serialized);
        $this->assertStringContainsString($publicLost->public_reference, $serialized);
        $this->assertStringContainsString($draftFound->public_reference, $serialized);
        $this->assertStringContainsString($draftLost->public_reference, $serialized);
        $this->assertStringNotContainsString('PRIVATE_LOST_SERIAL_7788', $serialized);
        $this->assertStringNotContainsString('PRIVATE_FOUND_MARK_9911', $serialized);
        $this->assertStringNotContainsString('SECRET-SERIAL', $serialized);
        $this->assertStringNotContainsString('PRIVATE_STAFF_NOTE', $serialized);
        $this->assertStringNotContainsString('not-public.jpg', $serialized);

        foreach (['PRIVATE_LOST_SERIAL_7788', 'PRIVATE_FOUND_MARK_9911', 'PRIVATE_STAFF_NOTE'] as $secret) {
            $this->assertTrue($reader->searchPublicRecords(
                new PublicUnifiedSearchCriteria(query: $secret),
            )->isEmpty());
        }
    }

    public function test_public_safe_cover_images_are_loaded_without_n_plus_one_queries(): void
    {
        $item = $this->createFound();
        $report = $this->createLost();
        FoundItemImage::create([
            'found_item_id' => $item->id,
            'created_by_user_id' => $this->staff->id,
            'visibility' => FoundItemImageVisibility::PUBLIC_SAFE,
            'storage_key' => 'lost-found/items/public/unified-cover.jpg',
            'mime_type' => 'image/jpeg',
            'byte_size' => 2048,
            'sort_order' => 0,
        ]);
        \CampusFind\LostAndFound\Models\LostReportImage::create([
            'lost_report_id' => $report->id,
            'storage_key' => 'lost-found/reports/public/report-cover.jpg',
            'mime_type' => 'image/jpeg',
            'byte_size' => 2048,
            'sort_order' => 0,
        ]);

        DB::flushQueryLog();
        DB::enableQueryLog();
        $result = $this->reader()->searchPublicRecords(new PublicUnifiedSearchCriteria);
        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        $found = collect($result->items)->firstWhere('type', 'found');
        $lost = collect($result->items)->firstWhere('type', 'lost');
        $this->assertTrue($found->hasImage);
        $this->assertStringContainsString('unified-cover.jpg', $found->imageUrl);
        $this->assertTrue($lost->hasImage);
        $this->assertNotNull($lost->imageUrl);
        $this->assertLessThanOrEqual(4, count($queries));

        $singleReport = $this->reader()->findPublicLostReportByReference($report->public_reference);
        $this->assertNotNull($singleReport);
        $this->assertTrue($singleReport->hasImage);
        $this->assertNotNull($singleReport->imageUrl);
    }

    public function test_invalid_criteria_and_sql_fragments_are_normalized_safely(): void
    {
        $this->createFound(['title' => 'Literal 100% Bottle']);
        $this->createFound(['title' => 'Ordinary Bottle']);
        $criteria = new PublicUnifiedSearchCriteria(
            query: '%',
            type: 'restricted',
            dateFrom: 'not-a-date',
            status: 'draft',
            sort: 'drop_table',
            page: -5,
            perPage: 500,
        );

        $result = $this->reader()->searchPublicRecords($criteria);

        $this->assertSame('all', $criteria->type);
        $this->assertNull($criteria->dateFrom);
        $this->assertNull($criteria->status);
        $this->assertSame('newest', $criteria->sort);
        $this->assertSame(1, $criteria->page);
        $this->assertSame(36, $criteria->perPage);
        $this->assertSame(['Literal 100% Bottle'], array_map(
            static fn (PublicUnifiedRecordData $record): string => $record->title,
            $result->items,
        ));
    }

    private function reader(): PublicLostAndFoundReadContract
    {
        return $this->app->make(PublicLostAndFoundReadContract::class);
    }

    private function createFound(array $attributes = []): FoundItem
    {
        return FoundItem::create(array_merge([
            'public_reference' => 'UF-'.strtoupper(Str::random(10)),
            'category_id' => $this->category->id,
            'logged_by_user_id' => $this->staff->id,
            'status' => ItemStatus::REPORTED,
            'title' => 'Unified Found Item',
            'public_description' => 'Unified public found description',
            'found_location' => 'Central Campus',
            'found_at' => CarbonImmutable::now()->subHour(),
            'reported_at' => CarbonImmutable::now(),
        ], $attributes));
    }

    private function createLost(array $attributes = []): LostReport
    {
        return LostReport::create(array_merge([
            'public_reference' => 'UL-'.strtoupper(Str::random(10)),
            'student_id' => $this->student->id,
            'category_id' => $this->category->id,
            'status' => ReportStatus::ACTIVE,
            'title' => 'Unified Lost Report',
            'public_description' => 'Unified public lost description',
            'private_description' => 'Private ownership description',
            'lost_location' => 'Central Campus',
            'lost_at' => CarbonImmutable::now()->subHours(2),
            'submitted_at' => CarbonImmutable::now()->subHour(),
        ], $attributes));
    }

    /** @return list<string> */
    private function references(PublicUnifiedSearchResult $result, bool $includeOtherCategories = true): array
    {
        $records = $result->items;

        if (! $includeOtherCategories) {
            $records = array_values(array_filter(
                $records,
                fn (PublicUnifiedRecordData $record): bool => $record->category === $this->category->code,
            ));
        }

        return array_map(static fn (PublicUnifiedRecordData $record): string => $record->reference, $records);
    }

    /** @return list<string> */
    private function publicIds(PublicUnifiedSearchResult $result): array
    {
        return array_map(static fn (PublicUnifiedRecordData $record): string => $record->publicId, $result->items);
    }
}
