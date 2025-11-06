<?php

namespace App\Services;

use App\Models\Market;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Str;

class MarketService
{
    public function __construct(private Market $market) {
    }

    public function getMarkets($with = [], $search = null)
    {
        return $this->market
        ->when(!empty($with), function ($query) use ($with) {
            $query->with($with);
        })
        ->when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%");
        })
        ->latest()->paginate(pagination_limit());
    }
    
    /**
     * Store a new market.
     *
     * @param array $data
     * @return Market
     * @throws \Exception
     */
    public function store(array $data): Market
    {
        DB::beginTransaction();
        try {
            $market = new Market();
            $market->name = $data['name'];
            $market->slug = $data['slug'] ?? null;
            $market->type = $data['type'];
            $market->description = $data['description'] ?? null;
            $market->address = $data['address'];
            $market->latitude = $data['latitude'] ?? null;
            $market->longitude = $data['longitude'] ?? null;
            $market->is_active = $data['status'] === 'active';
            $market->is_featured = isset($data['featured']) ? $data['featured'] : 0;
            $market->division = $data['division'] ?? null;      
            $market->district = $data['district'] ?? null;
            $market->upazila_or_thana = $data['upazila'] ?? null;
            $market->visibility = $data['visibility'] === 'public' ? 1 : 0;
            $market->zone_id = $data['zone_id'] ?? null;

            $market->save();

            // Record creator information
            // if (auth()->check()) {
            //     \App\Models\EntityCreator::create([
            //         'user_id' => auth()->id(),
            //         'creatable_id' => $market->id,
            //         'creatable_type' => \App\Models\Market::class,
            //     ]);
            // }

            // Handle market image
            if (isset($data['image']) && $data['image']->isValid()) {
                $market->image_path = handle_file_upload('markets/', $data['image']->getClientOriginalExtension(), $data['image']);
                $market->save();
            }

            // Handle market information
            $market->marketInformation()->updateOrCreate(
                ['market_id' => $market->id],
                [
                    'is_non_veg' => $data['is_non_veg'] ?? 0,
                    'is_halal' => $data['is_halal'] ?? 0,
                    'is_parking' => $data['is_parking'] ?? 0,
                    'is_restroom' => $data['is_restroom'] ?? 0,
                    'is_home_delivery' => $data['is_home_delivery'] ?? 0
                ]
            );

            // Handle operating hours for all days
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            foreach ($days as $day) {
                $hours = $data['opening_hours'][$day] ?? ['is_closed' => true];
                $is_closed = isset($hours['is_closed']) ? (bool)$hours['is_closed'] : true;

                $market->openingHours()->create([
                    'day' => $day,
                    'opening' => $is_closed ? null : ($hours['opening_time'] ?? null),
                    'closing' => $is_closed ? null : ($hours['closing_time'] ?? null),
                    'is_closed' => $is_closed,
                ]);
            }

            DB::commit();
            return $market;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Summary of update
     * @param array $data
     * @param string $id
     * @return Market|\Illuminate\Database\Eloquent\Collection<int, Market>
     */
    public function update(array $data, string $id)
    {
        DB::beginTransaction();
        try {
            $market = $this->findById($id);

            $market->name = $data['name'];
            $market->slug = $data['slug'] ?? null;
            $market->type = $data['type'];
            $market->description = $data['description'] ?? null;
            $market->address = $data['address'];
            $market->latitude = $data['latitude'] ?? null;
            $market->longitude = $data['longitude'] ?? null;
            $market->is_active = $data['status'] === 'active';
            $market->is_featured = isset($data['featured']) ? $data['featured'] : 0;
            $market->division = $data['division'] ?? null;
            $market->district = $data['district'] ?? null;
            $market->upazila_or_thana = $data['upazila'] ?? null;
            $market->visibility = $data['visibility'] === 'public' ? 1 : 0;
            $market->zone_id = $data['zone_id'] ?? null;

            $market->save();

            // Handle market image update if present
            if (isset($data['image']) && $data['image']->isValid()) {
                $market->image_path = handle_file_upload('markets/', $data['image']->getClientOriginalExtension(), $data['image'], $market->image_path);
                $market->save();
            }

            // Handle market information
            $market->marketInformation()->updateOrCreate(
                ['market_id' => $market->id],
                [
                    'is_non_veg' => $data['is_non_veg'] ?? 0,
                    'is_halal' => $data['is_halal'] ?? 0,
                    'is_parking' => $data['is_parking'] ?? 0,
                    'is_restroom' => $data['is_restroom'] ?? 0,
                    'is_home_delivery' => $data['is_home_delivery'] ?? 0
                ]
            );

            // Update operating hours for all days
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            foreach ($days as $day) {
                $hours = $data['opening_hours'][$day] ?? ['is_closed' => true];
                $is_closed = isset($hours['is_closed']) ? (bool)$hours['is_closed'] : true;

                $market->openingHours()->updateOrCreate(
                    ['day' => $day],
                    [
                        'opening' => $is_closed ? null : ($hours['opening_time'] ?? null),
                        'closing' => $is_closed ? null : ($hours['closing_time'] ?? null),
                        'is_closed' => $is_closed,
                    ]
                );
            }

            DB::commit();
            return $market;
        } catch (\Exception $e) {
            info(['market_update_error' => $e->getMessage()]); // Log the error message for debugging
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Summary of findById
     * @param mixed $id
     * @return Market|\Illuminate\Database\Eloquent\Collection<int, Market>
     */
    public function findById($id, array $with = [])
    {
        $query = $this->market->when(!empty($with), function ($query) use ($with) {
            $query->with($with);
        })->findOrFail($id);

        return $query; 
    }   
}
