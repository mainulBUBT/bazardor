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
        $filters = [];
        if ($request->has('is_active') && $request->is_active !== '') {
            $filters['is_active'] = (bool) $request->is_active;
        }
        
        $zones = $this->zoneService->getZones(
            search: $request->search,
            relations: ['markets'],
            filters: $filters
        );
        
        $otherZonesCoords = [];
        $activeZones = $zones->where('is_active', true);
        foreach ($activeZones as $activeZone) {
            if (!$activeZone->coordinates) {
                continue;
            }
            $raw = json_decode(json_encode($activeZone->coordinates), true);
            $coords = data_get($raw, 'coordinates.0', []);
            if (is_array($coords) && !empty($coords)) {
                $otherZonesCoords[] = [
                    'id' => $activeZone->id,
                    'name' => $activeZone->name,
                    'points' => array_map(function ($pair) {
                        return ['lat' => (float) $pair[1], 'lng' => (float) $pair[0]];
                    }, $coords),
                ];
            }
        }

        return view('admin.zones.index', compact('zones', 'otherZonesCoords'));
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
            'coordinates' => $request->coordinates,
        ];
        
        $this->zoneService->store($data);
        
        Toastr::success(translate('messages.zone_created_successfully'));
        return redirect()->route('admin.zones.index');
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


        // Format current zone coordinates for map
        $currentZoneCoords = [];
        if ($zone->coordinates) {
            $raw = json_decode(json_encode($zone->coordinates), true);
            $coords = data_get($raw, 'coordinates.0', []);
            if (is_array($coords)) {
                $currentZoneCoords = array_map(function ($pair) {
                    return ['lat' => (float) $pair[1], 'lng' => (float) $pair[0]];
                }, $coords);
            }
        }

        // Format other zones for map
        $activeZones = $this->zoneService->getActiveZones()->where('id', '<>', $id);
        $otherZonesCoords = [];
        foreach ($activeZones as $activeZone) {
            if (!$activeZone->coordinates) {
                continue;
            }
            $raw = json_decode(json_encode($activeZone->coordinates), true);
            $coords = data_get($raw, 'coordinates.0', []);
            if (is_array($coords) && !empty($coords)) {
                $otherZonesCoords[] = [
                    'id' => $activeZone->id,
                    'name' => $activeZone->name,
                    'points' => array_map(function ($pair) {
                        return ['lat' => (float) $pair[1], 'lng' => (float) $pair[0]];
                    }, $coords),
                ];
            }
        }

        return view('admin.zones.edit', [
            'zone' => $zone,
            'currentZoneCoords' => $currentZoneCoords,
            'otherZonesCoords' => $otherZonesCoords,
        ]);
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
        Toastr::success(translate('messages.zone_updated_successfully'));
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
        Toastr::success(translate('messages.zone_deleted_successfully'));
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
        $zone = $this->zoneService->toggleStatus($id);
        $message = $zone->is_active
            ? translate('messages.zone_activated_successfully')
            : translate('messages.zone_deactivated_successfully');

        Toastr::success($message);
        return redirect()->back();
    }
}