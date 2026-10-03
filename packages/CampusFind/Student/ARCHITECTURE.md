# CampusFind\Student — Architecture Manifest

## Package Classification
- **Domain**: Optional domain package (University authentication & Student identity entity).
- **Parent/Vendor**: `CampusFind`
- **Package ID**: `student`
- **Composer Name**: `campus-find/student`
- **Namespace**: `CampusFind\Student`
- **Presentation Ownership**: Owns its own Student session portal and Admin UI (DataGrid, Controller, Views, FormRequests, ACL, Menu, MegaSearch & Quick Creation contributions).

## Directory Structure
```text
packages/CampusFind/Student/
├── composer.json
├── ARCHITECTURE.md
├── README.md
├── docs/
│   ├── INSTALLATION.md
│   ├── CONFIGURATION.md
│   ├── AUTHENTICATION.md
│   ├── ADMIN_INTEGRATION.md
│   └── TESTING.md
├── dev/
│   └── mock-university-api/
│       └── router.php
├── src/
│   ├── Config/
│   │   ├── acl.php
│   │   ├── auth.php
│   │   ├── core_config.php
│   │   ├── menu.php
│   │   └── student.php
│   ├── Contracts/
│   │   ├── Student.php
│   │   └── UniversityStudentApiContract.php
│   ├── Database/
│   │   └── Migrations/
│   │       └── 2026_03_24_000001_create_students_table.php
│   ├── DataGrids/
│   │   └── StudentDataGrid.php
│   ├── DataTransferObjects/
│   │   └── StudentProfileDto.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   └── StudentController.php
│   │   │   └── StudentSessionController.php
│   │   └── Requests/
│   │       ├── Admin/
│   │       │   ├── CreateStudentRequest.php
│   │       │   ├── MassDestroyRequest.php
│   │       │   └── UpdateStudentRequest.php
│   │       └── StudentLoginRequest.php
│   ├── Models/
│   │   ├── Student.php
│   │   └── StudentProxy.php
│   ├── Providers/
│   │   ├── ModuleServiceProvider.php
│   │   └── StudentServiceProvider.php
│   ├── Repositories/
│   │   └── StudentRepository.php
│   ├── Resources/
│   │   ├── lang/
│   │   │   ├── ar/app.php
│   │   │   ├── en/app.php
│   │   │   ├── es/app.php
│   │   │   ├── fa/app.php
│   │   │   ├── pt_BR/app.php
│   │   │   ├── tr/app.php
│   │   │   └── vi/app.php
│   │   └── views/
│   │       ├── admin/
│   │       │   ├── layouts/header/
│   │       │   │   ├── desktop-mega-search-results.blade.php
│   │       │   │   ├── mobile-mega-search-results.blade.php
│   │       │   │   └── quick-creation-item.blade.php
│   │       │   └── students/
│   │       │       ├── create.blade.php
│   │       │       ├── edit.blade.php
│   │       │       ├── index.blade.php
│   │       │       └── view.blade.php
│   │       └── sessions/
│   │           └── create.blade.php
│   ├── Routes/
│   │   ├── admin-routes.php
│   │   ├── breadcrumbs.php
│   │   └── web.php
│   └── Services/
│       ├── Exceptions/
│       │   └── UniversityApiException.php
│       ├── FakeUniversityStudentApiClient.php
│       ├── StudentAdminService.php
│       └── UniversityStudentApiClient.php
└── tests/
    ├── Feature/
    │   ├── StudentPackageIsolationTest.php
    │   ├── StudentReferenceArchitectureTest.php
    │   ├── StudentRuntimeSafetyTest.php
    │   ├── StudentSecurityTest.php
    │   └── UniversityStudentApiClientTest.php
    ├── Fixtures/
    │   └── university-api-router.php
    └── TestCase.php
```

## Owned Routes & Endpoints
- `student.login` (GET `student/login`)
- `student.login.store` (POST `student/login`)
- `student.logout` (POST `student/logout`)
- `admin.students.index` (GET `admin/students`)
- `admin.students.search` (GET `admin/students/search`)
- `admin.students.create` (GET `admin/students/create`)
- `admin.students.store` (POST `admin/students/create`)
- `admin.students.view` (GET `admin/students/view/{id}`)
- `admin.students.edit` (GET `admin/students/edit/{id}`)
- `admin.students.update` (PUT `admin/students/edit/{id}`)
- `admin.students.delete` (DELETE `admin/students/{id}`)
- `admin.students.mass_delete` (POST `admin/students/mass-delete`)

## ACL Contribution
- `students`
- `students.create`
- `students.edit`
- `students.view`
- `students.delete`

## Navigation Contribution
- `students` (route: `admin.students.index`, icon: `icon-contact`)

## Configuration Contribution
- `general.store.student_login`
- `general.university_api`
- `general.university_api.endpoint_settings`
- `general.settings.menu` field `'students'`

## Extension Points Dispatched
- Lifecycle hook `admin.students.datagrid.query.after`
- Lifecycle hook `admin.students.datagrid.columns.after`
- View render hook `admin.students.view.details.after`

## Dependency Invariants
- `Student` contains **0** dependencies or imports on unrelated optional business packages.
- Foundation packages (`Admin`, `Core`, `User`, `DataGrid`, `Installer`) contain **0** imports of `CampusFind\Student` or `student::` namespace.
- Unregistering or removing the package leaves zero broken dependencies.
