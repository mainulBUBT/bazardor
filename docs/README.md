# Bazardor — Project Documentation

Bazardor is a community-driven market price tracker for Bangladesh. It lets anyone submit, browse, and compare product prices across local markets, backed by a moderated admin panel and a REST API for mobile clients.

---

## Feature Overview

| Area | Description |
|------|-------------|
| Price Contributions | Users (authenticated or anonymous via device ID) submit product prices with optional photo proof |
| Market Comparison | Compare prices for the same product across two or more markets |
| Admin Panel | Full CRUD for products, markets, categories, banners, zones, users, and roles |
| Role-Based Access | Dual role system — `user_type` for broad access levels, Spatie functional roles for granular permissions |
| Push Notifications | Create and send targeted push notifications by zone or audience |
| Social Login | Google and Facebook OAuth via Laravel Socialite |
| Reports | Contributions, data quality, markets, and price reports |
| Excel Import/Export | Import/export units, products, banners, users via `maatwebsite/excel` |
| Spatial Zones | Geographic zones stored as PostGIS Polygons using `laravel-eloquent-spatial` |

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Framework | Laravel 12 |
| PHP | 8.2+ |
| Database | MySQL (production) / SQLite (default dev) |
| Auth — Admin | Laravel session guard (`web`) |
| Auth — API | Laravel Sanctum (Bearer tokens) |
| Roles & Permissions | Spatie Laravel Permission v6 |
| Social Login | Laravel Socialite v5 |
| Spatial Data | `matanyadaev/laravel-eloquent-spatial` v4 |
| PDF | mPDF v8 |
| Excel | Maatwebsite Excel v3 |
| Frontend Build | Vite v6 + Tailwind CSS v4 |
| Admin Template | SB Admin 2 (Bootstrap 5) |
| JavaScript | Axios, jQuery, Chart.js, DataTables, Select2, SweetAlert2, Toastr |

---

## Documentation Index

| File | Contents |
|------|----------|
| [setup.md](setup.md) | Installation, environment config, seeding, artisan commands |
| [architecture.md](architecture.md) | Folder structure, layer diagram, key design patterns |
| [api-reference.md](api-reference.md) | All REST API endpoints with parameters and responses |
| [database.md](database.md) | All 30 database tables, columns, and relationships |
| [admin-panel.md](admin-panel.md) | Admin modules, routes, roles, permissions, and settings |

---

## Quick Start

```bash
# 1. Install PHP dependencies
composer install

# 2. Copy and configure environment
cp .env.example .env
php artisan key:generate

# 3. Configure DB_* in .env, then migrate and seed
php artisan migrate --seed

# 4. Install frontend dependencies and build
npm install && npm run build

# 5. Start development server
php artisan serve
```

Visit `http://localhost:8000` — you will be redirected to `/admin/auth/login`.

Default admin credentials are created by `AdminSeeder` (see [setup.md](setup.md)).

---

## Project Structure (Top Level)

```
bazardor/
├── app/                   # Application logic
│   ├── CentralLogics/     # Shared Helpers, Constants, Response
│   ├── Console/Commands/  # Artisan commands
│   ├── Enums/             # PHP enums (Permission, etc.)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/     # Admin panel controllers
│   │   │   └── Api/       # REST API controllers
│   │   ├── Middleware/    # Request middleware
│   │   ├── Requests/      # Form request validation
│   │   └── Resources/     # API JSON resources
│   ├── Imports/Exports/   # Excel import/export classes
│   ├── Mail/              # Mailable classes
│   ├── Models/            # Eloquent models (23+)
│   ├── Providers/         # Service providers
│   ├── Services/          # Business logic services
│   │   └── Api/           # API-specific services
│   └── Traits/            # Reusable traits (HasUuid, etc.)
├── adminpanel/            # Admin panel static assets
├── database/
│   ├── migrations/        # 30 migration files
│   └── seeders/           # 5 seeder classes
├── docs/                  # ← You are here
├── public/assets/admin/   # Admin template assets
├── resources/
│   ├── css/app.css        # Tailwind CSS entry
│   ├── js/app.js          # JS entry (Axios)
│   └── views/             # Blade templates (62 files)
└── routes/
    ├── web.php            # Root redirect
    ├── admin.php          # Admin panel routes (prefix: /admin)
    └── api.php            # REST API routes (prefix: /api)
```
