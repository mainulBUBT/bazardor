<?php

namespace App\Services;

use App\Models\Zone;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;

class ZoneService
{

    public function __construct(private Zone $zone)
    {
        
    }

    /**
     * Get all zones with pagination
     *
     * @param string|null $search
     * @param array $relations
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getZones(?string $search = null, array $relations = [], array $filters = []): LengthAwarePaginator
    {
        return $this->zone->with($relations)
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when(isset($filters['is_active']), function ($query) use ($filters) {
                $query->where('is_active', $filters['is_active']);
            })
            ->latest()
            ->paginate(pagination_limit());
    }

    /**
     * Get all active zones
     *
     * @return Collection
     */
    public function getActiveZones(array $relations = []): Collection
    {
        return $this->zone->active()->with($relations)->get();
    }

    /**
     * Find zone by ID
     *
     * @param int|string $id
     * @return Zone
     */
    public function findById(int|string $id, array $relations = []): Zone
    {
        return $this->zone->with($relations)->findOrFail($id);
    }

    /**
     * Store a new zone.
     *
     * @param array $data
     * @return Zone
     */
    public function store(array $data): Zone
    {
        DB::beginTransaction();
        try {
            $zone = new Zone();
            $zone->name = $data['name'];
            $zone->is_active = $data['is_active'] ?? true;

            if (! empty($data['coordinates'])) {
                $zone->coordinates = $this->parseCoordinatesToPolygon($data['coordinates']);
            }
            $zone->save();

            // Save translations for non-default locales
            $this->saveTranslations($zone, $data);

            DB::commit();
            return $zone;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing zone
     *
     * @param int|string $id
     * @param array $data
     * @return Zone
     */
    public function update(int|string $id, array $data): Zone
    {
        $zone = $this->findById($id);

        DB::beginTransaction();

        try {
            $zone->update([
                'name' => $data['name'],
                'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : $zone->is_active,
            ]);

            if (! empty($data['coordinates'])) {
                $zone->coordinates = $this->parseCoordinatesToPolygon($data['coordinates']);
                $zone->save();
            }

            // Save translations for non-default locales
            $this->saveTranslations($zone, $data);

            DB::commit();
            return $zone;
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error($e->getMessage() ?: translate('messages.failed_to_update_zone'));
            throw $e;
        }
    }

    /**
     * Delete a zone
     *
     * @param int|string $id
     * @return bool
     */
    public function delete(int|string $id): bool
    {
        $zone = $this->findById($id);
        
        DB::beginTransaction();
        
        try {
            $result = $zone->delete();
            
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error($e->getMessage() ?: translate('messages.failed_to_delete_zone'));
            throw $e;
        }
    }

    /**
     * Toggle zone active status
     *
     * @param int|string $id
     * @return Zone
     */
    public function toggleStatus(int|string $id): Zone
    {
        $zone = $this->findById($id);
        $zone->is_active = !$zone->is_active;
        $zone->save();
        
        return $zone;
    }

    /**
     * Get zone by coordinates (latitude, longitude)
     * Returns the first active zone containing the given point
     *
     * @param float $latitude
     * @param float $longitude
     * @return Zone|null
     */
    public function getZoneByCoordinates(float $latitude, float $longitude): ?Zone
    {
        $point = new Point($latitude, $longitude, POINT_SRID);

        return $this->zone
            ->whereContains('coordinates', $point)
            ->where('is_active', 1)
            ->select('id', 'name', 'is_active', 'coordinates')
            ->latest()
            ->first();
    }

    /**
     * Parse coordinate input into a spatial Polygon.
     * Supports two formats:
     *   - JSON: [{"lat":23.81,"lng":90.41}, ...]  (from JS parseCoordinateInput or edit hidden input)
     *   - String: (lat, lng),(lat, lng), ...       (from JS stringifyLatLngs)
     *
     * @param string $value
     * @return Polygon
     * @throws \InvalidArgumentException if fewer than 3 distinct points
     */
    protected function parseCoordinatesToPolygon(string $value): Polygon
    {
        $points = [];

        // Try JSON first
        $decoded = json_decode($value, true);
        if (is_array($decoded) && !empty($decoded)) {
            foreach ($decoded as $pair) {
                if (isset($pair['lat'], $pair['lng'])) {
                    $points[] = new Point((float) $pair['lat'], (float) $pair['lng']);
                }
            }
        } else {
            // Fallback: "(lat, lng),(lat, lng), ..." format
            $segments = explode('),(', trim($value, '()'));
            foreach ($segments as $segment) {
                $coords = explode(',', $segment);
                if (count($coords) >= 2) {
                    $lat = (float) trim($coords[0]);
                    $lng = (float) trim($coords[1]);
                    if ($lat !== 0.0 || $lng !== 0.0) {
                        $points[] = new Point($lat, $lng);
                    }
                }
            }
        }

        if (count($points) < 3) {
            throw new \InvalidArgumentException('A polygon requires at least 3 distinct coordinate points.');
        }

        // Close the polygon if first and last points differ
        $first = $points[0];
        $last = $points[count($points) - 1];
        if ($first->latitude !== $last->latitude || $first->longitude !== $last->longitude) {
            $points[] = new Point($first->latitude, $first->longitude);
        }

        return new Polygon([new LineString($points)]);
    }

    /**
     * Save translations for all non-default locales detected from submitted data.
     */
    protected function saveTranslations(Zone $zone, array $data): void
    {
        $defaultLocale = get_default_locale();
        $translatableFields = ['name', 'description'];
        $localeSuffixes = [];

        foreach ($translatableFields as $field) {
            foreach (array_keys($data) as $key) {
                if (preg_match('/^' . $field . '_(.+)$/', $key, $matches)) {
                    $localeSuffixes[$matches[1]] = true;
                }
            }
        }

        foreach (array_keys($localeSuffixes) as $locale) {
            if ($locale === $defaultLocale) {
                continue;
            }

            $hasData = false;
            $translation = $zone->translateOrNew($locale);
            foreach ($translatableFields as $field) {
                $key = "{$field}_{$locale}";
                if (isset($data[$key])) {
                    $translation->setAttribute($field, $data[$key]);
                    $hasData = true;
                }
            }
            if (!$hasData && $translation->exists) {
                $translation->delete();
            } elseif ($hasData) {
                $translation->save();
            }
        }
    }
} 