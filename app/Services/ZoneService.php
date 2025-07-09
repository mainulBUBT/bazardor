<?php

namespace App\Services;

use App\Models\Zone;
use App\Models\Market;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;

class ZoneService
{

    public function __construct(private Zone $zone)
    {
        
    }

    /**
     * Get all zones with pagination
     *
     * @param string|null $search
     * @return LengthAwarePaginator
     */
    public function getZones(?string $search = null): LengthAwarePaginator
    {
        return $this->zone->with('markets')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(pagination_limit());
    }

    /**
     * Get all active zones
     *
     * @return Collection
     */
    public function getActiveZones(): Collection
    {
        return $this->zone->with('markets')->active()->get();
    }

    /**
     * Find zone by ID
     *
     * @param int|string $id
     * @return Zone
     */
    public function findById(int|string $id): Zone
    {
        return $this->zone->with('markets')->findOrFail($id);
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
            $zone->save();
            
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
                'description' => $data['description'] ?? $zone->description,
                'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : $zone->is_active,
            ]);

            // Update markets if provided
            if (isset($data['markets'])) {
                // First, remove this zone from all markets
                Market::where('zone_id', $zone->id)->update(['zone_id' => null]);
                
                // Then assign the zone to the selected markets
                if (is_array($data['markets']) && count($data['markets'])) {
                    Market::whereIn('id', $data['markets'])->update(['zone_id' => $zone->id]);
                }
            }
            
            DB::commit();
            return $zone->fresh(['markets']);
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
            // Remove zone reference from markets
            Market::where('zone_id', $zone->id)->update(['zone_id' => null]);
            
            // Delete the zone
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
     * Get all markets that can be assigned to zones
     *
     * @return Collection
     */
    public function getAvailableMarkets(): Collection
    {
        return Market::where('is_active', true)->get();
    }
} 