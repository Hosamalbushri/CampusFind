# Web Form Migration & Parity Matrix

## Status of Web Forms & Parity with Admin Reference

| Form Location | Action / Mutation | Target Route | Submission Mode | Client Validation (VeeValidate 4) | Dual Controller Response | Loading Spinner State | Status |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `views/auth/login.blade.php` | Student Login | `campusfind_web.web.login.store` | AJAX via `<v-login-form>` | `required` on card & password | JSON (200 / 422) + HTML fallback | `<x-web::button ::loading="isProcessing">` | **VERIFIED & COMPLETED** |
| `views/reports/lost.blade.php` | Create Lost Item Report | `campusfind_web.web.reports.lost.store` | AJAX via `<v-lost-report-form>` | `required`, `max:160`, `max:255`, date | JSON (201 / 401 / 422) + HTML fallback | `<x-web::button ::loading="isProcessing">` | **VERIFIED & COMPLETED** |
| `views/reports/found.blade.php` | Create Found Item Notification | `campusfind_web.web.reports.found.store` | AJAX via `<v-found-report-form>` | `required`, `max:160`, `max:255`, date | JSON (201 / 422) + HTML fallback | `<x-web::button ::loading="isProcessing">` | **VERIFIED & COMPLETED** |
| `views/items/show.blade.php` | Submit Claim for Found Item | `campusfind_web.web.items.claim` | AJAX via `<v-claim-form>` | `required`, `max:2000` on statement | JSON (201 / 401 / 422) + HTML fallback | `<x-web::button ::loading="isProcessing">` | **VERIFIED & COMPLETED** |
| `views/reports/lost-detail.blade.php` | Submit Lost Report Response | `campusfind_web.web.lost-reports.responses.store` | AJAX via `<v-found-response-form>` | `required`, `max:255`, `max:2000` | JSON (200 / 401 / 422) + HTML fallback | `<x-web::button ::loading="isProcessing">` | **VERIFIED & COMPLETED** |
| `views/reports/lost-detail.blade.php` | Cancel Response | `campusfind_web.web.lost-reports.responses.cancel` | AJAX via `<v-cancel-response-form>` | N/A (State transition) | JSON (200 / 403) + HTML fallback | `<x-web::button ::loading="isProcessing">` | **VERIFIED & COMPLETED** |

---

## Component Level Parity Verification

| Admin Reference Component | Web Component Equivalent | Parity Verification Evidence |
| :--- | :--- | :--- |
| `<x-admin::form>` | `<x-web::form>` | Supports `as="div"` for slot forms and `method="POST"` with auto CSRF/spoofing |
| `<x-admin::form.control-group>` | `<x-web::form.control-group>` | Semantic container with label, slot control, hint, and error linking |
| `<x-admin::form.control-group.label>` | `<x-web::form.control-group.label>` | Accessible label with `:required="true"` asterisk marker |
| `<x-admin::form.control-group.control>` | `<x-web::form.control-group.control>` | VeeValidate `<v-field>` binding across all input types |
| `<x-admin::form.control-group.error>` | `<x-web::form.control-group.error>` | VeeValidate `<v-error-message>` with `role="alert"` and `aria-live` |
| `<x-admin::button>` / `<v-button>` | `<x-web::button>` / `<v-button>` | Full button support with `:loading="isProcessing"` spinner & disabled states |
| `<x-admin::flash-group>` | `<x-web::flash-group>` | Reactive event-driven toast system using mitt `$emitter.on('add-flash')` |
