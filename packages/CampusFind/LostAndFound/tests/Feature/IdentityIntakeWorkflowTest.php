<?php

namespace CampusFind\LostAndFound\Tests\Feature;

use CampusFind\LostAndFound\Enums\FoundItemImageVisibility;
use CampusFind\LostAndFound\Enums\FoundItemSubmissionChannel;
use CampusFind\LostAndFound\Enums\ItemStatus;
use CampusFind\LostAndFound\Models\FoundItem;
use CampusFind\LostAndFound\Models\FoundItemPrivateDetail;
use CampusFind\LostAndFound\Models\LostFoundCategory;
use CampusFind\LostAndFound\Services\Application\EmployeeItemApplicationService;
use CampusFind\LostAndFound\Services\Application\PublicFoundItemIntakeApplicationService;
use CampusFind\LostAndFound\Services\FoundItemImageService;
use CampusFind\LostAndFound\Tests\TestCase;
use CampusFind\Student\Models\Student;
use DomainException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use LogicException;
use RuntimeException;
use Webkul\User\Models\Role;
use Webkul\User\Models\User;

class IdentityIntakeWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    private LostFoundCategory $category;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Storage::fake('lost_found_private');
        $this->category = LostFoundCategory::create([
            'code' => 'identity-'.Str::random(8),
            'is_active' => true,
        ]);
    }

    public function test_anonymous_intake_has_no_fabricated_human_identity_or_custody(): void
    {
        $usersBefore = User::query()->count();
        $item = $this->publicService()->submit(null, $this->payload());

        $this->assertSame(FoundItemSubmissionChannel::PUBLIC_ANONYMOUS, $item->submission_channel);
        $this->assertNull($item->logged_by_user_id);
        $this->assertNull($item->reporter_student_id);
        $this->assertNull($item->submitted_by_student_id);
        $this->assertNull($item->intake_employee_user_id);
        $this->assertNull($item->current_custodian_user_id);
        $this->assertSame($usersBefore, User::query()->count());
    }

    public function test_authenticated_student_is_reporter_and_submitter_without_staff_impersonation(): void
    {
        $student = $this->student('REPORTER');
        $payload = $this->payload() + [
            'logged_by_user_id' => 999,
            'reporter_student_id' => 999,
            'submitted_by_student_id' => 999,
            'intake_employee_user_id' => 999,
        ];
        $item = $this->publicService()->submit($student, $payload);

        $this->assertSame(FoundItemSubmissionChannel::STUDENT_SELF_SERVICE, $item->submission_channel);
        $this->assertSame($student->id, $item->reporter_student_id);
        $this->assertSame($student->id, $item->submitted_by_student_id);
        $this->assertNull($item->logged_by_user_id);
        $this->assertNull($item->intake_employee_user_id);
    }

    public function test_employee_assisted_intake_preserves_employee_and_distinct_reporter_without_custody(): void
    {
        $employee = $this->employee();
        $reporter = $this->student('ASSISTED');
        $item = app(EmployeeItemApplicationService::class)->createFoundItem($employee, $this->payload() + [
            'reporter_student_id' => $reporter->id,
            'storage_location' => 'Untrusted attempted custody shortcut',
        ]);

        $this->assertSame(FoundItemSubmissionChannel::EMPLOYEE_ASSISTED, $item->submission_channel);
        $this->assertSame($employee->id, $item->logged_by_user_id);
        $this->assertSame($employee->id, $item->intake_employee_user_id);
        $this->assertSame($reporter->id, $item->reporter_student_id);
        $this->assertNull($item->submitted_by_student_id);
        $this->assertNull($item->current_custodian_user_id);
        $this->assertDatabaseCount('lost_found_custody_records', 0);
    }

    public function test_historical_rows_remain_explicitly_uncertain_and_keep_their_reference(): void
    {
        $legacyUser = $this->employee();
        $legacyUser->forceFill([
            'name' => 'System Intake',
            'email' => 'intake@campusfind.system',
        ])->save();
        $item = FoundItem::create([
            'public_reference' => 'LEGACY-'.Str::random(8),
            'category_id' => $this->category->id,
            'logged_by_user_id' => $legacyUser->id,
            'status' => ItemStatus::DRAFT,
            'title' => 'Historical intake',
        ])->refresh();

        $this->assertSame(FoundItemSubmissionChannel::LEGACY_UNCERTAIN, $item->submission_channel);
        $this->assertSame($legacyUser->id, $item->logged_by_user_id);
        $this->assertNull($item->reporter_student_id);
        $this->assertNull($item->intake_employee_user_id);
        $this->assertDatabaseHas('users', ['id' => $legacyUser->id, 'name' => 'System Intake']);
    }

    public function test_public_intake_image_is_sanitized_without_a_fake_staff_creator(): void
    {
        $item = $this->publicService()->submit(
            null,
            $this->payload(),
            UploadedFile::fake()->image('public-proof.jpg', 30, 30),
        );
        $image = $item->images()->firstOrFail();

        $this->assertNull($image->created_by_user_id);
        Storage::disk('public')->assertExists($image->storage_key);
        $this->assertSame('image/jpeg', $image->mime_type);
    }

    public function test_creatorless_intake_cannot_create_a_staff_only_image(): void
    {
        $item = $this->publicService()->submit(null, $this->payload());

        $this->expectException(DomainException::class);
        app(FoundItemImageService::class)->addImage(
            $item,
            null,
            FoundItemImageVisibility::STAFF_ONLY,
            UploadedFile::fake()->image('private-proof.jpg', 30, 30),
        );
    }

    public function test_related_intake_changes_roll_back_together(): void
    {
        FoundItemPrivateDetail::creating(static function (): void {
            throw new RuntimeException('Injected intake-detail failure.');
        });

        try {
            $this->publicService()->submit(null, $this->payload() + ['dropoff_location' => 'main_security']);
            $this->fail('Injected failure should escape.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Injected intake-detail failure.', $exception->getMessage());
        }

        $this->assertDatabaseCount('lost_found_items', 0);
        $this->assertDatabaseCount('lost_found_item_private_details', 0);
    }

    public function test_transient_student_cannot_be_used_as_an_authenticated_identity(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->publicService()->submit(new Student, $this->payload());
    }

    public function test_identity_contract_rejects_inconsistent_or_later_rewritten_attribution(): void
    {
        try {
            FoundItem::create([
                'public_reference' => 'INVALID-'.Str::random(8),
                'category_id' => $this->category->id,
                'submission_channel' => FoundItemSubmissionChannel::PUBLIC_ANONYMOUS,
                'reporter_student_id' => $this->student('SPOOF')->id,
                'status' => ItemStatus::DRAFT,
                'title' => 'Invalid identity combination',
            ]);
            $this->fail('Inconsistent identity should fail.');
        } catch (LogicException) {
            $this->assertDatabaseMissing('lost_found_items', ['title' => 'Invalid identity combination']);
        }

        $item = $this->publicService()->submit(null, $this->payload());
        $this->expectException(LogicException::class);
        $item->update(['submitted_by_student_id' => $this->student('REWRITE')->id]);
    }

    private function publicService(): PublicFoundItemIntakeApplicationService
    {
        return app(PublicFoundItemIntakeApplicationService::class);
    }

    private function payload(): array
    {
        return [
            'category_id' => $this->category->id,
            'title' => 'Found identity test item',
            'found_location' => 'Central Library',
            'found_at' => now()->subHour()->format('Y-m-d H:i:s'),
            'description' => 'Safe public description',
        ];
    }

    private function student(string $prefix): Student
    {
        return Student::create([
            'university_card_number' => $prefix.'-'.Str::random(10),
            'password' => Hash::make(Str::random(20)),
            'name' => $prefix.' Student',
        ]);
    }

    private function employee(): User
    {
        $role = Role::create([
            'name' => 'Intake Role '.Str::random(8),
            'permission_type' => 'custom',
            'permissions' => ['lost_found.items.create'],
        ]);

        return User::create([
            'name' => 'Intake Employee',
            'email' => Str::random(10).'@example.test',
            'password' => Hash::make(Str::random(20)),
            'status' => true,
            'role_id' => $role->id,
        ]);
    }
}
