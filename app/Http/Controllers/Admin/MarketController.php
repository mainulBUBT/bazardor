<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MarketStoreUpdateRequest;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\Enums\Location;
use App\Models\Market;
use App\Services\MarketService;
use App\Services\ZoneService;

class MarketController extends Controller
{
    public function __construct(protected MarketService $marketService, protected ZoneService $zoneService)
    {
    }

    /**
     * Display a listing of the markets.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $markets = $this->marketService->getMarkets(['zone']);
        return view('admin.markets.index', compact('markets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $divisions = Location::getDivisions();
        $zones = $this->zoneService->getZones();
        return view("admin.markets.create", compact('divisions', 'zones'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MarketStoreUpdateRequest $request)
    {
        $this->marketService->store($request->validated());
        Toastr::success(translate('messages.market_created_successfully'));

        return redirect()->route('admin.markets.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $market = $this->marketService->findById($id, ['marketInformation', 'openingHours']);
        return view('admin.markets.show', compact('market'));
    }

    /**
     * Show the form for editing the specified market.
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $market = $this->marketService->findById($id);
        
        // Get location data for dropdowns
        $divisions = Location::getDivisions();
        $districts = Location::getDistricts($market->division ?? '');
        $upazilas = Location::getThanas($market->division ?? '', $market->district ?? '');
        $zones = $this->zoneService->getZones();
        
        return view('admin.markets.edit', compact('market', 'divisions', 'districts', 'upazilas', 'zones'));
    }

    /**
     * Update the specified market in storage.
     *
     * @param MarketStoreUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(MarketStoreUpdateRequest $request, $id)
    {  
        $this->marketService->update($request->validated(), $id);
        Toastr::success(translate('messages.market_updated_successfully'));
        
        return redirect()->route('admin.markets.index');
    }

    /**
     * Summary of destroy
     * @param string $id
     * @return mixed|\Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        $this->marketService->findById($id)->delete();
        Toastr::success(translate('messages.market_deleted_successfully'));
        
        return redirect()->route('admin.markets.index');
    }

    /**
     * Summary of getDistricts
     * @param mixed $division
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getDistricts($division)
    {
        $districts = Location::getDistricts($division);
        return response()->json($districts);
    }

    /**
     * Summary of getThanas
     * @param mixed $division
     * @param mixed $district
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getThanas($division, $district)
    {
        $thanas = Location::getThanas($division, $district);
        return response()->json($thanas);
    }
    
}
