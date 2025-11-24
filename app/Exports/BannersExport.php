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
        return Banner::with('zone')->latest()->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Type',
            'URL',
            'Status',
            'Position',
            'Start Date',
            'End Date',
            'Zone',
            'Description',
        ];
    }

    /**
     * @param Banner $banner
     * @return array
     */
    public function map($banner): array
    {
        return [
            $banner->id,
            $banner->title,
            $banner->type === 'featured' ? 'Featured' : 'General',
            $banner->url ?? '-',
            $banner->is_active ? 'Active' : 'Inactive',
            $banner->position,
            $banner->start_date ? \Carbon\Carbon::parse($banner->start_date)->format('Y-m-d') : '-',
            $banner->end_date ? \Carbon\Carbon::parse($banner->end_date)->format('Y-m-d') : '-',
            $banner->zone?->name ?? '-',
            $banner->description ?? '-',
        ];
    }
}
