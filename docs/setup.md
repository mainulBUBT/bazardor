# Setup & Installation

## Requirements

| Tool | Minimum Version |
|------|----------------|
| PHP | 8.2 |
| Composer | 2.x |
| Node.js | 18+ |
| npm | 9+ |
| MySQL | 8.0+ (or SQLite for local dev) |
| PHP extensions | `pdo`, `mbstring`, `xml`, `curl`, `fileinfo`, `gd`, `zip`, `spatialite` (for zones) |

> **MySQL note:** Spatial zone queries require the MySQL spatial extension (`ST_Contains`, `ST_GeomFromGeoJSON`). SQLite does not support spatial queries — zone-related features will not work with SQLite.

---

## 1. Clone & Install

```bash
git clone <repo-url> bazardor
cd bazardor

# PHP dependencies
composer install

# Node dependencies
npm install
```

---

## 2. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Open `.env` and configure at minimum:

```dotenv
APP_NAME=Bazardor
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bazardor
DB_USERNAME=root
DB_PASSWORD=secret

MAIL_MAILER=log          # use 'smtp' for real mail
MAIL_FROM_ADDRESS=hello@bazardor.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Social Login (optional)

```dotenv
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI="${APP_URL}/api/auth/social-login/google/callback"

FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=
FACEBOOK_REDIRECT_URI="${APP_URL}/api/auth/social-login/facebook/callback"
```

---

## 3. Database Migration & Seeding

```bash
# Run all migrations
php artisan migrate

# Seed with roles, permissions, admin user, and demo data
php artisan db:seed
```

Or seed individual classes:

```bash
php artisan db:seed --class=AdminSeeder
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=GranularPermissionsSeeder
php artisan db:seed --class=BangladeshDemoDataSeeder
```

### What each seeder creates

| Seeder | Creates |
|--------|---------|
| `AdminSeeder` | Default super-admin account |
| `RolePermissionSeeder` | Basic roles (`super_admin`, `moderator`, `volunteer`, `user`) |
| `GranularPermissionsSeeder` | Granular permissions (`create_products`, `edit_users`, etc.) |
| `BangladeshDemoDataSeeder` | Sample zones, markets, categories, and products for Bangladesh |

### Default Admin Credentials

The `AdminSeeder` creates an admin account. Check `database/seeders/AdminSeeder.php` for the seeded email and password, then change them after first login via Settings → Admins.

---

## 4. Build Frontend Assets

```bash
# Production build
npm run build

# Development (watch mode with HMR)
npm run dev
```

Assets are output to `public/build/`. The admin template assets (`SB Admin 2`) already live in `public/assets/admin/` and do not need to be built.

---

## 5. Start the Application

```bash
# Laravel dev server
php artisan serve
```

Open `http://localhost:8000` — it redirects to `/admin/auth/login`.

For MAMP/Apache/Nginx, point the document root to the `public/` directory.

---

## 6. Queue Worker (if using database queues)

The `.env.example` defaults to `QUEUE_CONNECTION=database`. Start a queue worker to process jobs:

```bash
php artisan queue:work
```

For production, use Laravel Horizon or a Supervisor-managed worker.

---

## 7. Scheduler (for price contributions)

The `ProcessPriceContributions` command runs on a schedule to validate and promote pending price submissions. Add a cron entry:

```cron
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

Or run manually:

```bash
php artisan contributions:process
```

---

## Artisan Commands Reference

| Command | Description |
|---------|-------------|
| `php artisan migrate` | Run all pending migrations |
| `php artisan migrate:fresh --seed` | Drop all tables, re-migrate, and seed |
| `php artisan db:seed` | Run all seeders |
| `php artisan roles:setup` | Create/re-seed all roles and permissions (idempotent) |
| `php artisan contributions:process` | Validate pending price contributions against thresholds |
| `php artisan schedule:run` | Run scheduled tasks (add to cron) |
| `php artisan queue:work` | Start database queue worker |
| `php artisan config:clear` | Clear config cache |
| `php artisan cache:clear` | Clear application cache |
| `php artisan route:list` | List all registered routes |
| `php artisan tinker` | Interactive REPL |

---

## Development Workflow

```bash
# Terminal 1 — PHP server
php artisan serve

# Terminal 2 — Vite HMR (only needed if editing resources/css or resources/js)
npm run dev

# Terminal 3 — Queue worker (if testing jobs/notifications)
php artisan queue:work

# Terminal 4 — Log streaming
php artisan pail
```

---

## Troubleshooting

### "Class 'Spatie\Permission\PermissionRegistrar' not found"

```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### Roles/permissions not working after seeder

```bash
php artisan cache:clear
php artisan roles:setup
```

### Spatial queries failing

Ensure your MySQL version supports spatial functions and that the `coordinates` column was created correctly. Run:

```bash
php artisan migrate:fresh --seed
```

If using SQLite for local dev, zone-based filtering will be skipped automatically — this is expected.

### Storage link missing (uploaded images not visible)

```bash
php artisan storage:link
```
