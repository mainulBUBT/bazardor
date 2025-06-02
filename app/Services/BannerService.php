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
     * Store a new banner.
     */
    public function store(array $data): Banner
    {
        // Handle file uploads
        if (isset($data['image']) && $data['image']->isValid()) {
            $data['image_path'] = handle_file_upload('banners/', $data['image']->getClientOriginalExtension());
        }

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image_path'] = handle_file_upload('banners/', $data['image']->getClientOriginalExtension(), $data['image']);
        }
        unset($data['image']);
        
        return Banner::create($data);
    }

    /**
     * Update an existing banner.
     */
    public function update(Banner $banner, array $data): Banner
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image_path'] = handle_file_upload('banners/', $data['image']->getClientOriginalExtension(), $data['image'], $banner->image_path);
        }
        unset($data['image']);
        $banner->update($data);
        return $banner;
    }

    /**
     * Delete a banner and its image.
     */
    public function delete(Banner $banner): void
    {
        if ($banner->image_path) {
            handle_file_upload('banners/', '', null, $banner->image_path);
        }
        $banner->delete();
    }
}
