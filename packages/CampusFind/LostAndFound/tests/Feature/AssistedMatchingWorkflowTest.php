<?php

namespace CampusFind\LostAndFound\Tests\Feature;

use CampusFind\LostAndFound\Enums\ItemStatus;
use CampusFind\LostAndFound\Enums\MatchReviewDecision;
use CampusFind\LostAndFound\Enums\ReportStatus;
use CampusFind\LostAndFound\Models\FoundItem;
use CampusFind\LostAndFound\Models\LostFoundCategory;
use CampusFind\LostAndFound\Models\LostReport;
use CampusFind\LostAndFound\Models\MatchSuggestionSnapshot;
use CampusFind\LostAndFound\Models\PotentialReportItemMatch;
use CampusFind\LostAndFound\Services\Application\EmployeeAssistedMatchingService;
use CampusFind\LostAndFound\Services\Application\EmployeeReportItemLinkApplicationService;
use CampusFind\LostAndFound\Services\Matching\AssistedMatchScorer;
use CampusFind\LostAndFound\Services\Matching\MatchCandidateRetriever;
use CampusFind\LostAndFound\Tests\TestCase;
use CampusFind\Student\Models\Student;
use DomainException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;
use Webkul\User\Models\Role;
use Webkul\User\Models\User;

class AssistedMatchingWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    private LostFoundCategory $category;

    private User $employee;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('lost_found.matching.candidate_limit', 100);
        config()->set('lost_found.matching.result_limit', 25);
        $this->category = LostFoundCategory::create(['code' => 'match-'.Str::random(8), 'is_active' => true]);
        $this->employee = $this->employee([
            'lost_found.matches.view',
            'lost_found.matches.generate',
            'lost_found.matches.review',
            'lost_found.items.edit',
        ]);
    }

    public function test_exact_category_is_bounded_candidate_gate_and_different_category_is_excluded(): void
    {
        $report = $this->report();
        $matching = $this->item();
        $otherCategory = LostFoundCategory::create(['code' => 'other-'.Str::random(8), 'is_active' => true]);
        $this->item(['category_id' => $otherCategory->id]);

        $candidates = app(MatchCandidateRetriever::class)->forLostReport($report);

        $this->assertSame([$matching->id], $candidates->pluck('id')->all());
    }

    public function test_similarity_missing_data_location_and_dates_produce_explainable_non_probability_scores(): void
    {
        $report = $this->report([
            'title' => 'Black leather wallet',
            'public_description' => 'Black wallet with silver zip',
            'private_description' => 'Initials H A inside',
            'lost_location' => 'Central Library second floor',
            'lost_at' => now()->subDays(2),
        ]);
        $strong = $this->item([
            'title' => 'Black leather wallet',
            'public_description' => 'Black wallet silver zipper',
            'found_location' => 'Central Library 2nd floor',
            'found_at' => now()->subDay(),
        ]);
        $weak = $this->item([
            'title' => 'Unlabelled object',
            'public_description' => null,
            'found_location' => null,
            'found_at' => now()->subDays(5),
        ]);

        $scorer = app(AssistedMatchScorer::class);
        $strongScore = $scorer->score($report, $strong);
        $weakScore = $scorer->score($report, $weak);

        $this->assertGreaterThan($weakScore->scoreBasisPoints, $strongScore->scoreBasisPoints);
        $this->assertSame('category_match', $strongScore->signals['category']['explanation']);
        $this->assertSame('location_missing', $weakScore->signals['location']['explanation']);
        $this->assertSame('temporal_incompatible', $weakScore->signals['temporal']['explanation']);
        $this->assertArrayNotHasKey('probability', $strongScore->signals);
    }

    public function test_missing_optional_descriptions_are_handled_without_fabricated_similarity(): void
    {
        $report = $this->report([
            'title' => 'Wallet',
            'public_description' => null,
            'private_description' => null,
        ]);
        $item = $this->item([
            'title' => 'Umbrella',
            'public_description' => null,
        ]);

        $score = app(AssistedMatchScorer::class)->score($report, $item);

        $this->assertTrue($score->signals['description']['available']);
        $this->assertSame('description_weak', $score->signals['description']['explanation']);
        $this->assertGreaterThanOrEqual(0, $score->scoreBasisPoints);
    }

    public function test_ranking_is_deterministic_with_stable_identifier_tie_breaking(): void
    {
        $report = $this->report(['title' => 'Same title', 'lost_at' => null, 'lost_location' => null]);
        $first = $this->item(['title' => 'Same title', 'found_at' => null, 'found_location' => null]);
        $second = $this->item(['title' => 'Same title', 'found_at' => null, 'found_location' => null]);

        $this->service()->generateForLostReport($this->employee, $report->id);
        $ordered = PotentialReportItemMatch::query()
            ->where('lost_report_id', $report->id)
            ->orderBy('id')
            ->pluck('found_item_id')
            ->all();

        $this->assertSame([$first->id, $second->id], $ordered);
    }

    public function test_repeated_execution_is_idempotent_and_relevant_change_appends_refresh_snapshot(): void
    {
        $report = $this->report();
        $item = $this->item();

        $first = $this->service()->generateForLostReport($this->employee, $report->id);
        $second = $this->service()->generateForLostReport($this->employee, $report->id);
        $this->assertSame(1, $first['created']);
        $this->assertSame(1, $second['unchanged']);
        $this->assertDatabaseCount('lost_found_potential_matches', 1);
        $this->assertDatabaseCount('lost_found_match_snapshots', 1);

        $item->update(['public_description' => 'New meaningful description evidence']);
        $refreshed = $this->service()->generateForLostReport($this->employee, $report->id);
        $this->assertSame(1, $refreshed['refreshed']);
        $this->assertDatabaseCount('lost_found_match_snapshots', 2);
    }

    public function test_rejected_suggestion_is_terminal_not_recreated_or_verified(): void
    {
        $report = $this->report();
        $item = $this->item();
        $this->service()->generateForLostReport($this->employee, $report->id);
        $match = PotentialReportItemMatch::firstOrFail();
        $this->service()->review($this->employee, $match->id, MatchReviewDecision::REJECTED, 'Different serial fragment');

        $item->update(['public_description' => 'Changed after rejection']);
        $stats = $this->service()->generateForLostReport($this->employee, $report->id);
        $this->assertSame(1, $stats['rejected']);
        $this->assertDatabaseCount('lost_found_match_snapshots', 1);

        $this->expectException(DomainException::class);
        app(EmployeeReportItemLinkApplicationService::class)
            ->verifyPotentialMatch($this->employee, $match->id, 'Manual ownership evidence');
    }

    public function test_employee_review_is_audited_encrypted_and_does_not_resolve_or_handover(): void
    {
        $report = $this->report();
        $item = $this->item();
        $this->service()->generateForLostReport($this->employee, $report->id);
        $match = PotentialReportItemMatch::firstOrFail();
        $review = $this->service()->review($this->employee, $match->id, MatchReviewDecision::REVIEWED, 'Looks plausible');

        $this->assertSame(MatchReviewDecision::REVIEWED, $review->decision);
        $this->assertNotSame('Looks plausible', DB::table('lost_found_match_reviews')->value('notes'));
        $this->assertSame(ReportStatus::ACTIVE, $report->fresh()->status);
        $this->assertSame(ItemStatus::REPORTED, $item->fresh()->status);
        $this->assertDatabaseCount('lost_found_verified_links', 0);
        $this->assertDatabaseCount('lost_found_handovers', 0);
    }

    public function test_explicit_verification_reuses_phase_two_and_still_does_not_resolve_report(): void
    {
        $report = $this->report();
        $item = $this->item();
        $this->service()->generateForLostReport($this->employee, $report->id);
        $match = PotentialReportItemMatch::firstOrFail();

        $link = app(EmployeeReportItemLinkApplicationService::class)
            ->verifyPotentialMatch($this->employee, $match->id, 'Compared private distinguishing marks');

        $this->assertSame($match->id, $link->potential_match_id);
        $this->assertSame(ReportStatus::ACTIVE, $report->fresh()->status);
        $this->assertNull($report->resolved_found_item_id);
        $this->assertDatabaseCount('lost_found_handovers', 0);
    }

    public function test_authorized_employee_can_generate_review_and_verify_through_http_workflow(): void
    {
        $report = $this->report();
        $this->item();

        $this->actingAs($this->employee, 'user')
            ->post(route('admin.lost_found.matches.generate_report', $report->id))
            ->assertRedirect();

        $match = PotentialReportItemMatch::firstOrFail();
        $this->actingAs($this->employee, 'user')
            ->post(route('admin.lost_found.matches.review', $match->id), [
                'decision' => MatchReviewDecision::REVIEWED->value,
                'notes' => 'Compared the permitted details',
            ])
            ->assertRedirect();
        $this->assertDatabaseCount('lost_found_match_reviews', 1);

        $this->actingAs($this->employee, 'user')
            ->post(route('admin.lost_found.matches.verify', $match->id), [
                'verification_evidence' => 'Separate private ownership evidence checked',
            ])
            ->assertRedirect();

        $this->assertDatabaseCount('lost_found_verified_links', 1);
        $this->assertSame(ReportStatus::ACTIVE, $report->fresh()->status);
        $this->assertDatabaseCount('lost_found_handovers', 0);
    }

    public function test_unauthorized_employee_cannot_generate_review_or_view_private_matching_page(): void
    {
        $report = $this->report();
        $this->item();
        $unauthorized = $this->employee([]);

        try {
            $this->service()->generateForLostReport($unauthorized, $report->id);
            $this->fail('Generation must be authorized.');
        } catch (AuthorizationException) {
            $this->assertDatabaseCount('lost_found_potential_matches', 0);
        }

        $this->service()->generateForLostReport($this->employee, $report->id);
        $match = PotentialReportItemMatch::firstOrFail();

        try {
            $this->service()->review($unauthorized, $match->id, MatchReviewDecision::REVIEWED);
            $this->fail('Review must be authorized.');
        } catch (AuthorizationException) {
            $this->assertDatabaseCount('lost_found_match_reviews', 0);
        }

        $response = $this->actingAs($unauthorized, 'user')
            ->get(route('admin.lost_found.matches.index'));
        $this->assertContains($response->getStatusCode(), [302, 403]);
    }

    public function test_private_inputs_affect_fingerprint_but_never_enter_snapshot_or_employee_html(): void
    {
        $privateSecret = 'PRIVATE-MATCH-SECRET-'.Str::random(12);
        $report = $this->report(['private_description' => $privateSecret]);
        $this->item();
        $this->service()->generateForLostReport($this->employee, $report->id);
        $snapshot = MatchSuggestionSnapshot::firstOrFail();

        $this->assertStringNotContainsString($privateSecret, json_encode($snapshot->signals, JSON_THROW_ON_ERROR));
        $this->assertStringNotContainsString($privateSecret, (string) DB::table('lost_found_match_snapshots')->value('signals'));
        $this->actingAs($this->employee, 'user')
            ->get(route('admin.lost_found.matches.index'))
            ->assertOk()
            ->assertDontSee($privateSecret);
    }

    public function test_reverse_generation_from_found_item_is_supported(): void
    {
        $report = $this->report();
        $item = $this->item();

        $stats = $this->service()->generateForFoundItem($this->employee, $item->id);

        $this->assertSame(1, $stats['created']);
        $this->assertDatabaseHas('lost_found_potential_matches', [
            'lost_report_id' => $report->id,
            'found_item_id' => $item->id,
        ]);
    }

    public function test_transaction_failure_rolls_back_candidate_and_snapshot(): void
    {
        $report = $this->report();
        $this->item();
        MatchSuggestionSnapshot::creating(static function (): void {
            throw new RuntimeException('Injected snapshot failure');
        });

        try {
            $this->service()->generateForLostReport($this->employee, $report->id);
            $this->fail('Injected failure should escape.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Injected snapshot failure', $exception->getMessage());
        }

        $this->assertDatabaseCount('lost_found_potential_matches', 0);
        $this->assertDatabaseCount('lost_found_match_snapshots', 0);
    }

    public function test_candidate_retrieval_query_count_and_memory_are_bounded(): void
    {
        config()->set('lost_found.matching.candidate_limit', 40);
        $report = $this->report([
            'public_description' => null,
            'lost_location' => 'Main Library',
        ]);
        foreach (range(1, 120) as $index) {
            $this->item([
                'title' => $index % 3 === 0 ? 'Similar black backpack' : "Unrelated candidate {$index}",
                'public_description' => $index % 5 === 0 ? null : "Candidate description {$index}",
                'found_location' => $index % 4 === 0 ? 'Main Library' : 'Student Center',
                'found_at' => now()->subMinutes($index),
            ]);
        }

        $unrelatedCategory = LostFoundCategory::create([
            'code' => 'perf-other-'.Str::random(8),
            'is_active' => true,
        ]);
        foreach (range(1, 30) as $index) {
            $this->item([
                'category_id' => $unrelatedCategory->id,
                'title' => "Different category {$index}",
                'found_location' => 'Main Library',
                'found_at' => now()->subDays($index),
            ]);
        }

        DB::flushQueryLog();
        DB::enableQueryLog();
        $memoryBefore = memory_get_usage(true);
        $started = hrtime(true);
        $candidates = app(MatchCandidateRetriever::class)->forLostReport($report);
        $retrievalMs = (hrtime(true) - $started) / 1_000_000;
        $rankingStarted = hrtime(true);
        $ranked = $candidates
            ->map(fn (FoundItem $item) => app(AssistedMatchScorer::class)->score($report, $item))
            ->sortByDesc('scoreBasisPoints');
        $rankingMs = (hrtime(true) - $rankingStarted) / 1_000_000;
        $memoryDelta = memory_get_usage(true) - $memoryBefore;
        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        fwrite(STDOUT, sprintf(
            "\nPHASE06_PERF fixtures=150 candidates=%d queries=%d retrieval_ms=%.3f ranking_ms=%.3f memory_delta_bytes=%d\n",
            $candidates->count(),
            $queryCount,
            $retrievalMs,
            $rankingMs,
            $memoryDelta,
        ));

        $this->assertCount(40, $candidates);
        $this->assertCount(40, $ranked);
        $this->assertLessThanOrEqual(2, $queryCount);
        $this->assertLessThan(128 * 1024 * 1024, $memoryDelta);
        $this->assertLessThan(2_000, $retrievalMs);
        $this->assertLessThan(2_000, $rankingMs);
    }

    private function service(): EmployeeAssistedMatchingService
    {
        return app(EmployeeAssistedMatchingService::class);
    }

    private function report(array $overrides = []): LostReport
    {
        $student = Student::create([
            'university_card_number' => 'MATCH-'.Str::random(10),
            'password' => Hash::make(Str::random(20)),
            'name' => 'Matching Student',
        ]);

        return LostReport::create(array_merge([
            'public_reference' => 'LM-'.Str::random(10),
            'student_id' => $student->id,
            'category_id' => $this->category->id,
            'status' => ReportStatus::ACTIVE,
            'title' => 'Black backpack with laptop sleeve',
            'public_description' => 'Black canvas backpack',
            'private_description' => 'Small stitched initials inside',
            'lost_location' => 'Main Library',
            'lost_at' => now()->subDays(2),
            'submitted_at' => now(),
        ], $overrides));
    }

    private function item(array $overrides = []): FoundItem
    {
        return FoundItem::create(array_merge([
            'public_reference' => 'FM-'.Str::random(10),
            'category_id' => $this->category->id,
            'logged_by_user_id' => $this->employee->id,
            'status' => ItemStatus::REPORTED,
            'title' => 'Black canvas backpack',
            'public_description' => 'Backpack with padded laptop sleeve',
            'found_location' => 'Library entrance',
            'found_at' => now()->subDay(),
            'reported_at' => now(),
        ], $overrides));
    }

    /** @param array<int, string> $permissions */
    private function employee(array $permissions): User
    {
        $role = Role::create([
            'name' => 'Matching Role '.Str::random(8),
            'permission_type' => 'custom',
            'permissions' => $permissions,
        ]);

        return User::create([
            'name' => 'Matching Employee',
            'email' => Str::random(10).'@example.test',
            'password' => Hash::make(Str::random(20)),
            'status' => true,
            'role_id' => $role->id,
        ]);
    }
}
