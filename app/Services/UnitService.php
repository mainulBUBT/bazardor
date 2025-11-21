<?php

namespace App\Services;
use App\Models\Unit;
class UnitService
{
    public function __construct(private Unit $unit)  
    {
        
    }
    
    /**
     * Summary of getUnits
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getUnits(array $filters = [])
    {
        $query = Unit::query();
        
        if (!empty($filters['unit_type'])) {
            $query->where('unit_type', $filters['unit_type']);
        }
        
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', (bool) $filters['is_active']);
        }
        
        return $query->latest()->paginate(pagination_limit());
    }

    /**
     * Summary of findById
     * @param int $id
     * @return \App\Models\Unit
     */
    public function findById(int $id)
    {
        return $this->unit->findOrFail($id);
    }   
    
    /**
     * Summary of storeUnit
     * @param array $validated
     * @return Unit
     */
    public function storeUnit(array $validated)
    {
        return $this->unit->create($validated);
    }

    /**
     * Summary of updateUnit
     * @param array $validated
     * @param int $id
     * @return Unit
     */
    public function updateUnit(array $validated, int $id)
    {
        $unit = $this->findById($id);
        $unit->update($validated);
        return $unit;
    }

    /**
     * Summary of deleteUnit
     * @param int $id
     * @return void
     */
    public function deleteUnit(int $id)
    {
        $unit = $this->findById($id);
        $unit->delete();
    }   
}