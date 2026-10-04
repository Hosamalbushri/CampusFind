<?php

namespace CampusFind\Web\Tests\Feature\Web;

use CampusFind\LostAndFound\Enums\FoundItemSubmissionChannel;
use CampusFind\LostAndFound\Enums\ReportStatus;
use CampusFind\LostAndFound\Models\FoundItem;
use CampusFind\LostAndFound\Models\LostFoundCategory;
use CampusFind\LostAndFound\Models\LostReport;
use CampusFind\LostAndFound\Services\Application\StudentFoundResponseApplicationService;
use CampusFind\Student\Models\Student;
use CampusFind\Web\Tests\TestCase;
use Illuminate\Support\Str;

class IdentityPortalTest extends TestCase
{
    private LostFoundCategory $category;

    private Student $student;

    private Student $otherStudent;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = LostFoundCategory::create([
            'code' => 'portal-identity',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $this->student = $this->student('PORTAL-A');
        $this->otherStudent = $this->student('PORTAL-B');
    }

    public function test_authenticated_found_submission_uses_student_identity_not_payload_or_staff(): void
    {
        $response = $this->actingAs($this->student, 'student')->post(
            route('campusfind_web.web.reports.found.store'),
            [
                'category_id' => $this->category->id,
                'title' => 'Student Submitted Umbrella',
                'found_location' => 'Engineering Hall',
                'logged_by_user_id' => 999999,
                'submitted_by_student_id' => $this->otherStudent->id,
            ],
        );

        $response->assertRedirect();
        $item = FoundItem::query()->firstOrFail();
        $this->assertSame(FoundItemSubmissionChannel::STUDENT_SELF_SERVICE, $item->submission_channel);
        $this->assertSame($this->student->id, $item->reporter_student_id);
        $this->assertSame($this->student->id, $item->submitted_by_student_id);
        $this->assertNull($item->logged_by_user_id);
        $this->assertNull($item->intake_employee_user_id);
    }

    public function test_dashboard_shows_only_students_found_reports_and_responses(): void
    {
        $this->submitFound($this->student, 'OWN-FOUND-TITLE');
        $this->submitFound($this->otherStudent, 'OTHER-FOUND-TITLE');

        $lostReport = LostReport::create([
            'public_reference' => 'PORTAL-LOST-'.Str::random(8),
            'student_id' => $this->otherStudent->id,
            'category_id' => $this->category->id,
            'status' => ReportStatus::ACTIVE,
            'title' => 'Response target report',
            'private_description' => 'PRIVATE-REPORT-SECRET',
            'submitted_at' => now(),
        ]);
        app(StudentFoundResponseApplicationService::class)->submit($this->student, $lostReport->id, [
            'found_location' => 'Library',
            'dropoff_location' => 'main_security',
            'message' => 'PRIVATE-RESPONSE-SECRET',
        ]);

        $dashboard = $this->actingAs($this->student, 'student')
            ->get(route('campusfind_web.web.account.dashboard'));

        $dashboard->assertOk()
            ->assertSee('OWN-FOUND-TITLE')
            ->assertDontSee('OTHER-FOUND-TITLE')
            ->assertSee('Response target report')
            ->assertDontSee('PRIVATE-REPORT-SECRET')
            ->assertDontSee('PRIVATE-RESPONSE-SECRET');
    }

    public function test_unrelated_student_cannot_enumerate_another_students_portal_records(): void
    {
        $this->submitFound($this->student, 'FIRST-STUDENT-PRIVATE-TRACKING-TITLE');

        $this->actingAs($this->otherStudent, 'student')
            ->get(route('campusfind_web.web.account.dashboard'))
            ->assertOk()
            ->assertDontSee('FIRST-STUDENT-PRIVATE-TRACKING-TITLE');
    }

    private function submitFound(Student $student, string $title): void
    {
        $this->actingAs($student, 'student')->post(route('campusfind_web.web.reports.found.store'), [
            'category_id' => $this->category->id,
            'title' => $title,
            'found_location' => 'Campus',
        ])->assertRedirect();
    }

    private function student(string $prefix): Student
    {
        return Student::create([
            'university_card_number' => $prefix.'-'.Str::random(8),
            'password' => 'secret-password',
            'name' => $prefix.' Student',
        ]);
    }
}
