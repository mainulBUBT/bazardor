<?php

namespace App\Services;

use App\Models\Market;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketService
{
    /**
     * Store a new market.
     *
     * @param array $validatedData
     * @param Request $request
     * @return Market
     * @throws \Exception
     */
    public function store(array $validatedData, Request $request): Market
    {
        DB::beginTransaction();
        try {
            $market = new Market();
            $market->name = $validatedData['name'];
            $market->slug = $validatedData['slug'] ?? null; // The model will auto-generate if empty
            $market->type = $validatedData['type'];
            $market->description = $validatedData['description'] ?? null;
            $market->address = $validatedData['address'];
            $market->latitude = $validatedData['latitude'] ?? null;
            $market->longitude = $validatedData['longitude'] ?? null;
            $market->is_active = $validatedData['status'] === 'active';
            $market->featured = $validatedData['featured'] ?? false;

            // Combine location fields into a single JSON object
            $market->location = json_encode([
                'division' => $validatedData['division'],
                'district' => $validatedData['district'],
                'upazila' => $validatedData['upazila'] ?? null,
            ]);

            // Process operating hours
            $hours_data = [];
            foreach ($validatedData['opening_hours'] as $day => $hours) {
                $is_closed = isset($hours['is_closed']);
                $hours_data[$day] = [
                    'opening_time' => $is_closed ? null : ($hours['opening_time'] ?? null),
                    'closing_time' => $is_closed ? null : ($hours['closing_time'] ?? null),
                    'is_closed' => $is_closed,
                ];
            }
            $market->opening_hours = json_encode($hours_data);

            if ($request->hasFile('image')) {
                $market->image_path = handle_file_upload($request->file('image'), 'markets');
            }

            $market->save();

            DB::commit();

            return $market;
        } catch (\Exception $e) {
            DB::rollBack();
            // Re-throw the exception to be caught in the controller
            throw $e;
        }
    }

    /**
     * Update an existing market.
     *
     * @param array $validatedData
     * @param Request $request
     * @param string $id
     * @return Market
     * @throws \Exception
     */
    public function update(array $validatedData, Request $request, string $id): Market
    {
        DB::beginTransaction();
        try {
            $market = Market::findOrFail($id);

            $market->name = $validatedData['name'];
            $market->slug = $validatedData['slug'] ?? null;
            $market->type = $validatedData['type'];
            $market->description = $validatedData['description'] ?? null;
            $market->address = $validatedData['address'];
            $market->latitude = $validatedData['latitude'] ?? null;
            $market->longitude = $validatedData['longitude'] ?? null;
            $market->is_active = $validatedData['status'] === 'active';
            $market->featured = $validatedData['featured'] ?? false;

            $market->location = json_encode([
                'division' => $validatedData['division'],
                'district' => $validatedData['district'],
                'upazila' => $validatedData['upazila'] ?? null,
            ]);

            $hours_data = [];
            foreach ($validatedData['opening_hours'] as $day => $hours) {
                $is_closed = isset($hours['is_closed']);
                $hours_data[$day] = [
                    'opening_time' => $is_closed ? null : ($hours['opening_time'] ?? null),
                    'closing_time' => $is_closed ? null : ($hours['closing_time'] ?? null),
                    'is_closed' => $is_closed,
                ];
            }
            $market->opening_hours = json_encode($hours_data);

            if ($request->hasFile('image')) {
                $market->image_path = handle_file_upload($request->file('image'), 'markets', $market->image_path);
            }

            $market->save();

            DB::commit();

            return $market;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
