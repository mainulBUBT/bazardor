<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MarketStoreUpdateRequest;
use Illuminate\Http\Request;
use App\Enums\Location;
use App\Models\Market;
use App\Services\MarketService;

class MarketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("admin.markets.index");
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
    public function store(MarketStoreUpdateRequest $request, MarketService $marketService)
    {
        $marketService->store($request->validated(), $request);
        return redirect()->route('admin.markets.index')->with('success', 'Market created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $market = Market::findOrFail($id);
        $divisions = Location::getDivisions();
        // Decode JSON fields for easier use in the view
        $market->location = json_decode($market->location);
        $market->opening_hours = json_decode($market->opening_hours, true);

        return view('admin.markets.edit', compact('market', 'divisions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MarketStoreUpdateRequest $request, MarketService $marketService, string $id)
    {
        try {
            $marketService->update($request->validated(), $request, $id);
            return redirect()->route('admin.markets.index')->with('success', 'Market updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update market: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
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
