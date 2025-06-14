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
        unset($data['image']);
        
        $this->banner->title = $data['title'];
        $this->banner->image_path = $data['image_path'];
        $this->banner->url = $data['url'] ?? null;
        $this->banner->type = $data['type'] ?? 'general';
        $this->banner->description = $data['description'] ?? null;
        $this->banner->is_active = $data['is_active'];
        $this->banner->position = $data['position'];
        $this->banner->start_date = $data['start_date'] ?? null;
        $this->banner->end_date = $data['end_date'] ?? null;
        if (($data['type'] ?? 'general') === 'featured') {
            $this->banner->badge_text = $data['badge_text'] ?? null;
            $this->banner->badge_color = $data['badge_color'] ?? null;
            $this->banner->badge_background_color = $data['badge_background_color'] ?? null;
            $this->banner->badge_icon = $data['badge_icon'] ?? null;
            $this->banner->button_text = $data['button_text'] ?? null;
        } else {
            $this->banner->badge_text = null;
            $this->banner->badge_color = null;
            $this->banner->badge_background_color = null;
            $this->banner->badge_icon = null;
            $this->banner->button_text = null;
        }
        $this->banner->save();
        return $this->banner;
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
        $banner->url = $data['url'] ?? null;
        $banner->type = $data['type'] ?? 'general';
        $banner->description = $data['description'] ?? null;
        $banner->is_active = $data['is_active'];
        $banner->position = $data['position'];
        $banner->start_date = $data['start_date'] ?? null;
        $banner->end_date = $data['end_date'] ?? null;
        if (($data['type'] ?? 'general') === 'featured') {
            $banner->badge_text = $data['badge_text'] ?? null;
            $banner->badge_color = $data['badge_color'] ?? null;
            $banner->badge_background_color = $data['badge_background_color'] ?? null;
            $banner->badge_icon = $data['badge_icon'] ?? null;
            $banner->button_text = $data['button_text'] ?? null;
        } else {
            $banner->badge_text = null;
            $banner->badge_color = null;
            $banner->badge_background_color = null;
            $banner->badge_icon = null;
            $banner->button_text = null;
        }
        $banner->save();
        return $banner;
    }

    /**
     * Delete a banner and its image.
     */
    public function delete(int $bannerId): void
    {
        $banner = $this->findById($bannerId);
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
        $banner = $this->findById($bannerId);
        $banner->is_active = $status;
        $banner->save();
    }
    
    /**
     * Summary of findById
     * @param int $bannerId
     * @return Banner
     */
    public function findById(int $bannerId): Banner
    {
        return $this->banner->findOrFail($bannerId);
    }   
}
