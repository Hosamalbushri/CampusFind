# Configuration Reference

The Student package configuration is defined in `packages/CampusFind/Student/src/Config/student.php`.

## Environment Variables

| Variable | Default | Description |
| :--- | :--- | :--- |
| `STUDENT_UNIVERSITY_API_FAKE` | `false` | When `true`, uses `FakeUniversityStudentApiClient` for local development. **Prohibited in production**. |
| `STUDENT_UNIVERSITY_API_BASE_URL` | `https://api.university.example` | Base URL of the university API. |
| `STUDENT_UNIVERSITY_API_VERIFY_PATH` | `/students/verify` | Relative path to verification endpoint. |
| `STUDENT_UNIVERSITY_API_TIMEOUT` | `15` | Request timeout in seconds. |
| `STUDENT_REDIRECT_AFTER_LOGIN` | `/` | Default destination URL after student sign-in. |

## Administrative Core Config Settings
- **Student Login Page Branding** (`general.store.student_login`):
  - `logo_image`: Custom logo image for the login page.
  - `primary_color`, `accent_color`: Theme colors.
  - `surface_start`, `surface_end`: Gradient background colors.
  - `panel_start`, `panel_end`: Side panel gradient colors.
  - `title`, `description`, `eyebrow`, `panel_lead`: Content copy.
- **University API Endpoint** (`general.university_api.endpoint_settings`):
  - `endpoint`: Overrides default endpoint URL dynamically from Admin settings.
