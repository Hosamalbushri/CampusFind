# WEB PAGE MIGRATION MATRIX — FULL REPOSITORY INVENTORY

## 1. Overview & Verification Status

Every view file owned by `packages/CampusFind/Web` has been cataloged, audited, and migrated to the unified component system (`x-web::`). No raw HTML form elements, direct Admin presentation dependencies, or inline JS handlers remain.

---

## 2. Complete Page Migration Inventory

| # | Route Name | Controller & Action | View File | Layout Used | Form Behavior | Components Required & Utilized | Migration Status |
|---|---|---|---|---|---|---|---|
| 1 | `campusfind_web.web.home` | `HomeController@index` | `home/index.blade.php` | `<x-web::layouts>` | None (browsing actions) | `x-web::container`, `x-web::badge`, `x-web::button`, `x-web::section`, `x-web::card`, `x-web::accordion` | **Migrated & Verified** |
| 2 | `campusfind_web.web.login` | `LoginController@showLoginForm` | `auth/login.blade.php` | `<x-web::layouts.anonymous>` | Student credentials POST | `x-web::container`, `x-web::card`, `x-web::alert`, `x-web::flash-group`, `x-web::form`, `x-web::form.control-group`, `x-web::button` | **Migrated & Verified** |
| 3 | `campusfind_web.web.account.dashboard` | `AccountController@dashboard` | `account/dashboard.blade.php` | `<x-web::layouts>` | Student logout form | `x-web::container`, `x-web::breadcrumbs`, `x-web::flash-group`, `x-web::card`, `x-web::avatar`, `x-web::badge`, `x-web::form`, `x-web::button`, `x-web::tabs`, `x-web::tabs.item` | **Migrated & Verified** |
| 4 | `campusfind_web.web.items.index` | `ItemController@index` | `items/index.blade.php` | `<x-web::layouts>` | Unified search & filter form | `x-web::section`, `x-web::page-header`, `x-web::alert`, `x-web::campus.report-filters`, `x-web::spinner`, `x-web::campus.report-card`, `x-web::pagination`, `x-web::empty-state` | **Migrated & Verified** |
| 5 | `campusfind_web.web.items.show` | `ItemController@show` | `items/show.blade.php` | `<x-web::layouts>` | Ownership claim submission form | `x-web::section`, `x-web::button`, `x-web::badge`, `x-web::card`, `x-web::alert`, `x-web::form`, `x-web::form.control-group` | **Migrated & Verified** |
| 6 | `campusfind_web.web.reports.lost` | `ReportController@createLost` | `reports/lost.blade.php` | `<x-web::layouts>` | Lost item intake POST with image upload | `x-web::container`, `x-web::breadcrumbs`, `x-web::flash-group`, `x-web::badge`, `x-web::card`, `x-web::button`, `x-web::form`, `x-web::form.control-group` | **Migrated & Verified** |
| 7 | `campusfind_web.web.reports.found` | `ReportController@createFound` | `reports/found.blade.php` | `<x-web::layouts>` | Found item intake POST with image upload & dropoff selection | `x-web::container`, `x-web::breadcrumbs`, `x-web::flash-group`, `x-web::badge`, `x-web::card`, `x-web::button`, `x-web::form`, `x-web::form.control-group` | **Migrated & Verified** |
| 8 | `campusfind_web.web.lost-reports.show` | `LostReportResponseController@show` | `reports/lost-detail.blade.php` | `<x-web::layouts>` | Found response submission form & cancellation form | `x-web::section`, `x-web::card`, `x-web::badge`, `x-web::alert`, `x-web::button`, `x-web::form`, `x-web::form.control-group` | **Migrated & Verified** |
| 9 | `campusfind_web.web.pages.show` | `PageController@show` | `pages/show.blade.php` | `<x-web::layouts>` | None (static & FAQ information) | `x-web::container`, `x-web::breadcrumbs`, `x-web::card`, `x-web::accordion` | **Migrated & Verified** |
| 10 | (Internal Showcase) | Standalone View | `components/example.blade.php` | Component Testbed | Multiple interactive form & UI controls | Full showcase of all 20+ `<x-web::...>` components | **Migrated & Verified** |

---

## 3. Global Chrome & Layout Subcomponents

| Component View | Source Path | Key Responsibilities | Migration Status |
|---|---|---|---|
| Global Layout | `components/layouts/index.blade.php` | HTML doctype, meta, RTL/LTR detection, dark mode cookie, `#app` mount, header, footer, toast & confirm modal integration | **Migrated & Verified** |
| Anonymous Layout | `components/layouts/anonymous.blade.php` | Centered shell for authentication and standalone flows | **Migrated & Verified** |
| Layout Tabs | `components/layouts/tabs.blade.php` | Navigation tab bar with active route highlighting | **Migrated & Verified** |
| Global Header | `components/layouts/header/index.blade.php` | Brand logo, primary navigation links, language switcher dropdown, student auth state | **Migrated & Verified** |
| Navbar Partial | `components/layouts/header/navbar.blade.php` | Navigation links and accessible mobile drawer toggle | **Migrated & Verified** |
| Global Footer | `components/layouts/footer/index.blade.php` | Campus information, quick links, dark mode toggle, copyright notice | **Migrated & Verified** |
