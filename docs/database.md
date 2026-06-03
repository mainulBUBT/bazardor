# Database

## Overview

The database contains **37 tables** across four categories:

| Category | Tables |
|----------|--------|
| Laravel infrastructure | `users`, `password_reset_tokens`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `personal_access_tokens` |
| Spatie permissions | `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions` |
| Domain (Bazardor) | `admins`, `settings`, `zones`, `units`, `categories`, `markets`, `market_information`, `market_operating_hours`, `products`, `product_tags`, `product_market_prices`, `price_thresholds`, `price_contributions`, `price_contribution_votes`, `price_contributions_history`, `banners`, `push_notifications`, `favorites`, `entity_creators`, `user_statistics` |
| Translations | `product_translations`, `market_translations`, `category_translations`, `banner_translations`, `zone_translations`, `unit_translations`, `product_tag_translations` |

> Note: `users` is a Laravel core table but is extended by two migrations (`add_extra_fields_to_users_table`, `add_social_login_fields_to_users_table`).

---

## Entity Relationship Summary

```
zones
 └── markets (zone_id)
      ├── market_information (market_id)
      ├── market_operating_hours (market_id)
      └── product_market_prices (market_id)
           └── products (product_id)
                ├── categories (category_id)
                ├── units (unit_id)
                ├── product_tags (product_id)
                ├── price_thresholds (product_id, 1:1)
                └── price_contributions (product_id + market_id)
                     └── price_contribution_votes (price_contribution_id)

users
 ├── favorites (user_id) → morphTo → Market | Product
 ├── price_contributions (user_id, nullable)
 ├── price_contribution_votes (user_id)
 ├── user_statistics (user_id, 1:1)
 └── entity_creators (user_id) → morphTo → Market | Product

admins
 └── entity_creators (admin_id) → morphTo → Market | Product

banners → zones (zone_id, nullable)
push_notifications → zones (zone_id, nullable)

Translation tables (1:N per locale):
products → product_translations (product_id)
markets → market_translations (market_id)
categories → category_translations (category_id)
banners → banner_translations (banner_id)
zones → zone_translations (zone_id)
units → unit_translations (unit_id)
product_tags → product_tag_translations (product_tag_id)
```

---

## Table Definitions

### `users`

| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements | PK |
| name | string | |
| email | string | Unique |
| email_verified_at | timestamp | Nullable |
| password | string | Hashed |
| remember_token | string | Nullable |
| phone | string | Nullable (extra fields migration) |
| dob | date | Nullable |
| username | string | Unique, Nullable |
| referral_code | string | Unique, Nullable |
| user_type | string | `super_admin`, `moderator`, `volunteer`, `user` |
| image | string | Nullable |
| is_active | boolean | Default true |
| device_id | string | Nullable |
| provider | string | Nullable (social login) |
| provider_id | string | Nullable (social login) |
| provider_token | string | Nullable |
| role_id | foreignId | Nullable → Spatie `roles.id` |
| created_at / updated_at | timestamps | |

---

### `admins`

| Column | Type | Notes |
|--------|------|-------|
| id | uuid | PK (HasUuid trait) |
| name | string | |
| email | string | Unique |
| password | string | Hashed |
| is_active | boolean | Default true |
| created_at / updated_at | timestamps | |

Uses `HasRoles` (Spatie) with guard `admin`.

---

### `settings`

| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements | PK |
| key_name | string(191) | |
| value | json | Nullable, cast to array |
| settings_type | string(191) | Nullable, indexed |
| created_at / updated_at | timestamps | |

---

### `zones`

| Column | Type | Notes |
|--------|------|-------|
| id | uuid | PK |
| name | string | |
| description | text | Nullable |
| is_active | boolean | Default true |
| coordinates | Polygon | PostGIS spatial column |
| created_at / updated_at | timestamps | |

---

### `units`

| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements | PK |
| name | string | Unique |
| symbol | string | Unique, Indexed |
| unit_type | string | Nullable, Indexed |
| is_active | boolean | Default true |
| deleted_at | softDeletes | |
| created_at / updated_at | timestamps | |

---

### `categories`

| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements | PK |
| name | string | Indexed |
| slug | string | Unique |
| description | text | Nullable |
| image_path | string | Nullable |
| is_active | boolean | Default true |
| position | integer | Default 0, Indexed |
| parent_id | foreignId | Nullable → `categories.id` |
| deleted_at | softDeletes | |
| created_at / updated_at | timestamps | |

---

### `markets`

| Column | Type | Notes |
|--------|------|-------|
| id | uuid | PK |
| name | string | |
| slug | string | Unique |
| description | text | Nullable |
| image_path | string | Nullable |
| location | string | Nullable |
| type | string | Nullable |
| address | string | Nullable |
| latitude | decimal(10,8) | Nullable |
| longitude | decimal(11,8) | Nullable |
| phone | string | Nullable |
| email | string | Nullable |
| website | string | Nullable |
| is_active | boolean | Default true |
| visibility | string | Nullable |
| is_featured | boolean | Default false |
| position | integer | Default 0 |
| division | string | Nullable |
| district | string | Nullable |
| upazila_or_thana | string | Nullable |
| zone_id | foreignId | Nullable → `zones.id` |
| deleted_at | softDeletes | |
| created_at / updated_at | timestamps | |

---

### `market_information`

| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements | PK |
| market_id | foreignId | → `markets.id` |
| is_non_veg | boolean | Cast to bool |
| is_halal | boolean | Cast to bool |
| is_parking | boolean | Cast to bool |
| is_restroom | boolean | Cast to bool |
| is_home_delivery | boolean | Cast to bool |
| created_at / updated_at | timestamps | |

---

### `market_operating_hours`

| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements | PK |
| market_id | foreignId | → `markets.id` |
| day | string | e.g. `monday` |
| opening | time | Nullable |
| closing | time | Nullable |
| is_closed | boolean | Default false |
| created_at / updated_at | timestamps | |

---

### `products`

| Column | Type | Notes |
|--------|------|-------|
| id | uuid | PK |
| name | string | |
| slug | string | Unique, auto-generated |
| category_id | foreignId | → `categories.id` (cascade delete) |
| unit_id | foreignId | → `units.id` (cascade delete) |
| description | text | Nullable |
| status | enum | `active`, `inactive`, `draft` — default `active` |
| is_visible | boolean | Default true |
| is_featured | boolean | Default false |
| image_path | string | Nullable |
| sku | string | Unique, Nullable |
| barcode | string | Unique, Nullable |
| brand | string | Nullable |
| base_price | decimal(10,2) | Nullable |
| country_of_origin | string | Nullable |
| added_by | string | `user` or `admin` |
| added_by_id | string | UUID of creator |
| device_id | string | Nullable (anonymous submission) |
| deleted_at | softDeletes | |
| created_at / updated_at | timestamps | |

---

### `product_tags`

| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements | PK |
| product_id | foreignId | → `products.id` |
| tag | string | |
| Unique | (product_id, tag) | |
| Index | tag | |
| created_at / updated_at | timestamps | |

---

### `product_market_prices`

| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements | PK |
| product_id | foreignId | → `products.id` |
| market_id | foreignId | → `markets.id` |
| price | decimal(10,2) | |
| discount_price | decimal(10,2) | Nullable |
| price_date | timestamp | Default now |
| Unique | (product_id, market_id, price_date) | |
| Index | price, price_date, (product_id, market_id) | Composite indexes |
| created_at / updated_at | timestamps | |

---

### `price_thresholds`

| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements | PK |
| product_id | foreignId | → `products.id` |
| min_price | decimal(10,2) | |
| max_price | decimal(10,2) | |
| Index | product_id | |
| created_at / updated_at | timestamps | |

One threshold per product (enforced at application level via `ensureDefaultPriceThreshold()`).

---

### `price_contributions`

| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements | PK |
| product_id | foreignId | → `products.id` |
| market_id | foreignId | → `markets.id` |
| user_id | foreignId | Nullable → `users.id` |
| device_id | string | Nullable (guest submissions) |
| submitted_price | decimal(10,2) | |
| proof_image | string | Nullable |
| status | enum | `pending`, `approved`, `rejected` — default `pending` |
| upvotes | integer | Default 0 |
| downvotes | integer | Default 0 |
| verified_at | timestamp | Nullable |
| Index | (product_id, market_id, status), user_id, created_at | |
| deleted_at | softDeletes | |
| created_at / updated_at | timestamps | |

---

### `price_contribution_votes`

| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements | PK |
| price_contribution_id | foreignId | → `price_contributions.id` (cascade delete) |
| user_id | foreignId | → `users.id` |
| is_upvote | boolean | |
| Unique | (price_contribution_id, user_id) | One vote per user per contribution |
| created_at / updated_at | timestamps | |

---

### `price_contributions_history`

Archive table for processed contributions (approved or rejected).

| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements | PK |
| product_id | foreignId | |
| market_id | foreignId | |
| user_id | foreignId | Nullable |
| device_id | string | Nullable |
| submitted_price | decimal(10,2) | |
| proof_image | string | Nullable |
| status | enum | `validated`, `invalid` |
| validated_at | timestamp | Nullable |
| created_at / updated_at | timestamps | |

---

### `banners`

| Column | Type | Notes |
|--------|------|-------|
| id | uuid | PK |
| title | string | |
| image_path | string | |
| url | string | Nullable |
| type | string | `featured` or `general` |
| is_active | boolean | Default true |
| position | integer | Default 0 |
| start_date | date | Nullable |
| end_date | date | Nullable |
| zone_id | foreignId | Nullable → `zones.id` |
| deleted_at | softDeletes | |
| created_at / updated_at | timestamps | |

---

### `push_notifications`

| Column | Type | Notes |
|--------|------|-------|
| id | uuid | PK |
| title | string | |
| message | text | |
| type | string | |
| target_audience | string | |
| zone_id | foreignId | Nullable → `zones.id` |
| link_url | string | Nullable |
| image | string | Nullable |
| sent_at | timestamp | Nullable |
| status | string | `draft`, `sent`, etc. |
| recipients_count | integer | Default 0 |
| opened_count | integer | Default 0 |
| created_by | foreignId | → `users.id` |
| created_at / updated_at | timestamps | |

---

### `favorites`

| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements | PK |
| user_id | foreignId | → `users.id` |
| favoritable_type | string | `App\Models\Market` or `App\Models\Product` |
| favoritable_id | string | UUID of the target |
| created_at / updated_at | timestamps | |

---

### `entity_creators`

Tracks who created a market or product (User or Admin).

| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements | PK |
| user_id | foreignId | Nullable → `users.id` |
| creatable_id | string | UUID of the created entity |
| creatable_type | string | `App\Models\Market` or `App\Models\Product` |
| created_at / updated_at | timestamps | |

---

### `user_statistics`

| Column | Type | Notes |
|--------|------|-------|
| user_id | foreignId | PK (non-incrementing) → `users.id` |
| price_updates_count | integer | Default 0 |
| reviews_count | integer | Default 0 |
| products_added_count | integer | Default 0 |
| accurate_contributions_count | integer | Default 0 |
| inaccurate_contributions_count | integer | Default 0 |
| reputation_score | decimal | Default 0 |
| tier | string | Nullable |
| last_price_update_at | timestamp | Nullable |
| created_at / updated_at | timestamps | |

---

### Spatie Permission Tables

| Table | Description |
|-------|-------------|
| `roles` | Role records (`super_admin`, `moderator`, functional roles) |
| `permissions` | Granular permission strings (e.g. `create_products`) |
| `model_has_roles` | Pivot: User/Admin ↔ Role |
| `model_has_permissions` | Pivot: User/Admin ↔ Permission (direct assignment) |
| `role_has_permissions` | Pivot: Role ↔ Permission |

---

### `personal_access_tokens`

Standard Sanctum table. `tokenable_id` was migrated to `uuid` type.

---

## Translation Tables

All translation tables follow the same pattern: companion to a translatable model, one row per entity per locale. Managed by the `astrotomic/laravel-translatable` package.

### Common schema pattern

| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements | PK |
| {entity}_id | uuid | FK → parent table, cascade delete |
| locale | string(10) | Indexed (e.g. `en`, `bn`) |
| + translatable columns | varies | Model-specific (see below) |
| Unique | ({entity}_id, locale) | One translation per entity per locale |

No timestamps on translation rows.

### `product_translations`

| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements | PK |
| product_id | uuid | FK → `products.id` (cascade) |
| locale | string(10) | Indexed |
| name | string | |
| description | text | Nullable |
| brand | string | Nullable |
| Unique | (product_id, locale) | |
| Index | (locale, name) | |

### `market_translations`

| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements | PK |
| market_id | uuid | FK → `markets.id` (cascade) |
| locale | string(10) | Indexed |
| name | string | |
| description | text | Nullable |
| address | string | Nullable |
| Unique | (market_id, locale) | |
| Index | (locale, name) | |

### `category_translations`

| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements | PK |
| category_id | uuid | FK → `categories.id` (cascade) |
| locale | string(10) | Indexed |
| name | string | |
| description | text | Nullable |
| Unique | (category_id, locale) | |
| Index | (locale, name) | |

### `banner_translations`

| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements | PK |
| banner_id | uuid | FK → `banners.id` (cascade) |
| locale | string(10) | Indexed |
| title | string | |
| Unique | (banner_id, locale) | |

### `zone_translations`

| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements | PK |
| zone_id | uuid | FK → `zones.id` (cascade) |
| locale | string(10) | Indexed |
| name | string | |
| description | text | Nullable |
| Unique | (zone_id, locale) | |

### `unit_translations`

| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements | PK |
| unit_id | uuid | FK → `units.id` (cascade) |
| locale | string(10) | Indexed |
| name | string | |
| symbol | string | Nullable |
| Unique | (unit_id, locale) | |

### `product_tag_translations`

| Column | Type | Notes |
|--------|------|-------|
| id | bigIncrements | PK |
| product_tag_id | foreignId | FK → `product_tags.id` (cascade) |
| locale | string(10) | Indexed |
| tag | string | |
| Unique | (product_tag_id, locale) | |

---

### Laravel Infrastructure Tables

| Table | Description |
|-------|-------------|
| `sessions` | Database session storage |
| `cache` / `cache_locks` | Database cache driver |
| `jobs` / `job_batches` / `failed_jobs` | Database queue driver |
| `password_reset_tokens` | Password reset OTP storage (extended with extra columns: `otp`, `otp_expires_at`) |
