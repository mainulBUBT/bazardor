<?php

namespace App\Exports;

use App\Services\CategoryService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CategoriesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $categoryService;

    public function __construct()
    {
        $this->categoryService = app(CategoryService::class);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Get all categories without pagination for export
        return $this->categoryService->getAllCategoriesForExport();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Slug',
            'Parent Category',
            'Status',
            'Position',
            'Created At',
        ];
    }

    /**
     * @param mixed $category
     * @return array
     */
    public function map($category): array
    {
        return [
            $category->id,
            $category->name,
            $category->slug,
            $category->parent ? $category->parent->name : 'None',
            $category->is_active ? 'Active' : 'Inactive',
            $category->position,
            $category->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
