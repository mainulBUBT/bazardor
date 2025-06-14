<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    public function __construct(private Category $category)
    {
    }

    /**
     * Summary of getCategories
     * @param string|null $search
     * @param int|null $parentId
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getCategories($search = null, $parentId = null)
    {
        return $this->category->when($search, function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        })
        ->when($parentId, function ($query) use ($parentId) {
            $query->where('parent_id', $parentId);
        })
        ->latest()->paginate(pagination_limit());
    }

    /**
     * Summary of store
     * @param array $data
     * @return \App\Models\Category
     */
    public function store(array $data): Category
    {
        if (isset($data['image']) && $data['image']->isValid()) {
            $imageName = handle_file_upload('categories/', $data['image']->getClientOriginalExtension(), $data['image']);
            $data['image_path'] = $imageName;
        }
        unset($data['image']);

        $this->category->name = $data['name'];
        $this->category->slug = $data['slug'];
        $this->category->parent_id = $data['parent_id'] ?? 0;
        $this->category->position = $data['position'] ?? 0;
        $this->category->image_path = $data['image_path'];
        $this->category->is_active = $data['is_active'] ?? 1;
        $this->category->save();
        return $this->category;
    }

    /**
     * Summary of update
     * @param int $categoryId
     * @param array $data
     * @return \App\Models\Category
     */
    public function update(int $categoryId, array $data): Category
    {
        $category = $this->findById($categoryId);
        $oldImagePath = $category->image_path;

        if (isset($data['image']) && $data['image']->isValid()) {
            $imageName = handle_file_upload('categories/', $data['image']->getClientOriginalExtension(), $data['image'],  $oldImagePath);
            $data['image_path'] = $imageName;

            if ($oldImagePath) {
                // Extract filename from old path for deletion
                $oldFilename = basename($oldImagePath);
                handle_file_upload('categories/', '', null, $oldFilename);
            }
        }
        unset($data['image']);
        $category->name = $data['name'] ?? $category->name;
        $category->slug = $data['slug'] ?? $category->slug;
        $category->parent_id = $data['parent_id'] ?? $category->parent_id;
        $category->position = $data['position'] ?? $category->position;
        $category->image_path = $data['image_path'] ?? $category->image_path;
        $category->is_active = $data['is_active'] ?? $category->is_active;
        $category->save();
       
        return $category;
    }

    /**
     * Summary of delete
     * @param int $categoryId
     * @return void
     */
    public function delete(int $categoryId): void
    {
        $category = $this->findById($categoryId);
        if ($category->image_path) {
            // Extract filename from full path for deletion
            $filename = basename($category->image_path);
            handle_file_upload('categories/', '', null, $filename);
        }
        $category->delete();
    }

    /**
     * Summary of status
     * @param int $categoryId
     * @param mixed $status
     * @return void
     */
    public function status(int $categoryId, $status): void
    {
        $category = $this->findById($categoryId);
        $category->is_active = $status;
        $category->save();
    }

    /**
     * Summary of findById
     * @param int $categoryId
     * @return Category
     */
    public function findById(int $categoryId): Category
    {
        return $this->category->findOrFail($categoryId);
    }
}
