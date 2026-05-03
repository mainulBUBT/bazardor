# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

---

## Commands

```bash
# Code formatting
./vendor/bin/pint

# Tests (SQLite in-memory — see phpunit.xml)
php artisan test
php artisan test tests/Feature/NotificationSettingsTest.php
php artisan test --filter test_method_name

# Frontend (only needed when editing resources/css or resources/js)
npm run build        # production
npm run dev          # watch + HMR

# Roles & permissions (idempotent — run after any permission change)
php artisan roles:setup

# Process pending price contributions against thresholds
php artisan price-contributions:process

# After any config/.env change
php artisan config:clear && php artisan cache:clear

# After adding a new migration
php artisan migrate

# Storage symlink (first setup or after artisan storage:link is missing)
php artisan storage:link
```

---

## Project Overview

Bazardor is a **community-driven market price tracker for Bangladesh**. Users (authenticated or anonymous) submit product prices for local markets. An admin panel moderates content and a REST API serves mobile clients.

- **Admin panel** → `/admin` (session auth, Blade views)
- **REST API** → `/api` (Sanctum tokens, JSON)

---

## Documentation (`docs/`)

The `docs/` folder contains the full project documentation. **Read the relevant file before making changes in any of these areas** — they capture decisions and patterns that are not obvious from the code alone.

| File | When to read it |
|------|----------------|
| [docs/README.md](docs/README.md) | Project overview, tech stack, full feature list, top-level directory tree |
| [docs/architecture.md](docs/architecture.md) | Detailed request lifecycle, complete `app/` directory tree, all design decisions (UUID PKs, service layer, polymorphic relations, spatial zones, price flow, dual role system, frontend split) |
| [docs/api-reference.md](docs/api-reference.md) | Every API endpoint — method, path, request params, auth requirement, response resource. Read before adding or modifying any `/api/*` route. |
| [docs/database.md](docs/database.md) | All 30 tables with column-by-column definitions, types, constraints, indexes, and the full ER relationship map. Read before writing migrations or queries. |
| [docs/admin-panel.md](docs/admin-panel.md) | All admin modules, route tables, middleware stack, role/permission system, settings tabs, and Blade view structure. Read before touching `/admin/*` controllers or views. |
| [docs/setup.md](docs/setup.md) | Full install steps, `.env` config, seeder descriptions, artisan commands reference, and troubleshooting. |

---

## Architecture

### Request flow

```
routes/admin.php  →  Admin\*Controller  →  *Service  →  Eloquent Model  →  Blade view
routes/api.php    →  Api\*Controller    →  *Service  →  Eloquent Model  →  *Resource (JSON)
```

**Controllers are thin — all business logic lives in `app/Services/`.** Never query the database from a controller directly. Inject the relevant service via constructor.

### Directory layout (non-obvious parts)

| Path | Purpose |
|------|---------|
| `app/CentralLogics/` | Auto-loaded PHP files (not namespaced classes) |
| `app/CentralLogics/Constants.php` | Every API response constant (e.g. `AUTH_LOGIN_200`) |
| `app/CentralLogics/Helpers.php` | Global helper functions (`formated_response()`, `handle_file_upload()`, `translate()`, etc.) |
| `app/CentralLogics/Response.php` | Unused class — use the global `formated_response()` helper instead |
| `app/Services/Api/` | Services used only by API controllers |
| `app/Enums/Permission.php` | All Spatie permission strings as a PHP enum |
| `app/Traits/HasUuid.php` | Sets UUID PK on any model that uses it |

---

## Key Conventions

### API response format

Every API response **must** use `formated_response()` (defined in `Helpers.php`) paired with a constant from `Constants.php`:

```php
// Example from a controller
return response()->json(
    formated_response(constant: AUTH_LOGIN_200, content: $data),
    200
);

// With pagination
return response()->json(
    formated_response(constant: CATEGORY_MARKET_LIST_200, content: $paginator, limit: $perPage, offset: $offset),
    200
);
```

The function merges the constant's `response_code` and `message` with `data`, `total_size`, `limit`, `offset`, and `errors`. **Never build a raw array response in API controllers.**

### UUID primary keys

All domain models use `app/Traits/HasUuid` — `$incrementing = false`, `$keyType = 'string'`. IDs are UUIDs generated at creation time. Only Laravel infrastructure tables (`users`, `jobs`, `cache`, etc.) use auto-increment integers.

### Slug auto-generation

`Product` and `Market` models auto-generate slugs in their `booted()` / `boot()` hooks on `creating` and `updating`. Never rely on the caller to set a slug.

### Middleware aliases (registered in `bootstrap/app.php`)

| Alias | Class | When used |
|-------|-------|-----------|
| `admin` | `AdminMiddleware` | All admin routes (after login) |
| `permission` | `PermissionMiddleware` | Route-level Spatie permission check |
| `role` | `RoleMiddleware` | Route-level Spatie role check |
| `guest-track` | `ResolveGuestIdentifier` | Public API routes that allow anonymous submissions |

`guest-track` tries Sanctum auth first; if unauthenticated it reads `X-Device-ID` from the request header and stores it for downstream use.

### Dual role / permission system

Two orthogonal systems on `users`:

1. **`user_type` column** — broad access level: `super_admin`, `moderator`, `volunteer`, `user`. Automatically assigns a matching Spatie role.
2. **Spatie functional roles** — `Zone Manager`, `Content Manager`, `Price Manager`, `User Manager`, `Report Analyst` stored in the `roles` table.

Permission strings follow `{action}_{resource}` (e.g. `create_products`, `approve_price_contributions`). Legacy `manage_{resource}` strings still work. Use `app/Enums/Permission.php` for all permission values — never hardcode permission strings.

```php
// Route middleware
->middleware('permission:create_products')

// Blade
@can('edit_markets') ... @endcan

// PHP
auth()->user()->hasPermissionTo(Permission::CREATE_PRODUCTS->value)
```

### Settings

All app configuration lives in the `settings` table as JSON rows (`key_name`, `value`, `settings_type`). **Always read via `SettingService::getSetting($key)` or `SettingService::getSettingWithDefault($key, $default)`.** Never query the `settings` table directly.

### Price contribution flow

**Path A — automated processor (primary):**
1. `POST /api/products/submit-price` → `price_contributions` row (`status = pending`). Rate-limited to 1 submission/hour per user or device per product+market pair (`PRICE_SUBMISSION_RATE_LIMITED_429`).
2. `php artisan price-contributions:process` groups all pending rows by `(product_id, market_id)` and, inside a DB transaction per group:
   - Checks `price_thresholds.min_price / max_price` for the product. If no threshold exists, all positive prices pass.
   - **Valid** contributions: `AVG(submitted_price)` → `updateOrCreate` on `product_market_prices`.
   - All contributions (valid + invalid) are bulk-inserted into `price_contributions_history` (`status = validated | invalid`) and then **hard-deleted** from `price_contributions`.
   - `user_statistics.reputation_score` is incremented (+1 valid / -1 invalid) for each authenticated contributor.
3. The command is **not yet scheduled** — it must be run manually or added to the cron via `routes/console.php`.

**Path B — admin manual override:**
- Admin sets `status = approved | rejected` via `/admin/contributions` (`ContributionController`).
- This only changes the status column — it does **not** update `product_market_prices` or archive to history. The automated processor handles archiving when it next runs.

### Polymorphic relations

- **`entity_creators`** — tracks who created a `Market` or `Product` (`User` or `Admin`). Access via `$model->creatorRecord` (morphOne) or `$model->creator` (morphTo).
- **`favorites.favoritable`** — points to `Market` or `Product` by `favoritable_type` + `favoritable_id`.

### File uploads

Use the `handle_file_upload($folder, $extension, $file, $oldPath)` global helper (from `Helpers.php`) for all file storage. It handles disk storage and returns the stored path. To get the public URL, use the corresponding URL helper from `Helpers.php`.

---

## Frontend

The admin panel extends `layouts.admin.app` (**SB Admin 2** Bootstrap 5 template). Vendor libraries — jQuery, Chart.js, DataTables, Select2, SweetAlert2, Toastr, FontAwesome — live in `public/assets/admin/vendor/`. **They are not managed by npm and do not go through Vite.**

Only `resources/css/app.css` (Tailwind CSS v4) and `resources/js/app.js` (Axios setup) are Vite-compiled. Use Tailwind only for custom styling outside the SB Admin 2 template.

---

## Testing Notes

- Tests use in-memory SQLite (`DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:` in `phpunit.xml`).
- **Spatial queries (zones) require MySQL** — they will fail or be silently skipped in tests. Mock `ZoneService` or skip zone-dependent assertions in the test environment.
- Factory/seeder data for tests should be created inline — `BangladeshDemoDataSeeder` is for local dev only.
