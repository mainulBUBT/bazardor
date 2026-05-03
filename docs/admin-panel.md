# Admin Panel

## Access

| Item | Value |
|------|-------|
| URL prefix | `/admin` |
| Login page | `/admin/auth/login` |
| Auth guard | `web` (session-based) |
| Root redirect | `/` → `/admin/auth/login` |

After login the user lands on `/admin/dashboard`.

---

## Middleware Stack

Every admin route (except the login page) is protected by:

1. **`auth:web`** — Laravel session auth; redirects to `/admin/auth/login` if unauthenticated
2. **`AdminMiddleware`** — confirms the authenticated model is an `Admin` with role `SUPER_ADMIN` or `MODERATOR`; aborts `403` otherwise

Individual routes may add:

- **`PermissionMiddleware`** — checks `hasPermissionTo('<permission>')` (Spatie)
- **`RoleMiddleware`** — checks one of a list of Spatie roles

---

## Sidebar Modules

| Module | Route prefix | Controller |
|--------|-------------|------------|
| Dashboard | `/admin/dashboard` | `DashboardController` |
| Users | `/admin/users` | `UserManagementController` |
| Pending Users | `/admin/users/pending` | `UserManagementController` |
| Admins | `/admin/admins` | `AdminManagementController` |
| Roles & Permissions | `/admin/roles` | `RoleController` |
| Products | `/admin/products` | `ProductController` |
| Categories | `/admin/categories` | `CategoryController` |
| Markets | `/admin/markets` | `MarketController` |
| Banners | `/admin/banners` | `BannerController` |
| Units | `/admin/units` | `UnitController` |
| Zones | `/admin/zones` | `ZoneController` |
| Price Contributions | `/admin/contributions` | `ContributionController` |
| Push Notifications | `/admin/push-notifications` | `PushNotificationController` |
| Reports | `/admin/reports/*` | `ReportController` |
| Settings | `/admin/settings` | `SettingController` |

---

## Module Details

### Dashboard
`GET /admin/dashboard`

Overview stats: total users, markets, products, pending contributions.

---

### Users (`/admin/users`)

| Action | Route | Notes |
|--------|-------|-------|
| List | `GET /admin/users` | Paginated, searchable |
| Create | `GET/POST /admin/users/create` | |
| Edit | `GET/PUT /admin/users/{id}/edit` | |
| Show | `GET /admin/users/{id}` | |
| Delete | `DELETE /admin/users/{id}` | |
| Export | `GET /admin/users/export` | Excel download |
| Pending | `GET /admin/users/pending` | Users awaiting approval |
| Approve | `POST /admin/users/{id}/approve` | |
| Reject | `POST /admin/users/{id}/reject` | |
| Status toggle | `POST /admin/users/{id}/status` | |

---

### Admins (`/admin/admins`)

Full CRUD for admin accounts. Admins use Spatie roles (not `user_type`).

---

### Roles & Permissions (`/admin/roles`)

| Action | Route |
|--------|-------|
| List | `GET /admin/roles` |
| Create | `GET/POST /admin/roles/create` |
| Edit | `GET/PUT /admin/roles/{id}/edit` |
| Delete | `DELETE /admin/roles/{id}` |

Permissions are grouped by resource in the UI (select/deselect all, group toggle). System roles (`super_admin`) are protected from editing their name.

---

### Products (`/admin/products`)

| Action | Route |
|--------|-------|
| List | `GET /admin/products` |
| Create | `GET/POST /admin/products/create` |
| Edit | `GET/PUT /admin/products/{id}/edit` |
| Show | `GET /admin/products/{id}` |
| Delete | `DELETE /admin/products/{id}` |
| Export | `GET /admin/products/export` |
| Bulk import | `GET /admin/products/bulk-import` |
| Import submit | `POST /admin/products/import` |

Slug is auto-generated on save if not manually set.

---

### Categories (`/admin/categories`)

CRUD + `status` toggle + export to Excel.

---

### Markets (`/admin/markets`)

| Action | Route |
|--------|-------|
| CRUD | Standard resource routes |
| Get districts | `GET /admin/markets/districts?division=...` |
| Get thanas | `GET /admin/markets/thanas?district=...` |

Districts and thanas are Bangladesh administrative boundaries, fetched dynamically on the create/edit form.

---

### Banners (`/admin/banners`)

CRUD + `status` toggle + export.

Banner types: `featured`, `general`. Optional `zone_id` scopes the banner to a geographic zone.

---

### Units (`/admin/units`)

CRUD + Excel import/export. Units have a `unit_type` (e.g. weight, volume, quantity) and a `symbol`.

---

### Zones (`/admin/zones`)

CRUD + `status` toggle. The `coordinates` field holds a GeoJSON Polygon. Zones are used to scope banners, markets, push notifications, and API config queries.

---

### Price Contributions (`/admin/contributions`)

| Action | Route |
|--------|-------|
| List (paginated) | `GET /admin/contributions` |
| Approve | `POST /admin/contributions/{id}/approve` |
| Reject | `POST /admin/contributions/{id}/reject` |

Approved contributions update `product_market_prices` and are archived to `price_contributions_history`.

---

### Push Notifications (`/admin/push-notifications`)

| Action | Route |
|--------|-------|
| CRUD | Standard resource routes |
| Send | `POST /admin/push-notifications/{id}/send` |
| Resend | `POST /admin/push-notifications/{id}/resend` |
| Estimated reach | `GET /admin/push-notifications/estimated-reach` |

Notifications can target by zone or all users. `recipients_count` and `opened_count` are tracked.

---

### Reports (`/admin/reports`)

| Report | Route |
|--------|-------|
| Contributions | `GET /admin/reports/contributions` |
| Data Quality | `GET /admin/reports/data-quality` |
| Markets | `GET /admin/reports/markets` |
| Prices | `GET /admin/reports/prices` |

---

### Settings (`/admin/settings`)

Settings are stored in the `settings` table as key-value JSON rows. The settings page has multiple tabs:

| Tab | Route | Key settings |
|-----|-------|-------------|
| General | `GET /admin/settings` | Company name, logo, address, contact |
| App | `GET /admin/settings?tab=app` | App version, maintenance mode toggle |
| Mail | `GET /admin/settings?tab=mail` | SMTP config, test mail send |
| Notifications | `GET /admin/settings?tab=notifications` | Push notification config |
| Social | `GET /admin/settings?tab=social` | Google/Facebook OAuth keys |
| Backup | `GET /admin/settings?tab=backup` | Create database backup |
| Business Rules | `GET /admin/settings?tab=business-rules` | Price threshold rules |
| Others | `GET /admin/settings?tab=others` | Misc toggles |

**Special actions:**

| Action | Route |
|--------|-------|
| Update settings | `POST /admin/settings/update` |
| Toggle status | `POST /admin/settings/status` |
| Clear cache | `POST /admin/settings/clear-cache` |
| Create backup | `POST /admin/settings/backup` |
| Toggle maintenance | `POST /admin/settings/maintenance` |
| Update social config | `POST /admin/settings/social` |
| Send test mail | `POST /admin/settings/test-mail` |

---

## Roles & Permissions

### User Types (`user_type` column on `users`)

| Type | Access Level |
|------|-------------|
| `super_admin` | All permissions |
| `moderator` | Products, categories, markets, banners, users, prices, reports |
| `volunteer` | Create/edit products, markets, prices; view categories and zones |
| `user` | View only (products, categories, markets, prices) |

### Functional Roles (Spatie — `roles` table)

| Role | Permissions |
|------|------------|
| Zone Manager | Zone management, market oversight, user management |
| Content Manager | Full content management (products, categories, banners) |
| Price Manager | Price management, contribution approval, price analytics |
| User Manager | User account management and user analytics |
| Report Analyst | Advanced reporting, analytics, data export |

### Granular Permission Format

Permissions follow `{action}_{resource}`:

- **Actions:** `create`, `edit`, `view`, `delete`, `approve`, `manage`
- **Resources:** `products`, `categories`, `markets`, `banners`, `users`, `admins`, `prices`, `roles`, `zones`, `reports`

Legacy `manage_{resource}` permissions are still supported for backward compatibility.

### Usage in Code

```php
// Middleware on route
Route::get('/products', [ProductController::class, 'index'])
    ->middleware('permission:view_products');

// In controller
if (!auth()->user()->hasPermissionTo('create_products')) {
    abort(403);
}
```

```blade
{{-- In Blade views --}}
@can('create_products')
    <a href="{{ route('products.create') }}">Add Product</a>
@endcan
```

### Setup

```bash
php artisan roles:setup
```

This runs `SetupRolesAndPermissions` which creates all roles and permissions from scratch (idempotent).

---

## Views Structure

All admin Blade views extend `layouts.admin.app`.

```
resources/views/
├── layouts/admin/
│   ├── app.blade.php           # Main layout (navbar, sidebar, content slot)
│   ├── topbar.blade.php
│   ├── sidebar.blade.php
│   ├── footer.blade.php
│   └── logout-modal.blade.php
└── admin/
    ├── auth/login.blade.php
    ├── dashboard.blade.php
    ├── admins/          (index, create, edit, show)
    ├── users/           (index, create, edit, show, pending + partials)
    ├── roles/           (index, create, edit)
    ├── products/        (index, create, edit, show, bulk-import)
    ├── categories/      (index, create, edit)
    ├── markets/         (index, create, edit, show)
    ├── banners/         (index, create, edit)
    ├── units/           (index, edit, import_export)
    ├── zones/           (index, edit)
    ├── contributions/   (index)
    ├── push-notification/ (index, create, edit, show)
    ├── reports/         (contributions, data-quality, markets, prices)
    ├── settings/        (general, app, mail, notifications, social,
    │                     backup, business-rules, others + _partials/tabs)
    └── email-templates/password-reset-otp.blade.php
```
