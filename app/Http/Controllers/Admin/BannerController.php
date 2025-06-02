<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Http\Requests\BannerStoreUpdateRequest;
use App\Services\BannerService;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class BannerController extends Controller
{
    public function __construct(protected BannerService $bannerService)
    {

    }

    /**
     * Display a listing of the banners.
     */
    public function index()
    {
        $banners = $this->bannerService->getBanners();
        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Show the form for creating a new banner.
     */
    public function create()
    {
        return view('admin.banners.create');
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
        return view('admin.banners.edit', compact('banner'));
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
