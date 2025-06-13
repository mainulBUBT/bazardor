<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MarketStoreUpdateRequest;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\Enums\Location;
use App\Models\Market;
use App\Services\MarketService;

class MarketController extends Controller
{
    public function __construct(protected MarketService $marketService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $markets = $this->marketService->getMarkets();
        return view("admin.markets.index", compact('markets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $divisions = Location::getDivisions();
        return view("admin.markets.create", compact('divisions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MarketStoreUpdateRequest $request)
    {
        $this->marketService->store($request->validated());
        
        Toastr::success(translate("messages.market_created_successfully"));
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

    public function edit(string $id)
    {
        $market = $this->marketService->findById($id, ['marketInformation', 'openingHours']);
        $divisions = Location::getDivisions();
        $districts = Location::getDistricts($market->division);
        $upazilas = Location::getThanas($market->division, $market->district);


        return view('admin.markets.edit', compact('market', 'divisions', 'districts', 'upazilas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MarketStoreUpdateRequest $request, string $id)
    {
        $this->marketService->update($request->validated(), $id);
        Toastr::success(translate("messages.market_updated_successfully"));
        
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->marketService->delete($id);
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
