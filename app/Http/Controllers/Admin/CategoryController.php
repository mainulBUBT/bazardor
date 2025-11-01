<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryStoreUpdateRequest;
use App\Services\CategoryService;
use Brian2694\Toastr\Facades\Toastr;

class CategoryController extends Controller
{

    public function __construct(protected CategoryService $categoryService)
    {
        
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = $this->categoryService->getCategories();
        $parents = $this->categoryService->getCategories()->where('parent_id', 0);

        return view("admin.categories.index", compact("categories", "parents"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parentCategories = $this->categoryService->getCategories()->where('parent_id', 0);
        return view("admin.categories.create", compact("parentCategories"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryStoreUpdateRequest $request)
    {
        $data = $request->validated();
        $this->categoryService->store($data);

        Toastr::success(translate("messages.category_created_successfully"));
        return redirect()->route('admin.categories.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = $this->categoryService->findById($id);
        $parentCategories = $this->categoryService->getCategories(parentId: $category->parent_id); 
        return view("admin.categories.edit", compact("parentCategories", "category"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryStoreUpdateRequest $request, string $id)
    {   
        $data = $request->validated();
        $this->categoryService->update($id, $data);

        Toastr::success(translate("messages.category_updated_successfully"));
        return redirect()->route('admin.categories.index');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->categoryService->delete($id);

        Toastr::success(translate("messages.category_deleted_successfully"));
        return redirect()->route('admin.categories.index');
    }
}
