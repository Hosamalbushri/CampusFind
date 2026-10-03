# Testing & Verification Guide

## 1. Running Package Tests

The test suite runs with Pest and PHPUnit on SQLite in-memory databases with foreign keys enabled.

### Run LostAndFound Tests:
```bash
LARASEED_OPTIONAL_PACKAGES=student,lost_and_found vendor/bin/pest packages/CampusFind/LostAndFound/tests
```

### Run Full Suite (Student + LostAndFound):
```bash
LARASEED_OPTIONAL_PACKAGES=student,lost_and_found vendor/bin/pest packages/CampusFind/Student/tests packages/CampusFind/LostAndFound/tests
```

---

## 2. Test Suite Composition

| Test Suite | File | Tests | Coverage Scope |
|---|---|---|---|
| **Unit** | `ClaimStateMachineTest.php` | 2 | Claim state transitions & invalid state prevention |
| **Unit** | `ItemStateMachineTest.php` | 3 | Item state transitions & terminal states |
| **Unit** | `PublicReferenceTest.php` | 3 | Reference generation & case-insensitive normalization |
| **Unit** | `ReportStateMachineTest.php` | 2 | Report state machine & transitions |
| **Unit** | `SecurityInvariantsTest.php` | 3 | Secret prohibition & handover eligibility checks |
| **Feature** | `EmployeeClaimHttpTest.php` | 20 | Staff claim review, approval, rejection, and revocation |
| **Feature** | `EmployeeClaimReadTest.php` | 11 | Claim grids, authorization bounds, and data isolation |
| **Feature** | `EmployeeCustodyHttpTest.php` | 17 | Custody logging, transfer, and storage relocation |
| **Feature** | `EmployeeFoundItemHttpTest.php` | 14 | Found item creation, updating, and image uploads |
| **Feature** | `EmployeeFoundItemReadTest.php` | 14 | Staff items grid, filtering, and authorization bounds |
| **Feature** | `EmployeeHandoverHttpTest.php` | 22 | Physical handover execution, verification, and terminal logic |
| **Feature** | `FoundItemImageTest.php` | 15 | Public-safe and staff-only image pipelines and compensation |
| **Feature** | `LostAndFoundAuthorizationTest.php` | 16 | Granular permission gates and cross-role boundary tests |
| **Feature** | `LostAndFoundClaimEvidenceImageTest.php` | 26 | Private evidence image sanitization, GD encoding, and rollback |
| **Feature** | `LostAndFoundClaimPersistenceTest.php` | 16 | Claim persistence, winner uniqueness, and review history |
| **Feature** | `LostAndFoundCustodyPersistenceTest.php` | 16 | Custody projection consistency, history immutability |
| **Feature** | `LostAndFoundHandoverPersistenceTest.php` | 17 | Handover atomicity, recipient validation, and release logic |
| **Feature** | `LostAndFoundPersistenceTest.php` | 15 | FoundItem & LostReport repositories, protected fields |
| **Feature** | `LostReportImageTest.php` | 19 | Private student report image uploads and immutability |
| **Feature** | `PackageOwnershipTest.php` | 4 | ACL merging, route registration, and 7-locale translation parity |
| **Feature** | `PublicLostAndFoundReadTest.php` | 9 | Public contract methods, data clamping, and entity redaction |
| **Feature** | `PublicLostAndFoundSearchAndDetailTest.php` | 12 | Public search filtering, pagination, and SQL injection safety |
| **Feature** | `StudentClaimHttpTest.php` | 17 | Student claim submission, evidence uploading, and withdrawal |
| **Feature** | `StudentLostReportHttpTest.php` | 12 | Student report creation, updates, and image uploads |
| **Total** | **24 Test Files** | **309 Tests** | **100% Passing** |
