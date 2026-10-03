# Admin Integration Guide

## Overview
The Student package integrates seamlessly into the Laraseed Admin panel without depositing any feature logic inside Foundation packages.

## Integration Points
- **Routes**: Registered in `src/Routes/admin-routes.php` under prefix `admin/students` with middleware `['web', 'admin_locale', 'user']`.
- **Navigation**: Contributed via `src/Config/menu.php` under key `students`.
- **ACL & Permissions**: Contributed via `src/Config/acl.php`:
  - `students`: View index & search.
  - `students.create`: Create new student records.
  - `students.edit`: Edit existing student details.
  - `students.view`: View detailed student profiles.
  - `students.delete`: Delete single or bulk student records.
- **DataGrid**: `CampusFind\Student\DataGrids\StudentDataGrid` with search, sorting, filtering, and extension events:
  - `admin.students.datagrid.query.after`
  - `admin.students.datagrid.columns.after`
- **UI Contributions**:
  - Quick Creation dropdown action (`quick-creation-item.blade.php`).
  - MegaSearch integration (`desktop-mega-search-results.blade.php` and `mobile-mega-search-results.blade.php`).
  - View hook in student details (`admin.students.view.details.after`).
