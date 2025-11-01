<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStoreUpdateRequest;
use App\Services\ProductService;
use App\Services\CategoryService;
use App\Services\UnitService;
use App\Services\MarketService;
use Brian2694\Toastr\Facades\Toastr;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService,
        protected CategoryService $categoryService,
        protected UnitService $unitService,
        protected MarketService $marketService
    )
    {
    }

    public function index()
    {
        $products = $this->productService->getProducts();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = $this->categoryService->getCategories(null, null)->getCollection();
        $units = $this->unitService->getUnits()->getCollection();
        $markets = $this->marketService->getMarkets()->getCollection();

        return view('admin.products.create', compact('categories', 'units'));
    }

    public function store(ProductStoreUpdateRequest $request)
    {
        $data = $request->validated();
        // tags may come as comma separated string or array
        if ($request->has('tags')) {
            $tags = $request->input('tags');
            if (is_string($tags)) {
                $tags = array_filter(array_map('trim', explode(',', $tags)));
            }
            $data['tags'] = $tags;
        }

        $this->productService->store($data);
        Toastr::success(translate('messages.product_created_successfully'));
        return redirect()->route('admin.products.index');
    }

    public function show(string $id)
    {
        $product = $this->productService->findById($id, ['category', 'unit', 'tags']);
        return view('admin.products.show', compact('product'));
    }

    public function edit(string $id)
    {
        $product = $this->productService->findById($id, ['tags']);
        $categories = $this->categoryService->getCategories(null, null)->getCollection();
        $units = $this->unitService->getUnits()->getCollection();
        return view('admin.products.edit', compact('product', 'categories', 'units'));
    }

    public function update(ProductStoreUpdateRequest $request, string $id)
    {
        $data = $request->validated();
        if ($request->has('tags')) {
            $tags = $request->input('tags');
            if (is_string($tags)) {
                $tags = array_filter(array_map('trim', explode(',', $tags)));
            }
            $data['tags'] = $tags;
        }
        $this->productService->update($data, $id);
        Toastr::success(translate('messages.product_updated_successfully'));
        return redirect()->back();
    }

    public function destroy(string $id)
    {
        $this->productService->delete($id);
        Toastr::success(translate('messages.product_deleted_successfully'));
        return redirect()->route('admin.products.index');
    }
} 