# API Reference

## Base URL

```
{APP_URL}/api
```

All API responses follow the standard envelope:

```json
{
  "success": true,
  "message": "...",
  "data": { ... }
}
```

---

## Authentication

### Bearer Token (Sanctum)

Authenticated endpoints require an `Authorization` header:

```
Authorization: Bearer <token>
```

The token is returned from `POST /api/auth/login` or `POST /api/auth/register`.

### Guest Tracking

Anonymous users can still submit prices. Send the device identifier in:

```
X-Device-ID: <uuid-or-any-unique-string>
```

The `ResolveGuestIdentifier` middleware reads this header and stores it on the request for downstream use.

---

## Auth Endpoints — `/api/auth`

### `POST /api/auth/register`

Create a new user account.

**Request body:**

| Field | Type | Required | Notes |
|-------|------|----------|-------|
| name | string | Yes | |
| email | string | Yes | Unique |
| password | string | Yes | Min 8 chars |
| password_confirmation | string | Yes | |
| phone | string | No | |

**Response:** `201` with `UserResource` + token

---

### `POST /api/auth/login`

Authenticate a user and receive a Sanctum token.

**Request body:**

| Field | Type | Required |
|-------|------|----------|
| email | string | Yes |
| password | string | Yes |

**Response:** `200` with `UserResource` + token

---

### `POST /api/auth/social-login`

Authenticate via Google or Facebook.

**Request body:**

| Field | Type | Required | Notes |
|-------|------|----------|-------|
| provider | string | Yes | `google` or `facebook` |
| access_token | string | Yes | OAuth access token from client SDK |

**Response:** `200` with `UserResource` + token

---

### `POST /api/auth/forgot-password`

Request a password-reset OTP sent to email.

**Request body:**

| Field | Type | Required |
|-------|------|----------|
| email | string | Yes |

**Response:** `200` with success message

---

### `POST /api/auth/reset-password`

Reset password using OTP.

**Request body:**

| Field | Type | Required |
|-------|------|----------|
| email | string | Yes |
| otp | string | Yes |
| password | string | Yes |
| password_confirmation | string | Yes |

**Response:** `200` with success message

---

### `POST /api/auth/otp-verification`

Verify OTP for email verification or password reset.

**Request body:**

| Field | Type | Required |
|-------|------|----------|
| email | string | Yes |
| otp | string | Yes |

**Response:** `200` with success message

---

### `POST /api/auth/logout`  _(requires auth)_

Revoke the current Sanctum token.

**Response:** `200`

---

## Config Endpoints — `/api/config`

### `GET /api/config`

Returns app configuration for the mobile client.

**Response includes:**
- Company name, logo, contact info
- Social login enable/disable flags
- Minimum app version requirements
- Any other `settings` rows of type `config`

---

### `GET /api/config/get-zone`

Find which zone a coordinate falls within.

**Query params:**

| Param | Type | Required |
|-------|------|----------|
| lat | numeric | Yes |
| lng | numeric | Yes |

**Response:** `ZoneResource`

---

## Categories — `/api/categories`

### `GET /api/categories/list`

List all active categories with product and market counts.

**Query params:**

| Param | Default | Notes |
|-------|---------|-------|
| per_page | 15 | Pagination size |
| search | — | Filter by name |

**Response:** Paginated `CategoryMarketResource` collection

---

### `GET /api/categories/get-category`

Get a single category by ID.

**Query params:**

| Param | Required |
|-------|----------|
| id | Yes |

**Response:** `CategoryResource`

---

## Banners — `/api/banners`

### `GET /api/banners/list`

List active banners filtered by zone and type.

**Query params:**

| Param | Notes |
|-------|-------|
| zone_id | Filter by zone |
| type | `featured` or `general` |
| per_page | Pagination size |

**Response:** Paginated `BannerResource` collection

---

## Markets — `/api/markets`

### `GET /api/markets/list`

List markets with filters, distance, and operating status.

**Query params:**

| Param | Notes |
|-------|-------|
| zone_id | Filter by zone |
| lat, lng | User coordinates (enables distance sort) |
| search | Filter by name |
| is_open | `1` to show only open markets |
| per_page | Pagination size |

**Response:** Paginated `MarketResource` collection (includes `operating_hours`, `market_information`, `zone`)

---

### `GET /api/markets/random-list`

Random selection of active markets in a zone.

**Query params:**

| Param | Notes |
|-------|-------|
| zone_id | Required for zone-scoped results |
| limit | Number to return (default 10) |

---

### `GET /api/markets/random-product-list`

Random product+price listing across markets in a zone.

**Query params:** `zone_id`, `limit`

---

### `GET /api/markets/compare`

Compare two markets side by side.

**Query params / body:**

| Param | Required |
|-------|----------|
| market_id_1 | Yes |
| market_id_2 | Yes |

**Response:** `MarketsComparisonResource`

---

### `GET /api/markets/compare-products`

Compare prices for products available in both markets.

**Query params / body:**

| Param | Required |
|-------|----------|
| market_id_1 | Yes |
| market_id_2 | Yes |
| category_id | No |

**Response:** `ProductComparisonResource` collection

---

## Market Detail — `/api/market`

### `GET /api/market/details/{id}`

Full details for a single market.

**Response:** `MarketResource` with nested `MarketInformation`, `MarketOperatingHour[]`, `Zone`

---

### `GET /api/market/products/{id}`

Products listed in a specific market with their latest prices.

**Query params:**

| Param | Notes |
|-------|-------|
| category_id | Filter by category |
| search | Filter by product name |
| per_page | Pagination size |

**Response:** Paginated `ProductMarketPriceResource` collection

---

## Units — `/api/units`

### `GET /api/units/list`

List all active measurement units.

**Query params:** `search`, `per_page`

**Response:** Paginated `UnitResource` collection

---

## User (Authenticated) — `/api/users`

All endpoints in this group require `Authorization: Bearer <token>`.

### `GET /api/users/profile`

Get the authenticated user's profile and statistics.

**Response:** `UserResource` (includes `UserStatistics`)

---

### `POST /api/users/update-profile`

Update the authenticated user's profile.

**Request body:**

| Field | Notes |
|-------|-------|
| name | Optional |
| phone | Optional |
| dob | Optional (date) |
| image | Optional (file upload) |

**Response:** Updated `UserResource`

---

### `GET /api/users/favorites/list`

List the user's favorited items (markets or products).

**Query params:** `type` (`market` or `product`), `per_page`

**Response:** Paginated `FavoriteResource` collection (with polymorphic `favoritable`)

---

### `POST /api/users/favorites/add`

Add an item to favorites.

**Request body:**

| Field | Type | Required |
|-------|------|----------|
| favoritable_type | string | Yes | `market` or `product` |
| favoritable_id | string | Yes | UUID of the target |

---

### `DELETE /api/users/favorites/remove`

Remove an item from favorites.

**Query params:** `favoritable_type`, `favoritable_id`

---

## Products — `/api/products`

### `POST /api/products/submit-price`

Submit a price contribution. Works for both authenticated users and anonymous guests (with `X-Device-ID`).

**Request body:**

| Field | Type | Required | Notes |
|-------|------|----------|-------|
| product_id | UUID | Yes | |
| market_id | UUID | Yes | |
| submitted_price | numeric | Yes | |
| proof_image | file | No | JPG/PNG |

**Response:** `201` with contribution ID

---

### `POST /api/products/create`

Submit a new product for review. The product is created with `status = draft` pending admin approval.

**Request body:**

| Field | Type | Required |
|-------|------|----------|
| name | string | Yes |
| category_id | UUID | Yes |
| unit_id | UUID | Yes |
| description | text | No |
| base_price | numeric | No |
| image | file | No |

**Response:** `201` with `ProductResource`

---

## Error Codes

| HTTP Status | Meaning |
|-------------|---------|
| 200 | Success |
| 201 | Created |
| 401 | Unauthenticated (invalid or missing token) |
| 403 | Forbidden (insufficient permissions) |
| 404 | Resource not found |
| 422 | Validation failed (body contains `errors` map) |
| 500 | Server error |
