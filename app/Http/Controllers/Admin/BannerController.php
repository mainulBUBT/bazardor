<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Http\Requests\BannerStoreUpdateRequest;
use App\Services\BannerService;
use App\Services\ZoneService;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class BannerController extends Controller
{
    public function __construct(protected BannerService $bannerService, protected ZoneService $zoneService)
    {

    }

    /**
     * Display a listing of the banners.
     */
    public function index(Request $request)
    {
        $filters = [
            'type' => $request->query('type'),
            'is_active' => $request->query('is_active'),
            'sort' => $request->query('sort', 'latest'),
        ];
        
        $banners = $this->bannerService->getBanners(false, null, null, null, $filters);
        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Export banners to various formats.
     */
    public function export(Request $request)
    {
        $format = $request->query('format', 'xlsx');
        $extension = 'xlsx';
        $writerType = \Maatwebsite\Excel\Excel::XLSX;

        if ($format === 'csv') {
            $extension = 'csv';
            $writerType = \Maatwebsite\Excel\Excel::CSV;
        } elseif ($format === 'pdf') {
            $extension = 'pdf';
            $writerType = \Maatwebsite\Excel\Excel::MPDF;
        }

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\BannersExport, 
            'banners_' . date('Y-m-d_H-i-s') . '.' . $extension, 
            $writerType
        );
    }

    /**
     * Show the form for creating a new banner.
     */
    public function create()
    {
        $zones = $this->zoneService->getActiveZones();
        return view('admin.banners.create', compact('zones'));
    }

    /**
     * Store a newly created banner in storage.
     */
    public function store(BannerStoreUpdateRequest $request)
    {
        $validated = $request->validated();
        $this->bannerService->store($validated);

        Toastr::success(translate("messages.Banner created successfully!"));
        return redirect()->route('admin.banners.index');
    }

    /**
     * Show the form for editing the specified banner.
     */
    public function edit(Banner $banner)
    {
        $zones = $this->zoneService->getActiveZones();
        return view('admin.banners.edit', compact('banner', 'zones'));
    }

    /**
     * Update the specified banner in storage.
     */
    public function update(BannerStoreUpdateRequest $request, Banner $banner)
    {
        $validated = $request->validated();
        $this->bannerService->update($banner, $validated + ['image' => $request->file('image')]);
        Toastr::success(translate("messages.Banner updated successfully!"));
        return redirect()->route('admin.banners.index');
    }

    /**
     * Remove the specified banner from storage.
     */
    public function destroy($bannerId, Request $request)
    {
        $this->bannerService->delete($bannerId);
        Toastr::success(translate("messages.Banner deleted successfully!"));
        return redirect()->route('admin.banners.index');
    }

    public function status($bannerId, Request $request)
    {
        $this->bannerService->status($bannerId, $request->status);

        Toastr::success(translate("messages.Banner status updated successfully!"));
        return redirect()->back();
    }
}
