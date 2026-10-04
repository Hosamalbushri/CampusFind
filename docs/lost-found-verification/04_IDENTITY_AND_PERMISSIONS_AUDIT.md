# 04. Identity and Permissions Audit — Actors, Roles, Guards, and ACL Matrix

## 1. Finding Overview

| Attribute | Details |
|---|---|
| **Finding Identifier** | `FINDING-LF-06` |
| **Audit Focus** | Audit 04 — Identity, Guards, and Authorization Boundaries |
| **Verification Status** | **VERIFIED GAPS & PARTIAL IMPLEMENTATIONS** |
| **Severity** | **MEDIUM** |
| **Target Implementation Phase** | Phase 04 / Phase 05 |

---

## 2. Relevant Business Requirement

1. **Strict Role Separation**:
   The system must cleanly differentiate four operational actor classes:
   - **Public Visitors (Guests)**: Unauthenticated anonymous campus visitors.
   - **Students**: Authenticated campus students via guard `'student'`.
   - **Lost & Found Employees**: Institutional staff members authenticated via guard `'user'` with granular ACL permissions.
   - **System Administrators**: Superusers authenticated via guard `'user'` with full permissions (`role->permission_type === 'all'`).
2. **Actor Responsibilities and Operation Invariants**:
   - Only authenticated students may file `LostReport` records with private ownership details or submit `LostFoundClaim` ownership claims.
   - Only authorized employees with appropriate ACL gates may register found items, record custody transfers, review claims, inspect private evidence files, and execute physical handovers.
   - Public visitors may browse public listings and report physical drop-offs without gaining access to confidential student identity or staff notes.
   - Neither students nor public visitors may ever execute administrative state transitions.

---

## 3. Actual Implementation and Source-Code Evidence

### 3.1. Authentication Guards and Provider Topology
- **Student Guard**: Configured in `packages/CampusFind/Student/src/Config/auth.php`:
  - Guard: `'student'` (Driver: `session`, Provider: `students`).
  - Model: [`CampusFind\Student\Models\Student`](file:///home/hosam/Documents/compusfund/packages/CampusFind/Student/src/Models/Student.php).
  - External Authentication: First login authenticated via University API ([`UniversityStudentApiClient`](file:///home/hosam/Documents/compusfund/packages/CampusFind/Student/src/Services/UniversityStudentApiClient.php)); subsequent logins verified against local bcrypt password.
- **Staff / Admin Guard**: Configured in Laraseed Foundation:
  - Guard: `'user'` (Driver: `session`, Provider: `users`).
  - Model: `Webkul\User\Models\User`.

### 3.2. Granular ACL Configuration
Configured in [`packages/CampusFind/LostAndFound/src/Config/acl.php`](file:///home/hosam/Documents/compusfund/packages/CampusFind/LostAndFound/src/Config/acl.php#L4-L92):

```php
// ACL tree registered in acl.php:
'lost_found'                      // Root menu access
'lost_found.items'                // Items menu header
'lost_found.items.view'           // View items & reports grids
'lost_found.items.create'         // Create new found items
'lost_found.items.edit'           // Update found items & approve/reject lost reports
'lost_found.claims'               // Claims menu header
'lost_found.claims.view'          // View claims & download evidence files
'lost_found.claims.review'        // Review claims (under_review, needs_info)
'lost_found.claims.approve'       // Approve claims & revoke approval
'lost_found.claims.reject'        // Reject claims
'lost_found.custody'              // Custody menu header
'lost_found.custody.manage'       // Receive into custody, transfer custodian, move shelf
'lost_found.handover'             // Handover menu header
'lost_found.handover.complete'    // Execute physical handover & item release
'lost_found.settings'             // Settings root
'lost_found.settings.categories'  // Manage category records
```

### 3.3. Student Ownership Enforcement
Implemented in [`LostAndFoundAuthorization::authorizeStudentOwnership`](file:///home/hosam/Documents/compusfund/packages/CampusFind/LostAndFound/src/Services/Application/LostAndFoundAuthorization.php#L32-L37):
```php
public static function authorizeStudentOwnership(Student $actor, int $ownerId, string $resourceName = 'Resource'): void
{
    if ((int) $actor->id !== (int) $ownerId) {
        throw new AuthorizationException("Student {$actor->id} is not the owner of this {$resourceName}.");
    }
}
```
All student modification routes (`PUT /student/lost-found/reports/{id}`, `POST .../images`, `POST .../evidence`, `POST .../withdraw`) strictly invoke this ownership check.

---

## 4. Comprehensive Permission and Capability Matrix

| Operation / Capability | Public Visitor | Student Owner | Non-Owner Student | L&F Staff | System Admin |
|---|---|---|---|---|---|
| **Browse Public Items (`/items`)** | **ALLOWED** | **ALLOWED** | **ALLOWED** | **ALLOWED** | **ALLOWED** |
| **View Public Item Details** | **ALLOWED** | **ALLOWED** | **ALLOWED** | **ALLOWED** | **ALLOWED** |
| **Publish Lost Report** | **REDIRECT TO LOGIN** | **ALLOWED** | **ALLOWED** | **ALLOWED** (on behalf) | **ALLOWED** |
| **Edit Lost Report** | **DENIED** | **ALLOWED** (`draft` only) | **DENIED (404/403)** | **DENIED** (only status) | **ALLOWED** |
| **Upload Lost Report Image** | **DENIED** | **ALLOWED** | **DENIED (404/403)** | **DENIED** | **ALLOWED** |
| **Submit Citizen Drop-off** | **ALLOWED** | **ALLOWED** | **ALLOWED** | **ALLOWED** | **ALLOWED** |
| **Submit Found Response ("I Found This")** | *[Target: Open Intake]* | *[Target: Auth]* | *[Target: Auth]* | *[Target: Auth]* | *[Target: Auth]* |
| **Submit Ownership Claim** | **REDIRECT TO LOGIN** | **N/A** (Cannot claim own) | **ALLOWED** | **DENIED** (Staff role) | **DENIED** |
| **Upload Claim Evidence** | **DENIED** | **ALLOWED** (Own claim) | **DENIED (404/403)** | **DENIED** | **ALLOWED** |
| **Withdraw Own Claim** | **DENIED** | **ALLOWED** (Own claim) | **DENIED (404/403)** | **DENIED** | **ALLOWED** |
| **Review / Request Info on Claim** | **DENIED** | **DENIED** | **DENIED** | **ALLOWED** (`claims.review`) | **ALLOWED** |
| **Approve / Reject Claim** | **DENIED** | **DENIED** | **DENIED** | **ALLOWED** (`claims.approve`/`reject`) | **ALLOWED** |
| **Revoke Claim Approval** | **DENIED** | **DENIED** | **DENIED** | **ALLOWED** (`claims.approve`) | **ALLOWED** |
| **Receive Custody into Storage** | **DENIED** | **DENIED** | **DENIED** | **ALLOWED** (`custody.manage`) | **ALLOWED** |
| **Transfer Custody / Move Shelf** | **DENIED** | **DENIED** | **DENIED** | **ALLOWED** (`custody.manage`) | **ALLOWED** |
| **Download Private Evidence File** | **DENIED** | **DENIED** (Views own) | **DENIED** | **ALLOWED** (`claims.view`) | **ALLOWED** |
| **Complete Physical Handover** | **DENIED** | **DENIED** | **DENIED** | **ALLOWED** (`handover.complete`) | **ALLOWED** |
| **Manage Item Categories** | **DENIED** | **DENIED** | **DENIED** | **DENIED** | **ALLOWED** (`settings.categories`) |

---

## 5. Identified Authorization Gaps and Ambiguities

1. **Citizen Drop-Off Pseudo-User Creation**:
   In `ReportController@storeFound` (lines 152–164), when an unauthenticated citizen reports a drop-off, the controller queries or auto-creates a user named `"System Intake"` in the `users` table. This injects dummy records into the staff users table, blurring audit accountability.
   *Resolution*: Use a nullable `logged_by_user_id` on `lost_found_items` for citizen intake, or populate when staff formally accepts custody.
2. **Public Invisibility of Lost Reports**:
   Public visitors cannot see `LostReport` records on `/items` because `PublicLostAndFoundService` only queries `FoundItem`. Public read access to sanitized `LostReport` records is required for unified browsing.
3. **Missing Authorization Gates for Found-Item Responses**:
   Because found-item responses are currently missing, no ACL permission keys (e.g. `lost_found.responses.view`, `lost_found.responses.review`) or ownership checks exist.

---

## 6. Proposed Corrections

1. **Add Response ACL Keys**:
   Register `lost_found.responses` in `packages/CampusFind/LostAndFound/src/Config/acl.php` under `lost_found` hierarchy.
2. **Refactor Citizen Intake Logging**:
   Ensure open drop-offs do not create synthetic employee users in `users` table; instead, set `logged_by_user_id` to `NULL` until an employee claims intake via `admin.lost_found.items.approve` or `custody.receive`.
3. **Expose Public-Safe Lost Reports**:
   Provide sanitized public read view of `LostReport` records without exposing `private_description`, student university card number, or student contact details.

---

## 7. Dependencies

- `acl.php` configuration in `CampusFind\LostAndFound`.
- `LostAndFoundAuthorization` helper class.
- `PublicLostAndFoundService` read boundaries.

---

## 8. Required Automated Tests

1. `test_public_guest_cannot_access_private_evidence_files()`:
   - Ensure `GET /admin/lost-found/claims/{id}/evidence/{evidenceId}/file` returns 401/302 for unauthenticated visitors and students.
2. `test_employee_without_custody_permission_cannot_transfer_custody()`:
   - Verify staff role without `lost_found.custody.manage` gets 403.
3. `test_student_cannot_view_or_modify_other_students_claim_evidence()`:
   - Verify 404 existence-hiding when student attempts cross-account evidence access.

---

## 9. Acceptance Criteria

- [ ] Clear separation between student, employee, admin, and guest operations.
- [ ] No synthetic staff user auto-created during citizen reporting.
- [ ] Granular ACL keys enforced across all administrative routes.
- [ ] 100% of authorization feature tests pass.
