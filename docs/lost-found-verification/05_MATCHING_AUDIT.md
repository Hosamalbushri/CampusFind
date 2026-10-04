# 05. Matching Rules and Similarity Audit — Forensic Evaluation of Matching Capabilities

## 1. Finding Overview

| Attribute | Details |
|---|---|
| **Finding Identifier** | `FINDING-LF-07` |
| **Audit Focus** | Audit 05 — Matching Engine, Rules, and Threshold Analysis |
| **Verification Status** | **VERIFIED UNIMPLEMENTED / SPECULATIVE DOCUMENTATION** |
| **Severity** | **LOW (INFORMATIONAL / FUTURE SCOPE)** |
| **Target Implementation Phase** | Phase 06 (Assisted Matching Suggestions) |

---

## 2. Relevant Business Requirement

1. **Assisted Matching as a Suggestion Tool Only**:
   Matching between **LOST reports** and **FOUND items** must serve solely as an assisted suggestion mechanism to help students discover candidate items and assist staff during intake.
2. **Never an Automatic Substitute for Ownership Verification**:
   A high similarity score (e.g., matching keywords, same building, same category) must **never** be treated as proof of ownership, must **never** automatically establish a claim, and must **never** trigger automatic handover or report resolution.
3. **No Arbitrary Hardcoded Thresholds Without Business Policy**:
   Matching scoring weights and notification thresholds must be configurable and based on approved campus operational policies, rather than arbitrary mathematical formulas invented in documentation.

---

## 3. Actual Implementation and Source-Code Evidence

### 3.1. Forensic Verification of Matching in Codebase
A comprehensive inspection across `packages/CampusFind/LostAndFound`, `packages/CampusFind/Student`, and `packages/CampusFind/Web` establishes:

1. **Zero Matching Services or Algorithms**:
   There is no matching class, similarity calculator, Levenshtein/cosine distance function, or text analysis engine in the repository.
2. **Explicit Test Confirmation of Non-Existence**:
   In existing test files, the absence of matching tables is explicitly asserted:
   - [`LostAndFoundHandoverPersistenceTest.php#L117`](file:///home/hosam/Documents/compusfund/packages/CampusFind/LostAndFound/tests/Feature/LostAndFoundHandoverPersistenceTest.php#L117):
     ```php
     expect(Schema::hasTable('potential_matches'))->toBeFalse();
     ```
   - [`LostAndFoundCustodyPersistenceTest.php#L80`](file:///home/hosam/Documents/compusfund/packages/CampusFind/LostAndFound/tests/Feature/LostAndFoundCustodyPersistenceTest.php#L80):
     ```php
     expect(Schema::hasTable('potential_matches'))->toBeFalse();
     ```
   - [`LostAndFoundClaimPersistenceTest.php#L110`](file:///home/hosam/Documents/compusfund/packages/CampusFind/LostAndFound/tests/Feature/LostAndFoundClaimPersistenceTest.php#L110):
     ```php
     expect(Schema::hasTable('potential_matches'))->toBeFalse();
     ```
3. **Only Match-Related Code is Post-Handover Category Hook**:
   The word `"matching"` only appears in comments above the flawed heuristic handover resolution hook in [`HandoverService.php#L162`](file:///home/hosam/Documents/compusfund/packages/CampusFind/LostAndFound/src/Services/HandoverService.php#L162), which blindly matches on `category_id` and `student_id`.

---

## 4. Forensic Investigation of Conflicting Documentation Thresholds

In the prior analysis documents (`docs/lost-found-analysis/`), conflicting claims were made regarding matching thresholds:
- **Document 10 (`10_IMPLEMENTATION_ROADMAP.md#L111`)**:
  > *"Set high confidence threshold (>60%) before triggering automated notifications."*
- **Document 11 (`11_UNIFIED_REPORTS_AND_DUAL_INTERACTIONS.md#L64`)**:
  > *"MatchingEngine -- Score >= 80% --> MatchCandidate"*

### Forensic Determination:
These formulas and percentages (60%, 80%, category 40%, location 20%, etc.) are **purely speculative documentation proposals**. They do not exist in the codebase, are not supported by any university policy document, and were never approved by stakeholders.
Implementing arbitrary hardcoded thresholds without policy approval risks generating excessive false-positive spam or silently ignoring valid matching items.

---

## 5. Architectural Guidelines for Future Matching Implementation

When matching is introduced in Phase 06, it must adhere to the following invariants:

1. **Non-Intrusive Suggestion Architecture**:
   - Matching suggestions must be calculated on-demand or via queued background jobs.
   - Suggestions must appear in a dedicated "Suggested Matches" tab in the Admin and Student views.
2. **Explicit Verification Prerequisite**:
   - Clicking a suggested match must prompt the student to review the found item and submit a formal claim with private evidence.
   - Staff must independently evaluate evidence before approving.
3. **Multi-Factor Criteria**:
   - Category code equality.
   - Temporal proximity (e.g. lost date $\le$ found date within a configurable window).
   - Location proximity (e.g. campus zone / building code).
   - Text similarity on public title and sanitized description.

---

## 6. Dependencies

- **Unified Public Catalog (Phase 03)**: Search queries and DTOs.
- **Configurable Settings**: Matching thresholds defined in `config/lost_found.php` rather than hardcoded in services.

---

## 7. Required Automated Tests (for Phase 06)

1. `test_matching_engine_calculates_similarity_score_between_lost_and_found_items()`:
   - Verify multi-factor scoring against known fixtures.
2. `test_suggested_match_never_alters_report_or_item_status()`:
   - Ensure generating a match candidate leaves `status` of both entities unchanged.
3. `test_dismissing_match_suggestion_preserves_independent_lifecycles()`:
   - Dismissing a false positive match has zero side-effects on reports.

---

## 8. Acceptance Criteria

- [ ] Clear documentation that matching is currently unbuilt and not required for core transactional integrity.
- [ ] No arbitrary hardcoded threshold numbers committed without configuration backing.
- [ ] Future matching architecture defined strictly as an assisted human-in-the-loop suggestion tool.
