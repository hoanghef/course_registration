# Course Registration System

This repository contains a Laravel-based Course Registration System with a small frontend (static HTML under `public/frontend` and Blade views under `resources/views/frontend`). It implements roles for students, teachers, training office (phòng đào tạo) and admin, plus API endpoints for course/registration management.

## Quick start (development)

Requirements:
- PHP 8.1+ (as configured by the project), Composer, MySQL

Steps:

1. Install dependencies

```powershell
composer install
```

2. Copy `.env` and set DB credentials

```powershell
copy .env.example .env
# then edit .env (DB_CONNECTION, DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD)
```

3. Generate application key

```powershell
php artisan key:generate
```

4. Run migrations + seeders

```powershell
php artisan migrate --seed
```

5. Start dev server

```powershell
php artisan serve
```

Visit: http://127.0.0.1:8000 or open the frontend pages directly under `public/frontend` (for quick testing).

## Important project notes

- Frontend files are available at `public/frontend/*.html`. Blade views live in `resources/views/frontend/*.blade.php` and are used when serving via routes.
- API base URL used by frontends: `window.location.origin + '/api'`.

### Recent behavior & important fixes

- Course code format unified: course codes are now like `IT009.1` (migration `2025_11_11_update_course_code_format.php` was added and applied).
- Server-side schedule conflict detection: the registration endpoint (`RegistrationController::register`) now rejects registration when the requested course schedules overlap with any of the student's existing approved/pending registrations. The error returned is HTTP 400 with message like: `Xung đột lịch với lớp đã đăng ký: <MÃ_LỚP>`.
- A migration was added and executed to adjust schedules for some courses to avoid conflicts in the demo data: `2025_11_11_120000_fix_conflicting_schedules.php` (it updates `course_schedules` rows for `IT003.1`, `IT004.1`, `IT005.1`).

## How schedule conflict detection works

- The backend compares schedules by day_of_week and time ranges. Two schedules overlap when they are on the same day and `start_a < end_b && start_b < end_a` (time comparison using Carbon).

## Seeding / Demo accounts

- The repository contains seeders under `database/seeders` that create demo users/courses/subjects. See `DatabaseSeeder.php` for details.

## Useful Artisan commands

- Run migrations: `php artisan migrate`
- Run a single migration file (Windows Powershell example):

```powershell
php artisan migrate --path="database/migrations/2025_11_11_120000_fix_conflicting_schedules.php"
```

- Run tests: `./vendor/bin/phpunit` (if tests are added)

## Frontend tips

- The student dashboard (`public/frontend/student-dashboard.html`) fetches available courses from `/api/sinh-vien/courses/available` and displays schedules. The frontend now expects the server to block overlapping registrations — for best UX you can add a client-side check to warn users before sending the request (not required but recommended).

## Next steps / suggestions

- Add a user-facing conflict resolution UI: when a conflict is detected, return detailed conflicting session(s) so the frontend can show which class/time conflicts.
- Add unit/integration tests around schedule-overlap detection.

If you want, I can update the README with more specific API endpoint docs or add example requests; tell me which parts you prefer expanded.
