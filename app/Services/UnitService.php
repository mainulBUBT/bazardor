<?php

namespace App\Services;
use App\Models\Unit;
use App\Traits\SavesTranslations;

class UnitService
{
    use SavesTranslations;
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
        
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('symbol', 'like', "%{$search}%");
            });
        }
        
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
     * @param int|string $id
     * @return \App\Models\Unit
     */
    public function findById(int|string $id)
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
        $unitData = $this->stripTranslationFields($validated);
        $unit = $this->unit->create($unitData);

        $this->saveTranslations($unit, $validated, ['name', 'symbol']);

        return $unit;
    }

    /**
     * Summary of updateUnit
     * @param array $validated
     * @param int|string $id
     * @return Unit
     */
    public function updateUnit(array $validated, int|string $id)
    {
        $unit = $this->findById($id);
        $unitData = $this->stripTranslationFields($validated);
        $unit->update($unitData);

        $this->saveTranslations($unit, $validated, ['name', 'symbol']);

        return $unit;
    }

    /**
     * Remove translation-prefixed fields (e.g. name_bn, symbol_bn) from the data array
     * so they are not passed to Eloquent's create/update (which targets the base table).
     */
    protected function stripTranslationFields(array $data): array
    {
        $defaultLocale = get_default_locale();
        $translatableFields = ['name', 'symbol'];

        foreach ($translatableFields as $field) {
            foreach (array_keys($data) as $key) {
                if (preg_match('/^' . $field . '_(.+)$/', $key, $matches)) {
                    unset($data[$key]);
                }
            }
        }

        return $data;
    }

    /**
     * Summary of deleteUnit
     * @param int|string $id
     * @return void
     */
    public function deleteUnit(int|string $id)
    {
        $unit = $this->findById($id);
        $unit->delete();
    }   
}