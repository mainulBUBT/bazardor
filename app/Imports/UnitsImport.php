<?php

namespace App\Imports;

use App\Models\Unit;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UnitsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $this->processRow($row);
        }
    }

    private function processRow($row)
    {
        // Skip empty rows
        if (!isset($row['name']) || empty($row['name'])) {
            return;
        }

        $id = $row['id'] ?? null;
        $unit = null;

        if ($id) {
            $unit = Unit::find($id);
        }

        $data = [
            'name' => $row['name'],
            'symbol' => $row['symbol'] ?? '',
            'unit_type' => $row['type'] ?? 'other',
            'is_active' => $this->parseStatus($row['status'] ?? 'Active'),
        ];

        if ($unit) {
            $unit->update($data);
        } else {
            Unit::create($data);
        }
    }

    private function parseStatus($value)
    {
        $value = strtolower((string)$value);
        return in_array($value, ['active', '1', 'true', 'yes']) ? 1 : 0;
    }
}
