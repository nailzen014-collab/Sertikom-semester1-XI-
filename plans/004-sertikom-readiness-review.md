# 004 — Sertikom readiness review (MVC, middleware, auth, CRUD, payment, N+1, styling, readme)

- **Status**: DONE
- **Severity**: MEDIUM
- **Category**: SERTIKOM exam readiness; code quality; documentation
- **Estimated scope**: 7–9 files, approximately 120–200 changed lines

## Problem

The application already covers most SERTIKOM criteria (MVC structure, middleware, auth, partial CRUD, Midtrans payment gateway, Tailwind/Blade styling), but a few areas are not optimal and would strengthen the exam demonstration:

1. **Readme** — `README.md` is the default Laravel README, not project-specific. The "Readme" criterion has no evidence.
2. **N+1 query risk** — `PembayaranService::redirectUrl()` (app/Services/PembayaranService.php:30) reads `$pesanan->user` and `$pesanan->tiketPaket` without eager loading. `PembayaranController::process` binds a fresh `Pesanan` via route model binding, so each call triggers extra queries. `TiketController::pastikanPemilik()` and `pastikanSudahDibayar()` (app/Http/Controllers/TiketController.php:77-85) access `$tiket->pesanan` without eager loading.
3. **Authorization style** — Ownership/role checks use inline `abort_unless` in controllers (e.g. `pastikanPemilik`, middleware). Laravel Policies/Gates would be the more idiomatic demonstration of "Autorisasi".
4. **Email verification** — `MustVerifyEmail` is commented out in `app/Models/User.php:4` while verification controllers exist but are not wired to the User model.
5. **Base Controller** — `app/Http/Controllers/Controller.php` is empty (no `AuthorizesRequests`), so controllers cannot use `$this->authorize()` out of the box.

## Target

- **Readme**: Replace default Laravel README with project-specific documentation: app description, tech stack, installation steps, environment variables, route overview (public/user/admin), payment gateway (Midtrans) notes, and how to run tests.
- **N+1 (PembayaranService)**: In `redirectUrl()`, eager-load `user` and `tiketPaket` before accessing them: `$pesanan->load(['user', 'tiketPaket'])`.
- **N+1 (TiketController)**: Before `pastikanPemilik()` / `pastikanSudahDibayar()` access `$tiket->pesanan`, eager-load: `$tiket->load('pesanan.user')`.
- **Autorisasi (Policy)**:
  - Add `use Illuminate\Foundation\Auth\Access\AuthorizesRequests;` to abstract `app/Http/Controllers/Controller.php`.
  - Create `app/Policies/PesananPolicy.php` with `view(User $user, Pesanan $pesanan): bool` returning `$pesanan->user_id === $user->id`.
  - Create `app/Providers/AuthServiceProvider.php` registering `Pesanan::class => PesananPolicy::class` in `$policies`.
  - Replace inline ownership `abort_unless` in `PembayaranController::pastikanPemilik()` and `TiketController::pastikanPemilik()` with `$this->authorize('view', $pesanan)` / `$this->authorize('view', $tiket->pesanan)`.
- **Email verification**: Uncomment `use Illuminate\Contracts\Auth\MustVerifyEmail;` and `implements MustVerifyEmail` in `app/Models/User.php`.
- Run `vendor/bin/pint --dirty --format agent`.
- Run `vendor/bin/pest` (full suite) — must stay green.

## Boundaries

- Do NOT change existing route names, controller method signatures, or public behavior.
- Do NOT remove middleware until Policy equivalents are in place and tests pass.
- Do NOT add new dependencies.
- Do NOT alter Midtrans webhook signature verification logic.
- Styling/layout changes are out of scope for this plan (already addressed in plans 001–003).
- If any selector or method differs from the excerpts above when execution starts, stop and report drift.

## Steps

1. **README.md**: Write project-specific README (purpose, stack, install, `.env` example keys, route groups, Midtrans setup, `php artisan test` command).
2. **Base Controller**: Add `AuthorizesRequests` trait to `app/Http/Controllers/Controller.php`.
3. **Policy**: Create `app/Policies/PesananPolicy.php` (`view` method). Create `app/Providers/AuthServiceProvider.php` registering the policy.
4. **N+1 + Policy in controllers**: 
   - `PembayaranController::pastikanPemilik()` → `$this->authorize('view', $pesanan)`.
   - `PembayaranService::redirectUrl()` → add `$pesanan->load(['user', 'tiketPaket'])`.
   - `TiketController::show()` and `pdf()` → add `$tiket->load('pesanan.user')` before checks; `pastikanPemilik()` → `$this->authorize('view', $tiket->pesanan)`.
5. **Email verification**: Uncomment `MustVerifyEmail` import and interface in `app/Models/User.php`.
6. Run `vendor/bin/pint --dirty --format agent`.
7. Run `vendor/bin/pest` (full suite) — must stay green.

## Repo conventions to follow

- PHP 8 constructor property promotion, explicit return types, curly braces (AGENTS.md php rules).
- Use `php artisan make:policy` for Policy classes.
- Use named routes and `route()` for URL generation.
- Tests use Pest; run narrowest set then full suite.

## Verification

- **Mechanical**: `vendor/bin/pint --dirty --format agent` passes; `vendor/bin/pest` passes (all tests green).
- **N+1**: Confirm no extra queries when hitting `/pembayaran/{pesanan}` and `/tiket/{tiket}`.
- **Policy**: Confirm accessing another user's pesanan/tiket still returns 404/403 via Policy.
- **Readme**: File exists at repo root and describes install + routes.
