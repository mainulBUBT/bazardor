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
    public function getUnits()
    {
        return Unit::latest()->paginate(pagination_limit());
    }
}