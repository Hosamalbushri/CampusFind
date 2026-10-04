# 09. Test Coverage Audit and Executable Regression Test Plan

## 1. Finding Overview

| Attribute | Details |
|---|---|
| **Finding Identifier** | `FINDING-LF-09` |
| **Audit Focus** | Audit 09 — Test Suite Coverage & Regression Plan |
| **Verification Status** | **VERIFIED TEST GAP** |
| **Severity** | **MEDIUM** |
| **Target Implementation Phase** | Phase 01 / Phase 02 / Phase 03 / Phase 04 |

---

## 2. Comprehensive Test Suite Audit

The actual test execution across all packages was executed on SQLite in-memory databases with foreign keys enabled:

```bash
LARASEED_OPTIONAL_PACKAGES=student,lost_and_found,web vendor/bin/pest packages/CampusFind/LostAndFound/tests packages/CampusFind/Student/tests packages/CampusFind/Web/tests
```

**Audit Execution Result**:
- **Total Tests Passed**: **373 tests** (2,158 assertions).
- **Execution Time**: ~31.2 seconds.
- **Failures / Errors**: 0 failures.

### 2.1. Breakdown by Package and Suite

| Package | Test Directory | Files | Tests | Assertions | Focus Areas |
|---|---|---|---|---|---|
| **CampusFind LostAndFound** | `packages/CampusFind/LostAndFound/tests` | 24 | 309 | 1,793 | Unit state machines, claims, custody, handover, image sanitization, encryption, HTTP ACL |
| **CampusFind Student** | `packages/CampusFind/Student/tests` | 5 | 40 | 214 | Student authentication, University API client, package isolation, security |
| **CampusFind Web** | `packages/CampusFind/Web/tests` | 2 | 24 | 151 | Web views, catalog browsing, guest redirects, student login, locale parity |
| **Total** | | **31** | **373** | **2,158** | **100% Passing** |

---

## 3. Business Rule Coverage Analysis (Covered vs Uncovered)

| Business Rule / Invariant | Covered in Test Suite? | Evidence Test File |
|---|---|---|
| **Item State Machine Validity** | **YES** | `ItemStateMachineTest.php` |
| **Claim State Machine Validity** | **YES** | `ClaimStateMachineTest.php` |
| **Report State Machine Validity** | **YES** | `ReportStateMachineTest.php` |
| **Secret Keywords Prohibition (Multilingual)** | **YES** | `SecurityInvariantsTest.php` |
| **Pessimistic Locking & Handover Atomicity** | **YES** | `LostAndFoundHandoverPersistenceTest.php` |
| **Duplicate Handover Prevention** | **YES** | `LostAndFoundHandoverPersistenceTest.php` |
| **Competing Claims Automatic Rejection** | **YES** | `LostAndFoundClaimPersistenceTest.php` |
| **Dual-Entry Custody Projection Verification** | **YES** | `LostAndFoundCustodyPersistenceTest.php` |
| **Anti-Polyglot Raster Image Sanitization** | **YES** | `LostAndFoundClaimEvidenceImageTest.php` |
| **AES-256 Eloquent Attribute Encryption** | **YES** | `LostAndFoundPersistenceTest.php` |
| **Student Ownership Authorization Gates** | **YES** | `LostAndFoundAuthorizationTest.php` |
| **Cross-Student Evidence Existence Hiding (404)** | **YES** | `StudentClaimHttpTest.php` |
| **Handover Closing Unrelated Lost Reports** | **NO (UNCOVERED GAP)** | *No tests asserted LostReport count/status after handover* |
| **Handover Prematurely Resolving Drafts** | **NO (UNCOVERED GAP)** | *No tests created draft reports during handover* |
| **Unified Catalog Displaying Both Lost & Found** | **NO (UNCOVERED GAP)** | *Catalog tests only queried FoundItem* |
| **Found-Item Response Workflow ("I Found This")** | **NO (UNCOVERED GAP)** | *Feature does not exist in codebase* |

---

## 4. Executable Regression Test Specifications

The following tests must be implemented as permanent regression protections during the implementation phases.

### 4.1. Test Suite 1: Handover Report Isolation (Phase 01)
Target File: `packages/CampusFind/LostAndFound/tests/Feature/LostAndFoundHandoverPersistenceTest.php`

```php
test('handover does not close unrelated lost reports belonging to recipient in same category', function () {
    [$item, $claim, $student, $staff] = makeApprovedHandoverFixture();
    
    // Create an unrelated active lost report in the same category
    $unrelatedReport = LostReport::create([
        'student_id' => $student->id,
        'category_id' => $item->category_id,
        'status' => ReportStatus::ACTIVE,
        'title' => 'Unrelated Missing Laptop',
        'public_reference' => 'LR-UNRELATED-01',
    ]);
    
    // Create an unsubmitted draft report in the same category
    $draftReport = LostReport::create([
        'student_id' => $student->id,
        'category_id' => $item->category_id,
        'status' => ReportStatus::DRAFT,
        'title' => 'Draft Missing Item',
        'public_reference' => 'LR-DRAFT-01',
    ]);
    
    completeHandover($item, $claim, $student, $staff);
    
    $unrelatedReport->refresh();
    $draftReport->refresh();
    
    expect($unrelatedReport->status)->toBe(ReportStatus::ACTIVE)
        ->and($unrelatedReport->resolved_found_item_id)->toBeNull()
        ->and($draftReport->status)->toBe(ReportStatus::DRAFT)
        ->and($draftReport->resolved_found_item_id)->toBeNull();
});
```

### 4.2. Test Suite 2: Explicit Report Linking (Phase 02)
Target File: `packages/CampusFind/LostAndFound/tests/Feature/LostAndFoundHandoverPersistenceTest.php`

```php
test('handover resolves only the explicitly linked lost report attached to the approved claim', function () {
    [$item, $claim, $student, $staff] = makeApprovedHandoverFixture();
    
    $linkedReport = LostReport::create([
        'student_id' => $student->id,
        'category_id' => $item->category_id,
        'status' => ReportStatus::ACTIVE,
        'title' => 'Explicitly Linked Lost Item',
        'public_reference' => 'LR-LINKED-01',
    ]);
    
    // Attach explicit link to claim
    $claim->update(['lost_report_id' => $linkedReport->id]);
    
    completeHandover($item, $claim, $student, $staff);
    
    $linkedReport->refresh();
    expect($linkedReport->status)->toBe(ReportStatus::RESOLVED)
        ->and($linkedReport->resolved_found_item_id)->toBe($item->id)
        ->and($linkedReport->closed_at)->not->toBeNull();
});
```

### 4.3. Test Suite 3: Unified Browsing & Mixed Querying (Phase 03)
Target File: `packages/CampusFind/Web/tests/Feature/Web/WebPageTest.php`

```php
test('unified catalog renders both lost reports and found items side-by-side', function () {
    $category = LostFoundCategory::create(['code' => 'electronics', 'is_active' => true]);
    $student = Student::create(['university_card_number' => 'U12345', 'name' => 'Alice', 'password' => 'pass']);
    $user = User::create(['name' => 'Staff', 'email' => 'staff@test.com', 'password' => 'pass', 'role_id' => 1, 'status' => 1]);
    
    // Create active LostReport
    LostReport::create([
        'student_id' => $student->id,
        'category_id' => $category->id,
        'status' => ReportStatus::ACTIVE,
        'title' => 'Lost iPad Pro',
        'public_reference' => 'LR-IPAD-01',
    ]);
    
    // Create reported FoundItem
    FoundItem::create([
        'logged_by_user_id' => $user->id,
        'category_id' => $category->id,
        'status' => ItemStatus::REPORTED,
        'title' => 'Found Blue Umbrella',
        'public_reference' => 'LF-UMB-01',
    ]);
    
    $response = $this->get(route('campusfind_web.web.items.index'));
    $response->assertStatus(200)
        ->assertSee('Lost iPad Pro')
        ->assertSee('Found Blue Umbrella')
        ->assertSee(trans('campusfind_web_web::app.web.actions.i_found_this'))
        ->assertSee(trans('campusfind_web_web::app.web.actions.claim_ownership'));
});
```

### 4.4. Test Suite 4: Found-Item Response Lifecycle (Phase 04)
Target File: `packages/CampusFind/LostAndFound/tests/Feature/StudentReportResponseHttpTest.php`

```php
test('submitting found-item response leaves lost report in active status', function () {
    $studentOwner = makeStudent();
    $studentFinder = makeStudent();
    $category = makeCategory();
    
    $report = LostReport::create([
        'student_id' => $studentOwner->id,
        'category_id' => $category->id,
        'status' => ReportStatus::ACTIVE,
        'title' => 'Lost Scientific Calculator',
        'public_reference' => 'LR-CALC-01',
    ]);
    
    $response = $this->actingAs($studentFinder, 'student')
        ->postJson(route('student.lost_found.reports.response.store', $report->id), [
            'found_location' => 'Library 2nd Floor',
            'dropoff_location' => 'Library Front Desk',
            'message' => 'Handed to desk librarian.',
        ]);
        
    $response->assertStatus(201);
    
    $report->refresh();
    expect($report->status)->toBe(ReportStatus::ACTIVE)
        ->and($report->responses()->count())->toBe(1);
});
```

---

## 5. Acceptance Invariant for Testing Phase

- [ ] Zero assertions may be weakened or skipped to achieve passing tests.
- [ ] New tests must explicitly verify negative invariants (unrelated reports remain open, draft reports are untouched).
- [ ] Full suite across all 3 packages must maintain 100% green passing status.
