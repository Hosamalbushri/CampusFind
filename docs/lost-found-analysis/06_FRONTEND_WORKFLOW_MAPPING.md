# 06. Frontend and User Experience Mapping — CampusFind

## 1. CampusFind Design System Overview

CampusFind utilizes a modern, accessible UI design system built on **Tailwind CSS**, Blade View Components (`<x-campusfind_web_web::...>` in Web package and `<x-admin::...>` in Admin package), and responsive flexbox/grid layouts.

Key UI Invariants:
- **Brand Palette**: Deep Forest Emerald (`#185c54`), Sage Mint (`#e6f4ee`), Rose (`#e11d48`), and Amber (`#d97706`).
- **Typography & Directionality**: Native bidirectional support for **Arabic RTL** and **English LTR** via `app.locale` session switcher.
- **Component Architecture**: Reusable blade components (`card`, `badge`, `button`, `control-group`, `modal`, `avatar`, `breadcrumbs`, `flash-group`, `table`).

---

## 2. Detailed Page-by-Page Frontend Mapping

### 2.1. Public Landing Page (`home.blade.php`)
- **Route**: `GET /` &rarr; [`HomeController@index`](file:///home/hosam/Documents/compusfund/packages/CampusFind/Web/src/Web/Http/Controllers/HomeController.php#L13)
- **View File**: [`packages/CampusFind/Web/src/Web/Resources/views/home/index.blade.php`](file:///home/hosam/Documents/compusfund/packages/CampusFind/Web/src/Web/Resources/views/home/index.blade.php)
- **Intended Actor**: Public Visitor, Student, Staff.
- **Required Permissions**: None (Public).
- **Displayed Information**:
  - Hero banner with quick CTAs: "Report Lost Item", "Report Found Item", "Browse Inventory".
  - Grid of recent public items (limited to 6 recent items with `public_safe` images).
  - Category filter pills.
  - Trust and verification badges.
- **Available Actions**:
  - Navigate to search catalog, lost report form, found drop-off form, or student login.
  - Switch locale between 7 supported languages (`ar`, `en`, `es`, `fa`, `pt_BR`, `tr`, `vi`).
- **States**:
  - *Empty State*: Renders friendly graphic with "No items recorded recently".
  - *RTL/LTR*: Full mirror layout with `rtl:rotate-180` for directional arrow icons.

---

### 2.2. Catalog & Search Page (`items/index.blade.php`)
- **Route**: `GET /items` &rarr; [`ItemController@index`](file:///home/hosam/Documents/compusfund/packages/CampusFind/Web/src/Web/Http/Controllers/ItemController.php#L15)
- **View File**: [`packages/CampusFind/Web/src/Web/Resources/views/items/index.blade.php`](file:///home/hosam/Documents/compusfund/packages/CampusFind/Web/src/Web/Resources/views/items/index.blade.php)
- **Intended Actor**: Public Visitor, Student.
- **Required Permissions**: None.
- **Displayed Information**:
  - Search input (query text matching title, public description, found location, and reference key).
  - Category dropdown filter.
  - Bounded paginated item grid (12 items per page).
  - Each item card shows: cover image, category badge, public reference badge, title, description preview, location, and date found.
- **Available Actions**:
  - Submit search/filter form.
  - Clear search filters button (✕).
  - Click "View Details" to open item page.
  - Paginate forward/backward.
- **Current Limitation**: Currently displays **only** `FoundItem` records. Lost reports are not yet presented in this view.
- **States**:
  - *Empty State*: Renders card with "No items found matching criteria".

---

### 2.3. Item Detail & Claim Page (`items/show.blade.php`)
- **Route**: `GET /items/{reference}` &rarr; [`ItemController@show`](file:///home/hosam/Documents/compusfund/packages/CampusFind/Web/src/Web/Http/Controllers/ItemController.php#L56)
- **View File**: [`packages/CampusFind/Web/src/Web/Resources/views/items/show.blade.php`](file:///home/hosam/Documents/compusfund/packages/CampusFind/Web/src/Web/Resources/views/items/show.blade.php)
- **Intended Actor**: Public Visitor, Student.
- **Required Permissions**: None for viewing; `auth:student` for claiming.
- **Displayed Information**:
  - Primary image viewer and secondary thumbnail gallery (only `public_safe` images).
  - Badges for category and public reference.
  - Title and full public description.
  - Key metadata box (Found Location, Found Date & Time, Public Reference).
- **Available Actions**:
  - *For Guests*: Prompt box with "Claim Item" button redirecting to login with intended return URL.
  - *For Authenticated Students*: Interactive claim form with statement textarea, evidence photo file upload, and "Submit Claim" button (`POST /items/{reference}/claim`).
  - *For Students who already claimed*: Status badge displaying current claim status (`submitted`, `under_review`, `needs_information`, `approved`) and shortcut button to personal dashboard.
- **States**:
  - *Validation Errors*: Inline field error alerts under statement and image inputs.
  - *Success State*: Redirects to student dashboard with success alert banner.

---

### 2.4. Report Lost Item Page (`reports/lost.blade.php`)
- **Route**: `GET /reports/lost` &rarr; [`ReportController@createLost`](file:///home/hosam/Documents/compusfund/packages/CampusFind/Web/src/Web/Http/Controllers/ReportController.php#L28)
- **View File**: [`packages/CampusFind/Web/src/Web/Resources/views/reports/lost.blade.php`](file:///home/hosam/Documents/compusfund/packages/CampusFind/Web/src/Web/Resources/views/reports/lost.blade.php)
- **Intended Actor**: Authenticated Student (Guest sees login CTA card).
- **Required Permissions**: `auth:student`.
- **Form Fields**:
  - Category selector (required).
  - Item title (required, max: 160 chars).
  - Lost location (optional, max: 255 chars).
  - Lost date & time (optional, datetime-local).
  - Public description (optional textarea).
  - **Private Distinctive Details** (optional textarea, highlighted with green shield badge explaining private encrypted storage).
  - Item photo upload (optional, JPEG/PNG/WebP, max: 5MB).
- **Available Actions**:
  - Submit lost report (`POST /reports/lost.store`).
  - Back to items navigation.

---

### 2.5. Report Found Item / Drop-off Page (`reports/found.blade.php`)
- **Route**: `GET /reports/found` &rarr; [`ReportController@createFound`](file:///home/hosam/Documents/compusfund/packages/CampusFind/Web/src/Web/Http/Controllers/ReportController.php#L112)
- **View File**: [`packages/CampusFind/Web/src/Web/Resources/views/reports/found.blade.php`](file:///home/hosam/Documents/compusfund/packages/CampusFind/Web/src/Web/Resources/views/reports/found.blade.php)
- **Intended Actor**: Public Citizen, Student, Campus Staff.
- **Required Permissions**: None (Open intake).
- **Form Fields**:
  - Category selector (required).
  - Item title (required).
  - Found location (required).
  - Found date & time (optional).
  - Drop-off custody desk selector (`security_office`, `student_affairs`, `library_desk`).
  - Public description (optional).
  - Photo upload (optional).
- **Educational Cards**:
  - Explains the physical drop-off locations across campus with icons and building directions.

---

### 2.6. Student Dashboard (`account/dashboard.blade.php`)
- **Route**: `GET /account/dashboard` &rarr; [`AccountController@dashboard`](file:///home/hosam/Documents/compusfund/packages/CampusFind/Web/src/Web/Http/Controllers/AccountController.php#L17)
- **View File**: [`packages/CampusFind/Web/src/Web/Resources/views/account/dashboard.blade.php`](file:///home/hosam/Documents/compusfund/packages/CampusFind/Web/src/Web/Resources/views/account/dashboard.blade.php)
- **Intended Actor**: Authenticated Student.
- **Required Permissions**: `auth:student`.
- **Displayed Information**:
  - Student profile header (Avatar, Full Name, Active Badge, University Card Number).
  - Academic metrics (Registration Number, Major, Academic Level).
  - Action shortcut cards (Report Lost, Report Found, Browse Inventory).
  - **"My Lost Reports" Section**: Lists all reports filed by the student with title, reference, category, live status badge (`draft`, `active`, `resolved`, `cancelled`), and location/date.
  - **"My Claims" Section**: Lists all submitted ownership claims with item title, reference badge, category badge, live claim status badge (`submitted`, `under_review`, `needs_information`, `approved`), claim statement preview, submission timestamp, and reviewer feedback notes.
- **States**:
  - *Empty State*: Helpful prompt and action button when no reports or claims exist yet.

---

### 2.7. Employee Admin Workspace Views
Located in [`packages/CampusFind/LostAndFound/src/Resources/views/employee/`](file:///home/hosam/Documents/compusfund/packages/CampusFind/LostAndFound/src/Resources/views/employee):

1. **Found Items Management** (`items/index.blade.php`):
   - Interactive DataGrid of all found items with status filters, search, and action links.
   - Quick modals for "Create Found Item" and "Receive Custody".
2. **Lost Reports Management** (`reports/index.blade.php`):
   - DataGrid of all student lost reports with "Approve" and "Reject" quick actions.
3. **Claims Management** (`claims/all.blade.php` & `claims/index.blade.php`):
   - DataGrids listing claims per item and across the entire platform.
4. **Claim Detail & Review** (`claims/show.blade.php`):
   - In-depth verification console displaying item metadata, claimant profile, full submitted evidence list, secure private evidence image links, full historical review audit timeline, and review/approval action buttons.
5. **Category Settings** (`categories/index.blade.php`):
   - Admin DataGrid for managing item categories and sort order.
