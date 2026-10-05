<?php

namespace CampusFind\LostAndFound\Tests\Feature;

use CampusFind\LostAndFound\Enums\FoundResponseStatus;
use CampusFind\LostAndFound\Enums\ItemStatus;
use CampusFind\LostAndFound\Enums\ReportStatus;
use CampusFind\LostAndFound\Models\FoundItem;
use CampusFind\LostAndFound\Models\FoundReportResponse;
use CampusFind\LostAndFound\Models\FoundReportResponseReview;
use CampusFind\LostAndFound\Models\LostFoundCategory;
use CampusFind\LostAndFound\Models\LostReport;
use CampusFind\LostAndFound\Models\VerifiedReportItemLink;
use CampusFind\LostAndFound\Services\Application\EmployeeFoundResponseApplicationService;
use CampusFind\LostAndFound\Services\Application\StudentFoundResponseApplicationService;
use CampusFind\LostAndFound\Services\FoundResponseStateService;
use CampusFind\LostAndFound\Tests\TestCase;
use CampusFind\Student\Models\Student;
use DomainException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;
use Webkul\User\Models\Role;
use Webkul\User\Models\User;

class FoundResponseWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    private Student $owner;

    private Student $finder;

    private LostFoundCategory $category;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('lost_found_private');
        Storage::fake('public');
        $this->owner = $this->student('OWNER');
        $this->finder = $this->student('FINDER');
        $this->category = LostFoundCategory::create([
            'code' => 'response-'.Str::random(8),
            'is_active' => true,
        ]);
    }

    public function test_valid_submission_is_independent_encrypted_and_keeps_report_open(): void
    {
        $report = $this->report();
        $response = $this->submit($this->finder, $report, ['message' => 'Private finder note']);
        $raw = DB::table('lost_found_report_responses')->find($response->id);

        $this->assertSame(FoundResponseStatus::SUBMITTED, $response->status);
        $this->assertSame($this->finder->id, $response->responder_student_id);
        $this->assertSame('Private finder note', $response->message);
        $this->assertNotSame('Private finder note', $raw->message);
        $this->assertArrayNotHasKey('message', $response->toArray());
        $this->assertSame(ReportStatus::ACTIVE, $report->refresh()->status);
        $this->assertNull($report->resolved_found_item_id);
        $this->assertNull($report->closed_at);
    }

    public function test_self_ineligible_and_duplicate_submissions_are_rejected(): void
    {
        $report = $this->report();

        $this->expectException(DomainException::class);
        $this->submit($this->owner, $report);
    }

    public function test_duplicate_and_ineligible_report_states_are_rejected_without_side_effects(): void
    {
        $report = $this->report();
        $this->submit($this->finder, $report);

        try {
            $this->submit($this->finder, $report);
            $this->fail('Duplicate response should fail.');
        } catch (DomainException) {
            $this->assertSame(1, FoundReportResponse::query()->count());
        }

        $cancelled = $this->report(ReportStatus::CANCELLED);
        $otherFinder = $this->student('OTHER');
        $this->expectException(DomainException::class);
        $this->submit($otherFinder, $cancelled);
    }

    public function test_draft_reports_can_receive_found_responses(): void
    {
        $draft = $this->report(ReportStatus::DRAFT);
        $finder = $this->student('FINDER');
        $response = $this->submit($finder, $draft);

        $this->assertNotNull($response);
        $this->assertSame($draft->id, $response->lost_report_id);
    }

    public function test_multiple_students_can_submit_independent_responses_to_one_report(): void
    {
        $report = $this->report();
        $secondFinder = $this->student('SECOND');
        $first = $this->submit($this->finder, $report);
        $second = $this->submit($secondFinder, $report);

        $this->assertNotSame($first->public_reference, $second->public_reference);
        $this->assertCount(2, $report->foundResponses);
        $this->assertSame(ReportStatus::ACTIVE, $report->refresh()->status);
    }

    public function test_supporting_image_is_sanitized_private_and_compensated_on_failure(): void
    {
        $report = $this->report();
        $response = $this->submit($this->finder, $report, [], [UploadedFile::fake()->image('proof.jpg', 40, 40)]);
        $image = $response->images()->firstOrFail();

        Storage::disk('lost_found_private')->assertExists($image->storage_key);
        $this->assertSame('image/jpeg', $image->mime_type);
        $this->assertMatchesRegularExpression('/\A[a-f0-9]{64}\z/', $image->storage_key_hash);
        $this->assertSame([], Storage::disk('public')->allFiles());
        $this->assertArrayNotHasKey('storage_key', $image->toArray());

        $this->expectException(InvalidArgumentException::class);
        $this->submit($this->student('BADIMG'), $report, [], [UploadedFile::fake()->create('bad.pdf', 10, 'application/pdf')]);
    }

    public function test_authorized_review_and_verification_use_phase_two_link_without_resolution(): void
    {
        $report = $this->report();
        $response = $this->submit($this->finder, $report);
        $employee = $this->employee(['lost_found.responses.review', 'lost_found.responses.verify', 'lost_found.items.edit']);
        $item = $this->item($employee);
        $service = app(EmployeeFoundResponseApplicationService::class);

        $service->startReview($employee, $response, 'Physical intake received.');
        $verified = $service->verify($employee, $response->refresh(), $item->public_reference, 'Compared a private physical marking at the desk.');

        $this->assertSame(FoundResponseStatus::VERIFIED, $verified->status);
        $this->assertSame($item->id, $verified->resulting_found_item_id);
        $this->assertDatabaseHas('lost_found_verified_links', ['lost_report_id' => $report->id, 'found_item_id' => $item->id]);
        $this->assertSame(2, $verified->reviews()->count());
        $this->assertSame(ReportStatus::ACTIVE, $report->refresh()->status);
        $this->assertNull($report->resolved_found_item_id);
        $this->assertSame(ItemStatus::REPORTED, $item->refresh()->status);
    }

    public function test_unauthorized_review_and_invalid_transitions_are_rejected(): void
    {
        $response = $this->submit($this->finder, $this->report());
        $unauthorized = $this->employee(['lost_found.responses.view']);

        $this->expectException(AuthorizationException::class);
        app(EmployeeFoundResponseApplicationService::class)->startReview($unauthorized, $response);
    }

    public function test_rejection_is_audited_and_never_changes_report(): void
    {
        $report = $this->report();
        $response = $this->submit($this->finder, $report);
        $employee = $this->employee(['lost_found.responses.review']);
        $service = app(EmployeeFoundResponseApplicationService::class);
        $service->startReview($employee, $response);
        $rejected = $service->reject($employee, $response->refresh(), 'Physical item does not match.');

        $this->assertSame(FoundResponseStatus::REJECTED, $rejected->status);
        $review = $rejected->reviews()->where('to_status', FoundResponseStatus::REJECTED->value)->firstOrFail();
        $this->assertSame('Physical item does not match.', $review->staff_notes);
        $this->assertSame(ReportStatus::ACTIVE, $report->refresh()->status);
        $this->expectException(DomainException::class);
        $service->startReview($employee, $rejected);
    }

    public function test_verification_failure_rolls_back_link_response_and_review(): void
    {
        $report = $this->report();
        $response = $this->submit($this->finder, $report);
        $employee = $this->employee(['lost_found.responses.review', 'lost_found.responses.verify', 'lost_found.items.edit']);
        $item = $this->item($employee);
        $service = app(EmployeeFoundResponseApplicationService::class);
        $service->startReview($employee, $response);
        $reviewCount = FoundReportResponseReview::query()->count();

        FoundReportResponseReview::creating(static function (): void {
            throw new RuntimeException('Injected review failure.');
        });

        try {
            $service->verify($employee, $response->refresh(), $item->public_reference, 'Verified physical identifier.');
            $this->fail('Injected failure should escape.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Injected review failure.', $exception->getMessage());
        }

        $this->assertSame(FoundResponseStatus::UNDER_REVIEW, $response->refresh()->status);
        $this->assertNull($response->resulting_found_item_id);
        $this->assertSame($reviewCount, FoundReportResponseReview::query()->count());
        $this->assertSame(0, VerifiedReportItemLink::query()->count());
    }

    public function test_state_machine_rejects_shortcuts(): void
    {
        $this->assertSame(
            FoundResponseStatus::UNDER_REVIEW,
            FoundResponseStateService::transition(FoundResponseStatus::SUBMITTED, FoundResponseStatus::UNDER_REVIEW),
        );
        $this->expectException(DomainException::class);
        FoundResponseStateService::transition(FoundResponseStatus::SUBMITTED, FoundResponseStatus::VERIFIED);
    }

    public function test_employee_review_http_endpoint_enforces_dedicated_permission(): void
    {
        $response = $this->submit($this->finder, $this->report());
        $reader = $this->employee(['lost_found.responses.view']);

        $this->actingAs($reader, 'user');
        $this->postJson(
            route('admin.lost_found.responses.review', $response->id),
            ['notes' => 'Intake checked.'],
        )->assertStatus(401);

        $reviewer = $this->employee(['lost_found.responses.view', 'lost_found.responses.review']);
        $this->actingAs($reviewer, 'user');
        $this->postJson(
            route('admin.lost_found.responses.review', $response->id),
            ['notes' => 'Intake checked.'],
        )->assertOk()->assertJsonPath('data.status', FoundResponseStatus::UNDER_REVIEW->value);

        $this->assertSame(FoundResponseStatus::UNDER_REVIEW, $response->refresh()->status);
    }

    public function test_private_response_image_http_endpoint_requires_response_read_permission(): void
    {
        $response = $this->submit(
            $this->finder,
            $this->report(),
            [],
            [UploadedFile::fake()->image('private-proof.jpg', 24, 24)],
        );
        $image = $response->images()->firstOrFail();
        $route = route('admin.lost_found.responses.images.show', [$response->id, $image->id]);

        $this->get($route)->assertRedirect();
        $unauthorized = $this->employee(['lost_found.items.view']);
        $this->actingAs($unauthorized, 'user');
        $this->get($route)->assertStatus(401);
        $reader = $this->employee(['lost_found.responses.view']);
        $this->actingAs($reader, 'user');
        $this->get($route)
            ->assertOk()
            ->assertHeader('Content-Type', 'image/jpeg');
    }

    private function submit(Student $student, LostReport $report, array $overrides = [], array $images = []): FoundReportResponse
    {
        return app(StudentFoundResponseApplicationService::class)->submit($student, $report->id, array_merge([
            'found_location' => 'North Library',
            'found_at' => now()->subHour()->format('Y-m-d H:i:s'),
            'dropoff_location' => 'main_security',
            'message' => 'Finder will deliver the item.',
        ], $overrides), $images);
    }

    private function report(ReportStatus $status = ReportStatus::ACTIVE): LostReport
    {
        return LostReport::create([
            'public_reference' => 'RESP-REPORT-'.Str::random(10),
            'student_id' => $this->owner->id,
            'category_id' => $this->category->id,
            'status' => $status,
            'title' => 'Lost response test item',
            'public_description' => 'Safe public description',
            'private_description' => 'PRIVATE-OWNER-SERIAL',
            'lost_location' => 'Science Building',
            'lost_at' => now()->subDay(),
            'submitted_at' => $status === ReportStatus::ACTIVE ? now() : null,
        ]);
    }

    private function item(User $employee): FoundItem
    {
        return FoundItem::create([
            'public_reference' => 'RESP-ITEM-'.Str::random(10),
            'category_id' => $this->category->id,
            'logged_by_user_id' => $employee->id,
            'status' => ItemStatus::REPORTED,
            'title' => 'Existing found item',
            'found_at' => now(),
            'reported_at' => now(),
        ]);
    }

    private function student(string $prefix): Student
    {
        return Student::create([
            'university_card_number' => $prefix.'-'.Str::random(10),
            'password' => Hash::make(Str::random(20)),
            'name' => $prefix.' Student',
        ]);
    }

    private function employee(array $permissions): User
    {
        $role = Role::create([
            'name' => 'Response Role '.Str::random(8),
            'permission_type' => 'custom',
            'permissions' => $permissions,
        ]);

        return User::create([
            'name' => 'Response Employee',
            'email' => Str::random(10).'@example.test',
            'password' => Hash::make(Str::random(20)),
            'status' => true,
            'role_id' => $role->id,
        ]);
    }
}
