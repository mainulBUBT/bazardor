# Architecture

## Request Lifecycle

```
HTTP Request
    │
    ▼
routes/web.php  ──►  Admin routes (routes/admin.php)
routes/api.php  ──►  API routes
    │
    ▼
Middleware Stack
  ├── AdminMiddleware          — session auth + role check (admin guard)
  ├── PermissionMiddleware     — Spatie hasPermissionTo()
  ├── RoleMiddleware           — Spatie role check
  └── ResolveGuestIdentifier  — optional Sanctum auth + X-Device-ID extraction
    │
    ▼
Controller (Admin/ or Api/)
    │
    ▼
Service Layer (app/Services/)
    │
    ▼
Eloquent Model → Database
    │
    ▼
API Resource (app/Http/Resources/)  — for API responses only
    │
    ▼
JSON / Blade view response
```

---

## Layer Responsibilities

| Layer | Location | Responsibility |
|-------|----------|----------------|
| Routes | `routes/` | URL → controller mapping, middleware assignment |
| Middleware | `app/Http/Middleware/` | Auth, authorization, guest identity resolution |
| Controllers | `app/Http/Controllers/` | Request intake, delegates to Service, returns response |
| Form Requests | `app/Http/Requests/` | Input validation (run before controller method) |
| Services | `app/Services/` | All business logic — controllers call nothing else |
| Models | `app/Models/` | DB schema, relationships, scopes, mutators |
| Resources | `app/Http/Resources/` | Serialize models to JSON for API responses |
| CentralLogics | `app/CentralLogics/` | Global helpers, constants, response format |

---

## App Directory Tree

```
app/
├── CentralLogics/
│   ├── Constants.php          # App-wide constants (enums, config keys)
│   ├── Helpers.php            # Global utility functions (auto-loaded)
│   └── Response.php           # Standardized API response builder
│
├── Console/Commands/
│   ├── ProcessPriceContributions.php   # Scheduled: validates & promotes pending prices
│   └── SetupRolesAndPermissions.php    # php artisan roles:setup
│
├── Enums/
│   └── Permission.php         # Permission enum values (granular + legacy)
│
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── Auth/LoginController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── AdminManagementController.php
│   │   │   ├── UserManagementController.php
│   │   │   ├── ProductController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── MarketController.php
│   │   │   ├── BannerController.php
│   │   │   ├── UnitController.php
│   │   │   ├── ZoneController.php
│   │   │   ├── RoleController.php
│   │   │   ├── SettingController.php
│   │   │   ├── ContributionController.php
│   │   │   ├── PushNotificationController.php
│   │   │   └── ReportController.php
│   │   └── Api/
│   │       ├── AuthController.php
│   │       ├── UserManagementController.php
│   │       ├── CategoryMarketController.php
│   │       ├── BannerController.php
│   │       ├── UnitController.php
│   │       ├── ConfigController.php
│   │       └── LocationController.php
│   │
│   ├── Middleware/
│   │   ├── AdminMiddleware.php
│   │   ├── PermissionMiddleware.php
│   │   ├── RoleMiddleware.php
│   │   └── ResolveGuestIdentifier.php
│   │
│   ├── Requests/
│   │   ├── Admin/LoginRequest.php
│   │   ├── Api/
│   │   │   ├── LoginRequest.php
│   │   │   ├── RegisterRequest.php
│   │   │   ├── ForgotPasswordRequest.php
│   │   │   ├── ResetPasswordRequest.php
│   │   │   ├── OtpVerificationRequest.php
│   │   │   ├── SocialLoginRequest.php
│   │   │   ├── UpdateProfileRequest.php
│   │   │   └── AddFavoriteRequest.php
│   │   ├── AdminStoreUpdateRequest.php
│   │   ├── BannerStoreUpdateRequest.php
│   │   ├── CategoryStoreUpdateRequest.php
│   │   ├── CompareMarketsRequest.php
│   │   ├── CompareMarketProductsRequest.php
│   │   ├── MarketStoreUpdateRequest.php
│   │   ├── PriceContributionRequest.php
│   │   ├── ProductStoreUpdateRequest.php
│   │   ├── PushNotificationStoreUpdateRequest.php
│   │   ├── RoleStoreUpdateRequest.php
│   │   ├── UnitStoreUpdateRequest.php
│   │   ├── UpdateSettingsRequest.php
│   │   ├── UserStoreUpdateRequest.php
│   │   └── ZoneStoreUpdateRequest.php
│   │
│   └── Resources/
│       ├── BannerResource.php
│       ├── CategoryResource.php
│       ├── CategoryMarketResource.php
│       ├── FavoriteResource.php
│       ├── MarketResource.php
│       ├── MarketsComparisonResource.php
│       ├── MarketComparisonResource.php
│       ├── ProductResource.php
│       ├── ProductMarketPriceResource.php
│       ├── ProductComparisonResource.php
│       ├── UnitResource.php
│       ├── UserResource.php
│       └── ZoneResource.php
│
├── Models/               (see database.md for full list)
├── Services/
│   ├── Api/AuthService.php
│   ├── AdminManagementService.php
│   ├── AuthenticationService.php
│   ├── BannerService.php
│   ├── CategoryService.php
│   ├── ContributionService.php
│   ├── FavoriteService.php
│   ├── MarketComparisonService.php
│   ├── MarketService.php
│   ├── PriceContributionProcessor.php
│   ├── ProductMarketPriceService.php
│   ├── ProductService.php
│   ├── PushNotificationService.php
│   ├── RecaptchaService.php
│   ├── RoleService.php
│   ├── SettingService.php
│   ├── UnitService.php
│   ├── UserManagementService.php
│   └── ZoneService.php
│
└── Traits/
    └── HasUuid.php            # Overrides PK to UUID
```

---

## Key Design Decisions

### UUID Primary Keys
All domain models use the `HasUuid` trait instead of auto-incrementing integers. This prevents ID enumeration and allows client-side ID generation.

### Service Layer (no fat controllers)
Controllers never query the database directly. Every business operation lives in a dedicated `*Service` class. This makes controllers thin and services independently testable.

### Polymorphic Relations

| Relation | Models | Purpose |
|----------|--------|---------|
| `EntityCreator` (morphOne) | `Product`, `Market` | Track who created an entity (User or Admin) |
| `Favorite.favoritable` (morphTo) | `Market`, `Product` | Generic favorites for any entity type |

### Dual Role / Permission System
See [admin-panel.md](admin-panel.md#roles--permissions) for the full breakdown. In short:
- `user_type` column: broad access category (`super_admin`, `moderator`, `volunteer`, `user`)
- Spatie `roles` + `permissions`: granular functional roles (Zone Manager, Content Manager, etc.)

### Guest Tracking via Device ID
`ResolveGuestIdentifier` middleware tries Sanctum auth but, if unauthenticated, reads `X-Device-ID` from headers. This lets anonymous users submit prices and have their contributions tracked without an account.

### Spatial Zones
`zones.coordinates` is stored as a PostGIS `Polygon` via `laravel-eloquent-spatial`. The `ZoneService::getZoneByCoordinates()` method uses spatial queries to resolve which zone a lat/lng falls into.

### Price Contribution Flow
1. User (or guest with device ID) submits a price via `POST /api/products/submit-price`
2. Contribution lands in `price_contributions` with `status = pending`
3. `ProcessPriceContributions` artisan command (scheduled) validates against `price_thresholds.min_price / max_price`
4. Approved contributions update `product_market_prices` and are archived to `price_contributions_history`
5. Admin can manually approve/reject from the Contributions module

### Soft Deletes
`Product`, `Category`, `Banner`, `Unit`, and `PriceContribution` use `SoftDeletes`. Records are never hard-deleted from these tables by normal CRUD operations.

---

## CentralLogics

| File | Purpose |
|------|---------|
| `app/CentralLogics/Helpers.php` | Auto-loaded global helpers (uploaded file URLs, slugs, etc.) |
| `app/CentralLogics/Constants.php` | App-wide string/int constants used across layers |
| `app/CentralLogics/Response.php` | Builds standardized `{success, message, data}` API responses |

---

## Scheduled / Artisan Commands

| Command | Class | Purpose |
|---------|-------|---------|
| `php artisan contributions:process` | `ProcessPriceContributions` | Validate pending price contributions against thresholds and promote approved ones |
| `php artisan roles:setup` | `SetupRolesAndPermissions` | Create or re-seed all roles and permissions |

---

## Frontend

The admin panel uses the **SB Admin 2** Bootstrap template. Assets live in `public/assets/admin/`. New Blade views extend `layouts.admin.app`.

Tailwind CSS v4 is compiled via Vite into `public/build/`. It is used for any custom styling outside the SB Admin 2 template.

```
resources/
├── css/app.css        # @import "tailwindcss"; entry
├── js/app.js          # import './bootstrap'; (Axios setup)
└── js/bootstrap.js    # Axios global defaults

public/assets/admin/
├── css/               # sb-admin-2.css, custom.css
├── js/                # sb-admin-2.js + chart/datatable demos
└── vendor/            # bootstrap, jquery, chart.js, datatables,
                       # fontawesome, select2, sweetalert2, toastr
```
