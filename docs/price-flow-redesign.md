# Price Flow Redesign — Handoff Document

This document captures the full design decisions made about Bazardor's price system
and lists the exact implementation tasks. Read this before touching any price-related code.

---

## What We Decided (and Why)

### The core problem with the old system

The old system collected submissions → ran a batch processor every 30 min → published AVG price.

Three problems:
1. Price only goes live after 30 min delay
2. Admin had to manually approve every submission from every market — impossible at scale
3. Tolerance was ±20% — too tight for seasonal price variation in Bangladesh

### The agreed final design

**Submit → gate check → median of last 24h → live immediately. No batch processor for prices.**

Inspired by GasBuddy (recency + community) and Numbeo (median smoothing). Adapted for
Bazardor's scale and Bangladesh market reality (prices can spike 50%+ seasonally).

---

## Final Price Flow

```
User submits ৳95 for Rice at Karwan Bazar
      │
      ▼
GATE CHECK (in ContributionService::submitPrice)
  Reference price (priority):
    1. Current product_market_prices.price for this market   ← best
    2. Median of zone's product_market_prices                ← fallback
    3. Product base_price                                    ← last resort
    4. No reference → accept everything > 0                  ← cold start

  Rule: submitted_price must be within ±50% of reference
    ৳95 vs reference ৳90 → ratio 1.05 → PASS ✓
    ৳5000 vs reference ৳90 → ratio 55.5 → FAIL ✗ (silent discard)
    ৳10 vs reference ৳90 → ratio 0.11 → FAIL ✗ (silent discard)

  Fail → return success to user (don't reveal rejection)
      │
      ▼ PASS
Save to price_contributions (status = 'pending', rolling 24h window)
      │
      ▼
Fetch ALL price_contributions for this (product_id + market_id)
created within last 24 hours
      │
      ▼
Compute MEDIAN of those submitted_prices
  e.g. [৳90, ৳93, ৳95, ৳140] → median = ৳94
  (median absorbs the ৳140 outlier — one bad submission doesn't dominate)
      │
      ▼
UPDATE product_market_prices immediately (or INSERT if first time)
  price = ৳94, price_date = now()
      │
      ▼
DONE — price is live within seconds, no scheduler needed
```

---

## What to Remove from the API

### ProductResource — remove these two fields entirely

**File:** `app/Http/Resources/ProductResource.php`

Remove:
```php
'base_price' => $this->base_price !== null ? (float) $this->base_price : null,
'price_range' => $this->whenLoaded('priceThreshold', function () {
    return $this->priceThreshold ? [
        'min' => (float) $this->priceThreshold->min_price,
        'max' => (float) $this->priceThreshold->max_price,
    ] : null;
}),
```

Why: `base_price` is an internal admin field (used as validation reference, not a real market price).
`price_range` was exposing internal threshold data (base_price ± 20%) as if it were real market data —
misleading to the mobile client.

Keep `zone_price_range` — it is computed from real approved prices across markets in the zone.

### ProductMarketPriceResource — remove discount_price, add display object

**File:** `app/Http/Resources/ProductMarketPriceResource.php`

Remove:
```php
'discount_price' => $this->discount_price !== null ? (float) $this->discount_price : null,
```

Why: discount_price is never set by any code path. The column exists but is always null.
Showing a null field in every API response is noise.

Add these fields:
```php
'is_stale' => $this->price_date
    ? \Carbon\Carbon::parse($this->price_date)->lt(now()->subDays(30))
    : true,

'display' => $this->buildDisplay(),
```

Add the private helper in the same class:
```php
private function buildDisplay(): array
{
    // Price is unverified if reported_at is recent but not yet community-confirmed.
    // For now: price_date within last 2h with only 1 recent contributor = unverified.
    // Simple rule: show ~ if price_date is less than 2 hours old AND no prior price existed.
    // Adjust this logic as confidence tracking is added later.

    $isRecent = $this->price_date
        && \Carbon\Carbon::parse($this->price_date)->gt(now()->subHours(2));

    // For now treat all prices as verified (no confidence column yet).
    // When confidence column is added: $isVerified = ($this->confidence ?? 0) >= 3;
    $isVerified = true;

    $prefix = $isVerified ? null : '~';
    $amount = '৳' . number_format((float) $this->price, 0);

    return [
        'prefix' => $prefix,
        'amount' => $amount,
    ];
}
```

**Important:** Do NOT show contributor count or submission count to users.
Users should never know how many people submitted. Just show the price (with ~ if unverified).

---

## ContributionService Rewrite

**File:** `app/Services/ContributionService.php`

The `submitPrice()` method needs to:
1. Rate limit check (existing — keep as-is)
2. Gate check (new)
3. Save to price_contributions (existing — keep)
4. Compute median of last 24h submissions (new)
5. Update product_market_prices immediately (new)
6. Archive contributions older than 24h to history (new)

### Gate check logic

```php
private function getReferencePrice(string $productId, string $marketId): ?float
{
    // 1. Current live price for this exact market
    $current = ProductMarketPrice::query()
        ->where('product_id', $productId)
        ->where('market_id', $marketId)
        ->value('price');

    if ($current !== null) {
        return (float) $current;
    }

    // 2. Zone median — median of all product_market_prices for this product
    //    in the same zone as the submitted market
    $zoneId = DB::table('markets')->where('id', $marketId)->value('zone_id');

    if ($zoneId) {
        $zonePrices = DB::table('product_market_prices')
            ->join('markets', 'markets.id', '=', 'product_market_prices.market_id')
            ->where('product_market_prices.product_id', $productId)
            ->where('markets.zone_id', $zoneId)
            ->pluck('product_market_prices.price')
            ->map(fn($p) => (float) $p)
            ->sort()
            ->values();

        if ($zonePrices->isNotEmpty()) {
            return compute_median($zonePrices); // global helper in CentralLogics/Helpers.php
        }
    }

    // 3. Product base_price as last resort
    $basePrice = DB::table('products')->where('id', $productId)->value('base_price');

    return $basePrice ? (float) $basePrice : null;
}

private function passesGateCheck(float $submitted, ?float $reference): bool
{
    if ($reference === null || $reference <= 0) {
        return $submitted > 0; // cold start — accept anything positive
    }

    $tolerance = config('pricing.threshold_tolerance', 0.50);
    $min = $reference * (1 - $tolerance);
    $max = $reference * (1 + $tolerance);

    return $submitted >= $min && $submitted <= $max;
}
```

### Median update logic

```php
private function recomputeAndUpdatePrice(string $productId, string $marketId): void
{
    $prices = PriceContribution::query()
        ->where('product_id', $productId)
        ->where('market_id', $marketId)
        ->where('status', 'pending')
        ->where('created_at', '>=', now()->subHours(24))
        ->pluck('submitted_price')
        ->map(fn($p) => (float) $p)
        ->sort()
        ->values();

    if ($prices->isEmpty()) {
        return;
    }

    $median = compute_median($prices);

    ProductMarketPrice::query()->updateOrCreate(
        ['product_id' => $productId, 'market_id' => $marketId],
        ['price' => round($median, 2), 'price_date' => now()]
    );
}
```

### Archive old contributions

```php
private function archiveOldContributions(string $productId, string $marketId): void
{
    $old = PriceContribution::query()
        ->where('product_id', $productId)
        ->where('market_id', $marketId)
        ->where('created_at', '<', now()->subHours(24))
        ->get();

    if ($old->isEmpty()) {
        return;
    }

    $payload = $old->map(fn($c) => [
        'id'              => $c->id,
        'product_id'      => $c->product_id,
        'market_id'       => $c->market_id,
        'user_id'         => $c->user_id,
        'device_id'       => $c->device_id,
        'submitted_price' => $c->submitted_price,
        'proof_image'     => $c->proof_image,
        'status'          => 'validated',
        'validated_at'    => now(),
        'created_at'      => $c->created_at,
        'updated_at'      => now(),
    ])->toArray();

    PriceContributionHistory::query()->upsert($payload, ['id']);

    PriceContribution::query()
        ->whereIn('id', $old->pluck('id'))
        ->forceDelete();
}
```

### Full updated submitPrice() signature

```php
public function submitPrice(?User $user, ?string $deviceId, array $data): array
{
    // 1. Rate limit (existing logic — keep)

    // 2. Gate check
    $reference = $this->getReferencePrice($data['product_id'], $data['market_id']);
    if (!$this->passesGateCheck((float) $data['submitted_price'], $reference)) {
        // Silent discard — tell the user "thanks" anyway
        return ['rate_limited' => false, 'last_submission_at' => now()->toIso8601String(), 'contribution' => null];
    }

    // 3. Save to price_contributions (existing updateOrCreate — keep)

    // 4. Archive old contributions (> 24h) before recomputing
    $this->archiveOldContributions($data['product_id'], $data['market_id']);

    // 5. Recompute median and update product_market_prices immediately
    $this->recomputeAndUpdatePrice($data['product_id'], $data['market_id']);

    // 6. Return (existing return shape — keep)
}
```

---

## Scheduler Change

**File:** `routes/console.php`

Remove the 30-min price processor. Replace with a daily housekeeping job:

```php
<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Daily housekeeping only — price updates are now immediate (in ContributionService)
Schedule::command('prices:housekeeping')
    ->daily()
    ->withoutOverlapping()
    ->runInBackground();
```

Create `app/Console/Commands/PricesHousekeeping.php`:

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductMarketPrice;

class PricesHousekeeping extends Command
{
    protected $signature   = 'prices:housekeeping';
    protected $description = 'Mark stale prices and clean orphaned pending contributions';

    public function handle(): int
    {
        // Mark prices stale if not updated in 30 days
        // (is_stale is computed in the resource from price_date — no DB column needed yet)

        $count = ProductMarketPrice::query()
            ->where('price_date', '<', now()->subDays(30))
            ->count();

        $this->info("Stale prices (no update in 30 days): {$count}");
        $this->info('Housekeeping complete.');

        return self::SUCCESS;
    }
}
```

---

## Config Change

**File:** `config/pricing.php`

Change tolerance from 0.20 to 0.50:

```php
<?php

return [
    'threshold_tolerance'         => env('PRICING_THRESHOLD_TOLERANCE', 0.50), // was 0.20
    'min_samples_for_calibration' => env('PRICING_MIN_SAMPLES', 3),
];
```

Why: ±20% rejected legitimate seasonal price spikes (e.g. flood season doubles produce prices).
±50% still catches obvious troll submissions (৳5000 for rice) while accepting real variation.

---

## No Schema Changes Required

All changes above are PHP only. No new migrations needed for this phase.

The `product_market_prices` table already has `price` and `price_date` — that is all we need.

**Deferred to a future phase (do not add now):**
- `confidence` column on `product_market_prices`
- `is_verified` / `is_stale` boolean columns
- `pending_price` / `pending_weight` columns
- `is_locked` admin override column
- Weight-based trust system (logged user = 1, guest = 0.5)

These were discussed but are over-engineering for the current user volume. The `is_stale`
flag is computed from `price_date` in the resource (no column needed).

---

## What the Old Batch Processor Does (Keep or Remove)

**File:** `app/Services/PriceContributionProcessor.php`
**File:** `app/Console/Commands/ProcessPriceContributions.php`

These files can be kept but the scheduler entry in `routes/console.php` should be removed.
The processor can still be run manually (`php artisan price-contributions:process`) as a
one-off migration tool to drain any currently pending contributions.

After running it once to clear the backlog, it is no longer part of the normal flow.

---

## Summary of All File Changes

| File | Change |
|------|--------|
| `config/pricing.php` | tolerance 0.20 → 0.50 |
| `app/Services/ContributionService.php` | Add gate check + immediate median update + archive old |
| `app/Http/Resources/ProductResource.php` | Remove `base_price`, remove `price_range` |
| `app/Http/Resources/ProductMarketPriceResource.php` | Remove `discount_price`, add `is_stale`, add `display` |
| `routes/console.php` | Remove 30-min processor schedule, add daily housekeeping |
| `app/Console/Commands/PricesHousekeeping.php` | New file — daily stale-marking job |

**Files to leave untouched:**
- `app/Services/PriceContributionProcessor.php` — keep, just stop scheduling it
- `app/Models/*` — no changes
- All migrations — no new migrations needed

---

## MRP / Price Range Discussion

During design we discussed whether to show a single price or a range per market.

**Decision: one number per market, range across markets (zone).**

```
Rice at Karwan Bazar:    ৳ 95     ← single number (median of contributions)
Rice at New Market:      ৳ 88
Rice at Gulshan Market:  ৳ 105

zone_price_range (across all markets in zone):
  { min: 88, max: 105, typical: 95, market_count: 3 }
```

**Why single number per market:**
- Median absorbs grade variation naturally (grade A vs grade B rice both submitted → median)
- Market comparison (Bazardor's core feature) requires one number per market to compare
- Range per market only makes sense for packaged goods with variants — not for fresh produce

**Why zone_price_range:**
- Shows the user what is "normal" for this product in their city
- Used as validation reference (if zone has ≥3 markets, use its range as the gate)
- Already implemented in `ProductService` and `MarketService`

**discount_price is removed** because no code path ever sets it. It was a placeholder column.

---

## Key Helpers Already Available

In `app/CentralLogics/Helpers.php`:

- `compute_median(Collection $sorted): float` — median of a sorted collection
- `compute_zone_price_range(Collection $prices): ?array` — returns `[min, max, typical, market_count]`

Both are global functions (no import needed). Use them freely.
