<?php

namespace CampusFind\Web\Tests\Feature\Web;

use CampusFind\LostAndFound\Enums\ItemStatus;
use CampusFind\LostAndFound\Enums\ReportStatus;
use CampusFind\LostAndFound\Models\FoundItem;
use CampusFind\LostAndFound\Models\LostFoundCategory;
use CampusFind\LostAndFound\Models\LostReport;
use CampusFind\Student\Models\Student;
use CampusFind\Web\Tests\TestCase;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class UnifiedBrowsingPageTest extends TestCase
{
    private int $staffId;

    private Student $student;

    private LostFoundCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $roleId = DB::table('roles')->insertGetId([
            'name' => 'Unified Browsing Staff',
            'permission_type' => 'all',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->staffId = DB::table('users')->insertGetId([
            'name' => 'Unified Browsing Staff',
            'email' => 'unified-browsing@example.test',
            'password' => bcrypt('secret'),
            'role_id' => $roleId,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->student = Student::create([
            'university_card_number' => 'UNIFIED-WEB-001',
            'password' => 'secret',
            'name' => 'Unified Browsing Student',
        ]);
        $this->category = LostFoundCategory::create([
            'code' => 'unified-web',
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }

    public function test_page_renders_both_record_types_with_truthful_contextual_actions(): void
    {
        $found = $this->createFound([
            'public_reference' => 'UF-WEB-FOUND-01',
            'title' => 'Found Blue Calculator',
        ]);
        $lost = $this->createLost([
            'public_reference' => 'UL-WEB-LOST-01',
            'title' => 'Lost Green Notebook',
        ]);

        $response = $this->get(route('campusfind_web.web.items.index'));

        $response->assertOk()
            ->assertSee('Found Blue Calculator')
            ->assertSee('Lost Green Notebook')
            ->assertSee('data-public-id="found:'.$found->public_reference.'"', false)
            ->assertSee('data-public-id="lost:'.$lost->public_reference.'"', false)
            ->assertSee(trans('campusfind_web_web::app.web.browse.record_found'))
            ->assertSee(trans('campusfind_web_web::app.web.browse.record_lost'))
            ->assertSee(trans('campusfind_web_web::app.web.browse.claim_ownership'))
            ->assertSee(route('campusfind_web.web.items.show', $found->public_reference))
            ->assertSee(trans('campusfind_web_web::app.web.browse.i_found_this_item'))
            ->assertSee(route('campusfind_web.web.lost-reports.show', $lost->public_reference));
    }

    public function test_type_location_and_date_filters_apply_across_the_unified_page(): void
    {
        $this->createFound([
            'title' => 'Found North Hall Wallet',
            'found_location' => 'North Hall',
            'found_at' => CarbonImmutable::parse('2026-09-12 10:00:00'),
        ]);
        $this->createLost([
            'title' => 'Lost South Hall Wallet',
            'lost_location' => 'South Hall',
            'lost_at' => CarbonImmutable::parse('2026-08-01 10:00:00'),
        ]);

        $lostOnly = $this->get(route('campusfind_web.web.items.index', ['type' => 'lost']));
        $lostOnly->assertOk()->assertSee('Lost South Hall Wallet')->assertDontSee('Found North Hall Wallet');

        $foundOnly = $this->get(route('campusfind_web.web.items.index', [
            'location' => 'North',
            'date_from' => '2026-09-01',
            'date_to' => '2026-09-30',
        ]));
        $foundOnly->assertOk()->assertSee('Found North Hall Wallet')->assertDontSee('Lost South Hall Wallet');
    }

    public function test_invalid_and_restricted_filters_cannot_expose_private_records_or_fields(): void
    {
        $this->createFound([
            'title' => 'Restricted Draft Found Item',
            'status' => ItemStatus::DRAFT,
        ]);
        $this->createLost([
            'title' => 'Public Lost Item',
            'private_description' => 'SECRET-OWNERSHIP-MARKER',
        ]);
        $this->createLost([
            'title' => 'Restricted Draft Lost Report',
            'status' => ReportStatus::DRAFT,
        ]);

        $response = $this->get(route('campusfind_web.web.items.index', [
            'status' => 'draft',
            'sort' => 'malicious-sort',
            'query' => 'NO-PUBLIC-MATCH-MARKER',
        ]));

        $response->assertOk()
            ->assertSee(trans('campusfind_web_web::app.web.browse.invalid_filters'))
            ->assertSee(trans('campusfind_web_web::app.web.browse.no_results'))
            ->assertDontSee('Restricted Draft Found Item')
            ->assertDontSee('Restricted Draft Lost Report')
            ->assertDontSee('SECRET-OWNERSHIP-MARKER');
    }

    public function test_mixed_pagination_empty_state_and_arabic_rtl_are_rendered_accessibly(): void
    {
        for ($index = 1; $index <= 7; $index++) {
            $this->createFound([
                'public_reference' => sprintf('UF-WEB-PAGE-%02d', $index),
                'title' => sprintf('Found Paged Item %02d', $index),
                'found_at' => CarbonImmutable::parse('2026-09-20 12:00:00')->subMinutes($index),
            ]);
            $this->createLost([
                'public_reference' => sprintf('UL-WEB-PAGE-%02d', $index),
                'title' => sprintf('Lost Paged Item %02d', $index),
                'lost_at' => CarbonImmutable::parse('2026-09-20 12:00:00')->subMinutes($index),
            ]);
        }

        $page = $this->get(route('campusfind_web.web.items.index', ['sort' => 'newest']));
        $page->assertOk()
            ->assertSee(trans('campusfind_web_web::app.web.browse.page_of', ['current' => 1, 'last' => 2]))
            ->assertSee('page=2', false)
            ->assertSee('data-unified-search-form', false)
            ->assertSee('data-unified-loading', false)
            ->assertSee('aria-live="polite"', false)
            ->assertSee('dark:bg-slate-900', false)
            ->assertSee('sm:grid-cols-2', false);

        $empty = $this->get(route('campusfind_web.web.items.index', ['query' => 'NO-MATCH-UNIFIED-WEB']));
        $empty->assertOk()->assertSee(trans('campusfind_web_web::app.web.browse.no_results'));

        $arabic = $this->get(route('campusfind_web.web.items.index', ['locale' => 'ar']));
        $arabic->assertOk()
            ->assertSee('lang="ar"', false)
            ->assertSee('dir="rtl"', false)
            ->assertSee('عثرت على هذا الغرض')
            ->assertSee('المطالبة بالملكية');
    }

    private function createFound(array $attributes = []): FoundItem
    {
        return FoundItem::create(array_merge([
            'public_reference' => 'UF-WEB-'.strtoupper(fake()->bothify('????####')),
            'category_id' => $this->category->id,
            'logged_by_user_id' => $this->staffId,
            'title' => 'Unified Public Found Item',
            'public_description' => 'Public found description',
            'found_location' => 'Central Campus',
            'found_at' => CarbonImmutable::now()->subHour(),
            'status' => ItemStatus::IN_CUSTODY,
            'custody_state' => 'received',
            'custody_location' => 'Private Staff Storage',
        ], $attributes));
    }

    private function createLost(array $attributes = []): LostReport
    {
        return LostReport::create(array_merge([
            'public_reference' => 'UL-WEB-'.strtoupper(fake()->bothify('????####')),
            'student_id' => $this->student->id,
            'category_id' => $this->category->id,
            'status' => ReportStatus::ACTIVE,
            'title' => 'Unified Public Lost Report',
            'public_description' => 'Public lost description',
            'private_description' => 'Private lost description',
            'lost_location' => 'Central Campus',
            'lost_at' => CarbonImmutable::now()->subHours(2),
            'submitted_at' => CarbonImmutable::now()->subHour(),
        ], $attributes));
    }
}
