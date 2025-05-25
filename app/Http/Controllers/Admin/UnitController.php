<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UnitStoreUpdateRequest;
use App\Services\UnitService;
use Brian2694\Toastr\Facades\Toastr;

class UnitController extends Controller
{
    public function __construct(protected UnitService $unitService) {

    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = $this->unitService->getUnits();
        return view("admin.units.index", compact('units'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UnitStoreUpdateRequest $unitStoreUpdateRequest)
    {
        $this->unitService->storeUnit($unitStoreUpdateRequest->validated());

        Toastr::success(translate("messages.Unit created successfully"));
        return redirect()->route("admin.units.index");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $unit = $this->unitService->findById($id);
        return view("admin.units.edit", compact('unit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UnitStoreUpdateRequest $unitStoreUpdateRequest, string $id)
    {
        $this->unitService->updateUnit($unitStoreUpdateRequest->validated(), $id);

        Toastr::success(translate("messages.Unit updated successfully"));
        return redirect()->route("admin.units.index");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->unitService->deleteUnit($id);

        Toastr::success(translate("messages.Unit deleted successfully"));
        return redirect()->route("admin.units.index");
    }
}
