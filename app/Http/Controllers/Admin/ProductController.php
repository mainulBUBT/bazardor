<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStoreUpdateRequest;
use App\Services\ProductService;
use App\Services\CategoryService;
use App\Services\UnitService;
use App\Services\MarketService;
use App\Exports\ProductsExport;
use App\Imports\ProductsImport;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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

    public function index(Request $request)
    {
        $filters = [
            'category_id' => $request->query('category_id'),
            'status' => $request->query('status'),
            'sort' => $request->query('sort', 'latest'),
            'search' => $request->query('search'),
        ];
        
        $products = $this->productService->getProducts($filters['search'], ['category', 'unit'], null, null, $filters);
        $categories = $this->categoryService->getCategories(filters: ['is_active' => 1])->getCollection();
        
        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = $this->categoryService->getCategories(filters: ['is_active' => 1])->getCollection();
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
        toastr()->success(translate('messages.product_created_successfully'));
        return redirect()->route('admin.products.index');
    }

    public function show(string $id)
    {
        $product = $this->productService->findById($id, ['category', 'unit', 'tags']);
        return view('admin.products.show', compact('product'));
    }

    public function edit($id)
    {
        $product = $this->productService->findById($id, ['tags', 'category', 'unit']);
        $categories = $this->categoryService->getCategories(filters: ['is_active' => 1])->getCollection();
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
        toastr()->success(translate('messages.product_updated_successfully'));
        return redirect()->back();
    }

    public function destroy(string $id)
    {
        $this->productService->delete($id);
        toastr()->success(translate('messages.product_deleted_successfully'));
        return redirect()->route('admin.products.index');
    }

    /**
     * Display the bulk import/export page.
     */
    public function bulkImport()
    {
        return view('admin.products.bulk-import');
    }

    /**
     * Export products to Excel.
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

        return Excel::download(new ProductsExport, 'products_' . date('Y-m-d_H-i-s') . '.' . $extension, $writerType);
    }

    /**
     * Import products from Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        try {
            Excel::import(new ProductsImport, $request->file('file'));
            
            toastr()->success(translate('messages.products_imported_successfully'));
            return redirect()->back();
        } catch (\Exception $e) {
            toastr()->error(translate('messages.import_failed') . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }
} 