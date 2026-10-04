<?php

namespace CampusFind\Web\Tests\Feature\Web;

use CampusFind\LostAndFound\Enums\FoundResponseStatus;
use CampusFind\LostAndFound\Enums\ReportStatus;
use CampusFind\LostAndFound\Models\FoundReportResponse;
use CampusFind\LostAndFound\Models\LostFoundCategory;
use CampusFind\LostAndFound\Models\LostReport;
use CampusFind\Student\Models\Student;
use CampusFind\Web\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FoundResponsePageTest extends TestCase
{
    private Student $owner;

    private Student $finder;

    private LostFoundCategory $category;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('lost_found_private');
        $this->owner = $this->student('OWNER');
        $this->finder = $this->student('FINDER');
        $this->category = LostFoundCategory::create([
            'code' => 'found-response-web',
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }

    public function test_guest_sees_only_safe_public_detail_and_login_action(): void
    {
        $report = $this->report();

        $response = $this->get(route('campusfind_web.web.lost-reports.show', $report->public_reference));

        $response->assertOk()
            ->assertSee('Lost navy backpack')
            ->assertSee('Safe public description')
            ->assertSee(trans('campusfind_web_web::app.web.found_response.login_action'))
            ->assertDontSee('PRIVATE-OWNER-SERIAL')
            ->assertDontSee($this->owner->name)
            ->assertDontSee('data-found-response-form', false);
    }

    public function test_inactive_reports_are_not_reachable_by_direct_url(): void
    {
        $draft = $this->report(ReportStatus::DRAFT);

        $this->get(route('campusfind_web.web.lost-reports.show', $draft->public_reference))->assertNotFound();
        $this->get(route('campusfind_web.web.lost-reports.show', 'LOST-DOES-NOT-EXIST'))->assertNotFound();
        $this->actingAs($this->finder, 'student')->post(
            route('campusfind_web.web.lost-reports.responses.store', 'LOST-DOES-NOT-EXIST'),
            ['found_location' => 'Library', 'dropoff_location' => 'main_security'],
        )->assertNotFound();
    }

    public function test_authenticated_non_owner_can_submit_and_report_stays_active(): void
    {
        $report = $this->report();

        $page = $this->actingAs($this->finder, 'student')
            ->get(route('campusfind_web.web.lost-reports.show', $report->public_reference));
        $page->assertOk()->assertSee('data-found-response-form', false);

        $response = $this->actingAs($this->finder, 'student')->post(
            route('campusfind_web.web.lost-reports.responses.store', $report->public_reference),
            [
                'found_location' => 'North Library',
                'found_at' => now()->subHour()->format('Y-m-d H:i:s'),
                'dropoff_location' => 'main_security',
                'message' => 'Private note for staff.',
                'images' => [UploadedFile::fake()->image('evidence.jpg', 32, 32)],
            ],
        );

        $response->assertRedirect(route('campusfind_web.web.lost-reports.show', $report->public_reference));
        $stored = FoundReportResponse::query()->firstOrFail();
        $this->assertSame(FoundResponseStatus::SUBMITTED, $stored->status);
        $this->assertSame(ReportStatus::ACTIVE, $report->refresh()->status);
        $this->assertNull($report->resolved_found_item_id);
        Storage::disk('lost_found_private')->assertExists($stored->images()->firstOrFail()->storage_key);
    }

    public function test_guest_post_redirects_to_student_login_without_creating_response(): void
    {
        $report = $this->report();

        $response = $this->post(route('campusfind_web.web.lost-reports.responses.store', $report->public_reference), [
            'found_location' => 'Library',
            'dropoff_location' => 'main_security',
        ]);

        $response->assertRedirect(route('campusfind_web.web.login'));
        $this->assertDatabaseCount('lost_found_report_responses', 0);
    }

    public function test_owner_is_explicitly_ineligible_in_ui_and_server_validation(): void
    {
        $report = $this->report();

        $this->actingAs($this->owner, 'student')
            ->get(route('campusfind_web.web.lost-reports.show', $report->public_reference))
            ->assertOk()
            ->assertSee(trans('campusfind_web_web::app.web.found_response.self_response_policy_pending'))
            ->assertDontSee('data-found-response-form', false);

        $post = $this->actingAs($this->owner, 'student')->from(
            route('campusfind_web.web.lost-reports.show', $report->public_reference),
        )->post(route('campusfind_web.web.lost-reports.responses.store', $report->public_reference), [
            'found_location' => 'Library',
            'dropoff_location' => 'main_security',
        ]);

        $post->assertRedirect(route('campusfind_web.web.lost-reports.show', $report->public_reference))
            ->assertSessionHasErrors('response');
        $this->assertDatabaseCount('lost_found_report_responses', 0);
    }

    public function test_duplicate_submission_is_blocked_and_arabic_page_is_rtl(): void
    {
        $report = $this->report();
        $payload = ['found_location' => 'Library', 'dropoff_location' => 'main_security'];

        $this->actingAs($this->finder, 'student')->post(
            route('campusfind_web.web.lost-reports.responses.store', $report->public_reference),
            $payload,
        )->assertRedirect();
        $this->actingAs($this->finder, 'student')->post(
            route('campusfind_web.web.lost-reports.responses.store', $report->public_reference),
            $payload,
        )->assertSessionHasErrors('response');

        $this->assertDatabaseCount('lost_found_report_responses', 1);
        $this->actingAs($this->finder, 'student')->get(route(
            'campusfind_web.web.lost-reports.show',
            ['reference' => $report->public_reference, 'locale' => 'ar'],
        ))->assertOk()
            ->assertSee('lang="ar"', false)
            ->assertSee('dir="rtl"', false)
            ->assertSee('عثرت على هذا الغرض');
    }

    public function test_invalid_image_and_dropoff_are_rejected_before_storage(): void
    {
        $report = $this->report();

        $response = $this->actingAs($this->finder, 'student')->post(
            route('campusfind_web.web.lost-reports.responses.store', $report->public_reference),
            [
                'found_location' => 'Library',
                'dropoff_location' => 'unapproved-place',
                'images' => [UploadedFile::fake()->create('secret.pdf', 10, 'application/pdf')],
            ],
        );

        $response->assertSessionHasErrors(['dropoff_location', 'images.0']);
        $this->assertDatabaseCount('lost_found_report_responses', 0);
        $this->assertSame([], Storage::disk('lost_found_private')->allFiles());
    }

    public function test_responder_can_cancel_pending_response_without_closing_report(): void
    {
        $report = $this->report();
        $this->actingAs($this->finder, 'student')->post(
            route('campusfind_web.web.lost-reports.responses.store', $report->public_reference),
            ['found_location' => 'Library', 'dropoff_location' => 'main_security'],
        )->assertRedirect();

        $other = $this->student('OTHER');
        $this->actingAs($other, 'student')->post(
            route('campusfind_web.web.lost-reports.responses.cancel', $report->public_reference),
        )->assertNotFound();

        $this->actingAs($this->finder, 'student')->post(
            route('campusfind_web.web.lost-reports.responses.cancel', $report->public_reference),
        )->assertRedirect();

        $this->assertSame(FoundResponseStatus::CANCELLED, FoundReportResponse::query()->firstOrFail()->status);
        $this->assertSame(ReportStatus::ACTIVE, $report->refresh()->status);
    }

    private function report(ReportStatus $status = ReportStatus::ACTIVE): LostReport
    {
        return LostReport::create([
            'public_reference' => 'WEB-LOST-'.fake()->unique()->numerify('########'),
            'student_id' => $this->owner->id,
            'category_id' => $this->category->id,
            'status' => $status,
            'title' => 'Lost navy backpack',
            'public_description' => 'Safe public description',
            'private_description' => 'PRIVATE-OWNER-SERIAL',
            'lost_location' => 'Science Building',
            'lost_at' => now()->subDay(),
            'submitted_at' => $status === ReportStatus::ACTIVE ? now() : null,
        ]);
    }

    private function student(string $prefix): Student
    {
        return Student::create([
            'university_card_number' => $prefix.'-'.fake()->unique()->numerify('########'),
            'password' => 'secret-password',
            'name' => $prefix.' Student',
        ]);
    }
}
