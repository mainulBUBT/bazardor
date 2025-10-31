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
     * @return LengthAwarePaginator
     */
    public function getZones(?string $search = null, array $relations = []): LengthAwarePaginator
    {
        return $this->zone->with($relations)
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

            $value = $data['coordinates'];
            foreach(explode('),(',trim($value,'()')) as $index=>$single_array){
                if($index == 0)
                {
                    $lastcord = explode(',',$single_array);
                }
                $coords = explode(',',$single_array);
                $polygon[] = new Point($coords[0], $coords[1]);
            }

            $polygon[] = new Point($lastcord[0], $lastcord[1]);
            $zone->coordinates = new Polygon([new LineString($polygon)]);
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
                'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : $zone->is_active,
            ]);

            if (! empty($data['coordinates'])) {
                $value = $data['coordinates'];
                foreach(explode('),(',trim($value,'()')) as $index=>$single_array){
                    if($index == 0)
                    {
                        $lastcord = explode(',',$single_array);
                    }
                    $coords = explode(',',$single_array);
                    $polygon[] = new Point($coords[0], $coords[1]);
                }

                $polygon[] = new Point($lastcord[0], $lastcord[1]);

                $zone->coordinates = new Polygon([new LineString($polygon)]);
                $zone->save();
            }
            
            DB::commit();
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


} 