<?php

namespace App\Exports;

use App\Models\Banner;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BannersExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Banner::with('zones')->latest()->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Link',
            'Status',
            'Featured',
            'Start Date',
            'End Date',
            'Zones',
        ];
    }

    /**
     * @param  Banner  $banner
     */
    public function map($banner): array
    {
        return [
            $banner->id,
            $banner->title,
            $banner->link ?? '-',
            $banner->is_active ? 'Active' : 'Inactive',
            $banner->is_featured ? 'Yes' : 'No',
            $banner->start_date ? \Carbon\Carbon::parse($banner->start_date)->format('Y-m-d') : '-',
            $banner->end_date ? \Carbon\Carbon::parse($banner->end_date)->format('Y-m-d') : '-',
            $banner->zones->pluck('name')->join(', ') ?: 'All Zones',
        ];
    }
}
