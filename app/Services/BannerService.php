<?php

namespace App\Services;

use App\Models\Banner;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\CentralLogics\Helpers;

class BannerService
{
    public function __construct(private Banner $banner)
    {
        
    }

    /**
     * Summary of getBanners
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getBanners()
    {
        return $this->banner->latest()->paginate(pagination_limit());
    }

    /**
     * Store a new banner.
     */
    public function store(array $data): Banner
    {
        // Handle file uploads
        if (isset($data['image']) && $data['image']->isValid()) {
            $data['image_path'] = handle_file_upload('banners/', $data['image']->getClientOriginalExtension());
        }
        $banner = Banner::create($data);
        unset($data['image']);
        
        $banner->title = $data['title'];
        $banner->image_path = $data['image_path'];
        $banner->url = $data['url'];
        $banner->type = 'general';
        $banner->description = $data['description'];
        $banner->is_active = $data['is_active'];
        $banner->position = $data['position'];
        $banner->start_date = $data['start_date'];
        $banner->end_date = $data['end_date'];
        $banner->save();
        return $banner;
    }

    /**
     * Update an existing banner.
     */
    public function update(Banner $banner, array $data): Banner
    {
        if (isset($data['image']) && $data['image']->isValid()) {
            $data['image_path'] = handle_file_upload('banners/', $data['image']->getClientOriginalExtension());
        }
        unset($data['image']);
        $banner->title = $data['title'];
        $banner->image_path = $data['image_path'];
        $banner->url = $data['url'];
        $banner->type = 'general';
        $banner->description = $data['description'];
        $banner->is_active = $data['is_active'];
        $banner->position = $data['position'];
        $banner->start_date = $data['start_date'];
        $banner->end_date = $data['end_date'];
        $banner->save();
        return $banner;
    }

    /**
     * Delete a banner and its image.
     */
    public function delete(int $bannerId): void
    {
        $banner = $this->banner->findOrFail($bannerId);
        if ($banner->image_path) {
            handle_file_upload('banners/', '', null, $banner->image_path);
        }
        $banner->delete();
    }

    /**
     * Summary of status
     * @param \App\Models\Banner $banner
     * @return void
     */
    public function status(int $bannerId, $status): void
    {
        $banner = $this->banner->findOrFail($bannerId);
        $banner->is_active = $status;
        $banner->save();
    }   
}
