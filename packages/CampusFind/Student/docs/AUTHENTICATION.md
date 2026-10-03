# Authentication Architecture

## Overview
Student authentication operates independently of staff/admin authentication:
- **Guard**: `student` (session driver)
- **Provider**: `students` (Eloquent provider resolving `CampusFind\Student\Models\Student`)

## Two-Phase Authentication Workflow
1. **First-Time Student Login**:
   - The student submits their university card number and initial password.
   - If no local record exists, `UniversityStudentApiClient` contacts the configured university API.
   - Upon verification, the profile is returned as a `StudentProfileDto`.
   - A local `Student` record is created, and the password is automatically hashed.
   - The session is established and regenerated via `$request->session()->regenerate()`.
2. **Subsequent Logins**:
   - The local database record is matched and authenticated via `Auth::guard('student')->attempt()`.
   - No external network request is made.
   - Session is regenerated to prevent session fixation.

## Security Controls
- **Rate Limiting**: 5 attempts per minute per `sha1(IP | card_number)` on `student.login.store`.
- **Redirect Isolation**: Uses `AuthenticationRedirectResolver` to cleanly route unauthorized requests on `student/*` to `student.login`.
- **SSRF & Transport Security**: HTTPS is enforced in `production`. Automatic redirects are disabled. Sensitive credentials are never logged.
