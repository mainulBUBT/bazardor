<?php

namespace App\Services;

use App\Models\Banner;
use App\Models\Zone;
use App\Traits\SavesTranslations;
use Illuminate\Support\Facades\DB;

class BannerService
{
    use SavesTranslations;

    public function __construct(private Banner $banner) {}

    /**
     * Get paginated list of banners with optional filters.
     *
     * @param  mixed  $zoneId
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getBanners(bool $isFeatured = false, ?int $limit = null, ?int $offset = null, $zoneId = null, array $filters = [])
    {
        return $this->banner
            ->with('zones')
            ->when($isFeatured, function ($query) {
                $query->featured();
            })
            ->when(! is_null($zoneId), function ($query) use ($zoneId) {
                $query->whereHas('zones', fn ($q2) => $q2->where('zones.id', $zoneId));
            })
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', function ($query) use ($filters) {
                $query->where('is_active', $filters['is_active']);
            })
            ->when(isset($filters['is_featured']) && $filters['is_featured'] !== '', function ($query) use ($filters) {
                $query->where('is_featured', $filters['is_featured']);
            })
            ->when(! empty($filters['sort']), function ($query) use ($filters) {
                match ($filters['sort']) {
                    'title_asc' => $query->orderBy('title', 'asc'),
                    'title_desc' => $query->orderBy('title', 'desc'),
                    default => $query->latest(),
                };
            }, function ($query) {
                $query->latest();
            })
            ->paginate($limit ?? pagination_limit(), ['*'], 'page', $offset ?? 1);
    }

    /**
     * Store a new banner.
     */
    public function store(array $data): Banner
    {
        // Handle file uploads
        if (isset($data['image']) && $data['image']->isValid()) {
            $data['image_path'] = handle_file_upload('banners/', $data['image']->getClientOriginalExtension(), $data['image']);
        }
        unset($data['image']);

        $this->banner->title = $data['title'];
        $this->banner->image_path = $data['image_path'] ?? null;
        $this->banner->link = $data['link'] ?? null;
        $this->banner->is_active = $data['is_active'];
        $this->banner->is_featured = $data['is_featured'] ?? false;
        $this->banner->start_date = $data['start_date'] ?? null;
        $this->banner->end_date = $data['end_date'] ?? null;
        $this->banner->save();

        // Sync zones — if "all zones", insert every zone ID
        $zoneIds = $this->resolveZoneIds($data);
        $this->banner->zones()->sync($zoneIds);

        $this->saveTranslations($this->banner, $data, ['title']);

        return $this->banner;
    }

    /**
     * Update an existing banner.
     */
    public function update(Banner $banner, array $data): Banner
    {
        $oldImagePath = $banner->image_path;

        if (isset($data['image']) && $data['image']->isValid()) {
            $data['image_path'] = handle_file_upload('banners/', $data['image']->getClientOriginalExtension(), $data['image'], $oldImagePath);
        }
        unset($data['image']);

        $banner->title = $data['title'];
        $banner->image_path = $data['image_path'] ?? $banner->image_path;
        $banner->link = $data['link'] ?? null;
        $banner->is_active = $data['is_active'];
        $banner->is_featured = $data['is_featured'] ?? false;
        $banner->start_date = $data['start_date'] ?? null;
        $banner->end_date = $data['end_date'] ?? null;
        $banner->save();

        // Sync zones — if "all zones", insert every zone ID
        $zoneIds = $this->resolveZoneIds($data);
        $banner->zones()->sync($zoneIds);

        $this->saveTranslations($banner, $data, ['title']);

        return $banner;
    }

    /**
     * Delete a banner and its image.
     */
    public function delete(int|string $bannerId): void
    {
        $banner = $this->findById($bannerId);
        if ($banner->image_path) {
            handle_file_upload('banners/', '', null, $banner->image_path);
        }
        $banner->delete();
    }

    /**
     * Toggle banner status.
     */
    public function status(int|string $bannerId, $status): void
    {
        $banner = $this->findById($bannerId);
        $banner->is_active = $status;
        $banner->save();
    }

    /**
     * Find a banner by ID.
     */
    public function findById(int|string $bannerId): Banner
    {
        return $this->banner->with('zones')->findOrFail($bannerId);
    }

    /**
     * Resolve zone IDs from input data.
     * If "all" is present in zone_ids, return every zone ID.
     * Otherwise return the selected zone_ids.
     */
    protected function resolveZoneIds(array $data): array
    {
        $zoneIds = $data['zone_ids'] ?? [];

        if (in_array('all', $zoneIds)) {
            return Zone::pluck('id')->toArray();
        }

        return $zoneIds;
    }
}
