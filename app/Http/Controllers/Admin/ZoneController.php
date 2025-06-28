<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ZoneStoreUpdateRequest;
use App\Services\ZoneService;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class ZoneController extends Controller
{
    public function __construct(protected ZoneService $zoneService)
    {
    }

    /**
     * Display a listing of the zones.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $zones = $this->zoneService->getZones($request->search);
        return view('admin.zones.index', compact('zones'));
    }

    /**
     * Store a newly created zone in storage.
     *
     * @param  \App\Http\Requests\ZoneStoreUpdateRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ZoneStoreUpdateRequest $request)
    {
        $data = [
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
        ];
        
        $zone = $this->zoneService->store($data);
        
        return redirect()->route('admin.zones.index')
            ->with('success', translate('messages.zone_created_successfully'));
    }

    /**
     * Display the specified zone.
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $zone = $this->zoneService->findById($id);
        return view('admin.zones.show', compact('zone'));
    }

    /**
     * Show the form for editing the specified zone.
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $zone = $this->zoneService->findById($id);
        $markets = $this->zoneService->getAvailableMarkets();
        $zoneMarketIds = $zone->markets->pluck('id')->toArray();
        
        return view('admin.zones.edit', compact('zone', 'markets', 'zoneMarketIds'));
    }

    /**
     * Update the specified zone in storage.
     *
     * @param ZoneStoreUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ZoneStoreUpdateRequest $request, $id)
    {
        $this->zoneService->update($id, $request->validated());
        Toastr::success();
        return redirect()->route('admin.zones.index');
    }

    /**
     * Remove the specified zone from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $this->zoneService->delete($id);
        Toastr::success();
        return redirect()->route('admin.zones.index');
    }

    /**
     * Toggle the status of the specified zone.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus($id)
    {
        $message = $this->zoneService->toggleStatus($id);
        Toastr::success();
        return redirect()->back();
    }
}