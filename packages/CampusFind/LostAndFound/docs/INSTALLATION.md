# Installation & Activation Guide

## Prerequisites

- PHP >= 8.2 with `gd`, `pdo_sqlite`, `openssl`, and `fileinfo` extensions.
- Laraseed V4 Core Application.
- `CampusFind\Student` package installed and activated.

---

## 1. Package Registration

In `.env`, include `lost_and_found` and `student` in the active optional packages composition:

```env
LARASEED_OPTIONAL_PACKAGES=student,lost_and_found
```

---

## 2. Database Migrations

Run database migrations:

```bash
php artisan migrate
```

---

## 3. Storage Symlink & Private Disk Preparation

Ensure the public storage symlink exists and create the private storage root:

```bash
php artisan storage:link
mkdir -p storage/app/lost-found-private
```

---

## 4. Verify Installation

Check the package registration status via the Laraseed CLI:

```bash
php artisan laraseed:packages
```

Both `Student` and `LostAndFound` should display as `ACTIVE`.
