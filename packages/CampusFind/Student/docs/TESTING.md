# Testing Guide

The Student package includes a complete Pest test suite located in `packages/CampusFind/Student/tests/`.

## Running Tests

### Run Student Package Tests:
```bash
LARASEED_OPTIONAL_PACKAGES=student vendor/bin/pest packages/CampusFind/Student/tests
```

### Run Full Test Suite:
```bash
vendor/bin/pest --testsuite=Packages
```

## Test Suites Covered
- `StudentReferenceArchitectureTest.php`: FormRequest validation, uniqueness, profile image lifecycle, transaction safety, DataGrid events, and architectural cleanliness.
- `StudentPackageIsolationTest.php`: Route contracts, auth guard isolation, Bouncer authorization, ACL/Menu/CoreConfig loading, Foundation purity, and 7-locale key parity.
- `StudentRuntimeSafetyTest.php`: Guest redirection, API 401 unauthenticated response, and 2-phase university authentication.
- `StudentSecurityTest.php`: Production fake-auth prevention, configuration enforcement, and staff boundary isolation.
- `UniversityStudentApiClientTest.php`: HTTP API integration with a real socket-bound mock server testing success, invalid credentials, HTTP 401/403, timeout, connection failures, malformed responses, redirect blocking, and production HTTPS enforcement.
