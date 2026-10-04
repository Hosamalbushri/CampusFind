<?php

use CampusFind\LostAndFound\Enums\ItemStatus;
use CampusFind\LostAndFound\Enums\ReportStatus;
use CampusFind\LostAndFound\Models\FoundItem;
use CampusFind\LostAndFound\Models\LostFoundCategory;
use CampusFind\LostAndFound\Models\LostReport;
use CampusFind\LostAndFound\Models\PotentialReportItemMatch;
use CampusFind\LostAndFound\Models\VerifiedReportItemLink;
use CampusFind\LostAndFound\Services\Application\EmployeeReportItemLinkApplicationService;
use CampusFind\LostAndFound\Tests\TestCase;
use CampusFind\Student\Models\Student;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Webkul\User\Models\Role;
use Webkul\User\Models\User;

uses(TestCase::class, DatabaseTransactions::class);

function makeExplicitLinkEmployee(array|string $permissions = 'all'): User
{
    $role = Role::create([
        'name' => 'Explicit Link Role '.Str::random(8),
        'permission_type' => $permissions === 'all' ? 'all' : 'custom',
        'permissions' => $permissions === 'all' ? null : $permissions,
    ]);

    return User::create([
        'name' => 'Explicit Link Employee',
        'email' => Str::random(12).'@example.test',
        'password' => Hash::make(Str::random(32)),
        'status' => true,
        'role_id' => $role->id,
    ]);
}

function makeExplicitLinkStudent(): Student
{
    return Student::create([
        'university_card_number' => 'LINK-'.Str::random(12),
        'password' => Hash::make(Str::random(32)),
        'name' => 'Explicit Link Student',
    ]);
}

function makeExplicitLinkCategory(): LostFoundCategory
{
    return LostFoundCategory::create([
        'code' => 'explicit-link-'.Str::random(10),
        'is_active' => true,
    ]);
}

function makeExplicitLinkReport(
    Student $student,
    LostFoundCategory $category,
    ReportStatus $status = ReportStatus::ACTIVE,
): LostReport {
    return LostReport::create([
        'public_reference' => 'LINK-REPORT-'.Str::random(12),
        'student_id' => $student->id,
        'category_id' => $category->id,
        'status' => $status,
        'title' => 'Explicit link report',
        'submitted_at' => $status === ReportStatus::ACTIVE ? now() : null,
        'closed_at' => $status === ReportStatus::CANCELLED ? now() : null,
    ]);
}

function makeExplicitLinkItem(
    User $employee,
    LostFoundCategory $category,
    ItemStatus $status = ItemStatus::REPORTED,
): FoundItem {
    return FoundItem::create([
        'public_reference' => 'LINK-ITEM-'.Str::random(12),
        'category_id' => $category->id,
        'logged_by_user_id' => $employee->id,
        'status' => $status,
        'title' => 'Explicit link item',
        'found_at' => now(),
        'reported_at' => now(),
    ]);
}

test('authorized employee creates a potential match and explicitly verifies it', function () {
    $employee = makeExplicitLinkEmployee(['lost_found.items.edit']);
    $student = makeExplicitLinkStudent();
    $category = makeExplicitLinkCategory();
    $report = makeExplicitLinkReport($student, $category);
    $item = makeExplicitLinkItem($employee, $category);
    $service = app(EmployeeReportItemLinkApplicationService::class);

    $potential = $service->proposePotentialMatch($employee, $report->id, $item->id);
    $verified = $service->verifyPotentialMatch(
        $employee,
        $potential->id,
        'Staff compared the physical serial marking with private ownership evidence.',
    );
    $raw = DB::table('lost_found_verified_links')->find($verified->id);

    expect($potential->lost_report_id)->toBe($report->id)
        ->and($potential->found_item_id)->toBe($item->id)
        ->and($potential->proposed_by_user_id)->toBe($employee->id)
        ->and($potential->proposed_at)->not->toBeNull()
        ->and($verified->potential_match_id)->toBe($potential->id)
        ->and($verified->lost_report_id)->toBe($report->id)
        ->and($verified->found_item_id)->toBe($item->id)
        ->and($verified->verified_by_user_id)->toBe($employee->id)
        ->and($verified->verified_at)->not->toBeNull()
        ->and($verified->verification_evidence)->toBe('Staff compared the physical serial marking with private ownership evidence.')
        ->and($raw->verification_evidence)->not->toBe($verified->verification_evidence)
        ->and($verified->toArray())->not->toHaveKey('verification_evidence')
        ->and($potential->verifiedLink->is($verified))->toBeTrue()
        ->and($report->potentialItemMatches->contains($potential))->toBeTrue()
        ->and($report->verifiedItemLink->is($verified))->toBeTrue()
        ->and($item->verifiedReportLink->is($verified))->toBeTrue();
});

test('unauthorized employee cannot propose or verify relationships', function () {
    $employee = makeExplicitLinkEmployee(['lost_found.items.view']);
    $authorized = makeExplicitLinkEmployee(['lost_found.items.edit']);
    $student = makeExplicitLinkStudent();
    $category = makeExplicitLinkCategory();
    $report = makeExplicitLinkReport($student, $category);
    $item = makeExplicitLinkItem($authorized, $category);
    $service = app(EmployeeReportItemLinkApplicationService::class);

    expect(fn () => $service->proposePotentialMatch($employee, $report->id, $item->id))
        ->toThrow(AuthorizationException::class);

    $potential = $service->proposePotentialMatch($authorized, $report->id, $item->id);

    expect(fn () => $service->verifyPotentialMatch($employee, $potential->id, 'Unauthorized verification'))
        ->toThrow(AuthorizationException::class)
        ->and(VerifiedReportItemLink::query()->count())->toBe(0);
});

test('invalid report item and potential references are rejected', function () {
    $employee = makeExplicitLinkEmployee(['lost_found.items.edit']);
    $student = makeExplicitLinkStudent();
    $category = makeExplicitLinkCategory();
    $report = makeExplicitLinkReport($student, $category);
    $item = makeExplicitLinkItem($employee, $category);
    $service = app(EmployeeReportItemLinkApplicationService::class);

    expect(fn () => $service->proposePotentialMatch($employee, 999999999, $item->id))
        ->toThrow(ModelNotFoundException::class);
    expect(fn () => $service->proposePotentialMatch($employee, $report->id, 999999999))
        ->toThrow(ModelNotFoundException::class);
    expect(fn () => $service->verifyPotentialMatch($employee, 999999999, 'Missing potential'))
        ->toThrow(ModelNotFoundException::class);

    expect(PotentialReportItemMatch::query()->count())->toBe(0)
        ->and(VerifiedReportItemLink::query()->count())->toBe(0);
});

test('duplicate candidates and contradictory verified relationships are prevented', function () {
    $employee = makeExplicitLinkEmployee(['lost_found.items.edit']);
    $student = makeExplicitLinkStudent();
    $category = makeExplicitLinkCategory();
    $report = makeExplicitLinkReport($student, $category);
    $otherReport = makeExplicitLinkReport($student, $category);
    $item = makeExplicitLinkItem($employee, $category);
    $otherItem = makeExplicitLinkItem($employee, $category);
    $service = app(EmployeeReportItemLinkApplicationService::class);
    $potential = $service->proposePotentialMatch($employee, $report->id, $item->id);

    expect(fn () => $service->proposePotentialMatch($employee, $report->id, $item->id))
        ->toThrow(DomainException::class);

    $sameReportCandidate = $service->proposePotentialMatch($employee, $report->id, $otherItem->id);
    $sameItemCandidate = $service->proposePotentialMatch($employee, $otherReport->id, $item->id);
    $service->verifyPotentialMatch($employee, $potential->id, 'Authoritative physical comparison');

    expect(fn () => $service->verifyPotentialMatch($employee, $sameReportCandidate->id, 'Conflicting report link'))
        ->toThrow(DomainException::class);
    expect(fn () => $service->verifyPotentialMatch($employee, $sameItemCandidate->id, 'Conflicting item link'))
        ->toThrow(DomainException::class);

    $verifiedInsert = static fn (PotentialReportItemMatch $candidate): bool => DB::table('lost_found_verified_links')->insert([
        'potential_match_id' => $candidate->id,
        'lost_report_id' => $candidate->lost_report_id,
        'found_item_id' => $candidate->found_item_id,
        'verified_by_user_id' => $employee->id,
        'verification_evidence' => 'Direct database constraint attempt',
        'verified_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    expect(fn () => $verifiedInsert($sameReportCandidate))->toThrow(QueryException::class);
    expect(fn () => $verifiedInsert($sameItemCandidate))->toThrow(QueryException::class);

    expect(fn () => DB::table('lost_found_potential_matches')->insert([
        'lost_report_id' => $report->id,
        'found_item_id' => $item->id,
        'proposed_by_user_id' => $employee->id,
        'proposed_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]))->toThrow(QueryException::class)
        ->and(VerifiedReportItemLink::query()->count())->toBe(1);
});

test('potential match has no verified or report state side effects', function () {
    $employee = makeExplicitLinkEmployee(['lost_found.items.edit']);
    $student = makeExplicitLinkStudent();
    $category = makeExplicitLinkCategory();
    $report = makeExplicitLinkReport($student, $category);
    $item = makeExplicitLinkItem($employee, $category);

    $potential = app(EmployeeReportItemLinkApplicationService::class)
        ->proposePotentialMatch($employee, $report->id, $item->id);

    expect($potential->verifiedLink)->toBeNull()
        ->and(VerifiedReportItemLink::query()->count())->toBe(0)
        ->and($report->refresh()->status)->toBe(ReportStatus::ACTIVE)
        ->and($report->resolved_found_item_id)->toBeNull()
        ->and($report->closed_at)->toBeNull()
        ->and($item->refresh()->status)->toBe(ItemStatus::REPORTED);
});

test('verified relationship does not resolve its report or modify unrelated reports', function () {
    $employee = makeExplicitLinkEmployee(['lost_found.items.edit']);
    $student = makeExplicitLinkStudent();
    $category = makeExplicitLinkCategory();
    $report = makeExplicitLinkReport($student, $category);
    $unrelated = makeExplicitLinkReport($student, $category);
    $item = makeExplicitLinkItem($employee, $category);
    $unrelatedBefore = DB::table('lost_found_reports')->find($unrelated->id);
    $service = app(EmployeeReportItemLinkApplicationService::class);
    $potential = $service->proposePotentialMatch($employee, $report->id, $item->id);

    $service->verifyPotentialMatch($employee, $potential->id, 'Verified private physical identifier');

    expect($report->refresh()->status)->toBe(ReportStatus::ACTIVE)
        ->and($report->resolved_found_item_id)->toBeNull()
        ->and($report->closed_at)->toBeNull()
        ->and((array) DB::table('lost_found_reports')->find($unrelated->id))->toBe((array) $unrelatedBefore)
        ->and($item->refresh()->status)->toBe(ItemStatus::REPORTED);
});

test('draft cancelled and resolved reports cannot become candidates or verified links', function () {
    $employee = makeExplicitLinkEmployee(['lost_found.items.edit']);
    $student = makeExplicitLinkStudent();
    $category = makeExplicitLinkCategory();
    $item = makeExplicitLinkItem($employee, $category);
    $service = app(EmployeeReportItemLinkApplicationService::class);

    foreach ([ReportStatus::DRAFT, ReportStatus::CANCELLED, ReportStatus::RESOLVED] as $status) {
        $report = makeExplicitLinkReport($student, $category, $status);

        if ($status === ReportStatus::RESOLVED) {
            $report->resolved_found_item_id = $item->id;
            $report->save();
        }

        expect(fn () => $service->proposePotentialMatch($employee, $report->id, $item->id))
            ->toThrow(DomainException::class);
    }

    $active = makeExplicitLinkReport($student, $category);
    $potential = $service->proposePotentialMatch($employee, $active->id, $item->id);
    $active->status = ReportStatus::CANCELLED;
    $active->closed_at = now();
    $active->save();

    expect(fn () => $service->verifyPotentialMatch($employee, $potential->id, 'Stale verification attempt'))
        ->toThrow(DomainException::class)
        ->and(VerifiedReportItemLink::query()->count())->toBe(0);
});

test('verification database failure rolls back without changing candidate report or item', function () {
    $employee = makeExplicitLinkEmployee(['lost_found.items.edit']);
    $student = makeExplicitLinkStudent();
    $category = makeExplicitLinkCategory();
    $report = makeExplicitLinkReport($student, $category);
    $item = makeExplicitLinkItem($employee, $category);
    $service = app(EmployeeReportItemLinkApplicationService::class);
    $potential = $service->proposePotentialMatch($employee, $report->id, $item->id);
    $reportBefore = (array) DB::table('lost_found_reports')->find($report->id);
    $itemBefore = (array) DB::table('lost_found_items')->find($item->id);

    DB::unprepared(
        "CREATE TRIGGER verified_link_failure BEFORE INSERT ON lost_found_verified_links BEGIN SELECT RAISE(ABORT, 'forced verified link failure'); END",
    );

    try {
        expect(fn () => $service->verifyPotentialMatch($employee, $potential->id, 'Evidence that must roll back'))
            ->toThrow(QueryException::class);
    } finally {
        DB::unprepared('DROP TRIGGER IF EXISTS verified_link_failure');
    }

    expect(VerifiedReportItemLink::query()->count())->toBe(0)
        ->and(PotentialReportItemMatch::query()->whereKey($potential->id)->exists())->toBeTrue()
        ->and((array) DB::table('lost_found_reports')->find($report->id))->toBe($reportBefore)
        ->and((array) DB::table('lost_found_items')->find($item->id))->toBe($itemBefore);
});

test('relationship history is immutable and foreign keys preserve it', function () {
    $employee = makeExplicitLinkEmployee(['lost_found.items.edit']);
    $student = makeExplicitLinkStudent();
    $category = makeExplicitLinkCategory();
    $report = makeExplicitLinkReport($student, $category);
    $item = makeExplicitLinkItem($employee, $category);
    $service = app(EmployeeReportItemLinkApplicationService::class);
    $potential = $service->proposePotentialMatch($employee, $report->id, $item->id);
    $verified = $service->verifyPotentialMatch($employee, $potential->id, 'Immutable verification evidence');

    $verified->verification_evidence = 'Attempted rewrite';
    expect(fn () => $verified->save())->toThrow(LogicException::class);
    expect(fn () => $potential->delete())->toThrow(LogicException::class);
    expect(fn () => DB::table('lost_found_reports')->where('id', $report->id)->delete())
        ->toThrow(QueryException::class);
    expect(fn () => DB::table('lost_found_items')->where('id', $item->id)->delete())
        ->toThrow(QueryException::class);
});
