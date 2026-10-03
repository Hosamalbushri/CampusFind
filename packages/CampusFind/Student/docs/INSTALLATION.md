# Installation Guide

## Requirements
- PHP ^8.3
- Laraseed Foundation V4
- Laravel ^12.0

## Activation
1. Enable the package in your `.env` file:
   ```env
   LARASEED_OPTIONAL_PACKAGES=student
   ```
   Or when composing multiple packages:
   ```env
   LARASEED_OPTIONAL_PACKAGES=student,lost_and_found
   ```

2. Run database migrations:
   ```bash
   php artisan migrate
   ```

3. Optimize / clear caches:
   ```bash
   php artisan config:clear
   php artisan route:clear
   ```

4. Verify package discovery:
   ```bash
   php artisan laraseed:packages
   ```
