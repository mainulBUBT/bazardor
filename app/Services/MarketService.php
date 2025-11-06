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

    public function getMarkets($with = [], $search = null, $limit = null, $offset = null)
    {
        return $this->market
        ->when(!empty($with), function ($query) use ($with) {
            $query->with($with);
        })
        ->when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%");
        })
        ->latest()->paginate($limit ?? pagination_limit(), ['*'], 'page', $offset ?? 1);
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

    /**
     * Get markets by zone with filters, distance calculation, and operating hours
     *
     * @param string $zoneId
     * @param float $userLat
     * @param float $userLng
     * @param string|null $search
     * @param bool|null $isOpen
     * @param string|null $type
     * @param string|null $information
     * @param int $limit
     * @param int $offset
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getMarketsByZoneWithFilters(
        string $zoneId,
        float $userLat = 0,
        float $userLng = 0,
        ?string $search = null,
        ?bool $isOpen = null,
        ?string $type = null,
        ?string $information = null,
        int $limit = 15,
        int $offset = 1
    ) {
        $today = strtolower(now()->format('l'));

        $query = $this->market
            ->where('zone_id', $zoneId)
            ->where('is_active', 1)
            ->where('visibility', 1)
            ->with([
                'marketInformation:id,market_id,is_non_veg,is_halal,is_parking,is_restroom,is_home_delivery',
                'openingHours' => function ($q) use ($today) {
                    $q->where('day', ucfirst($today));
                }
            ])
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->when($type, function ($q) use ($type) {
                $q->where('type', $type);
            })
            ->when(!is_null($isOpen), function ($q) use ($isOpen, $today) {
                if ($isOpen) {
                    $q->whereHas('openingHours', function ($subQ) use ($today) {
                        $subQ->where('day', ucfirst($today))
                            ->where('is_closed', 0)
                            ->whereRaw('TIME(?) BETWEEN opening AND closing', [now()->format('H:i:s')]);
                    });
                } else {
                    $q->whereDoesntHave('openingHours', function ($subQ) use ($today) {
                        $subQ->where('day', ucfirst($today))
                            ->where('is_closed', 0)
                            ->whereRaw('TIME(?) BETWEEN opening AND closing', [now()->format('H:i:s')]);
                    });
                }
            })
            ->when($information, function ($q) use ($information) {
                $q->whereHas('marketInformation', function ($subQ) use ($information) {
                    $field = 'is_' . $information;
                    $subQ->where($field, 1);
                });
            });

        // Add distance calculation if user coordinates provided
        if ($userLat !== 0 && $userLng !== 0) {
            $haversine = "(6371 * acos(cos(radians($userLat)) 
                         * cos(radians(latitude)) 
                         * cos(radians(longitude) - radians($userLng)) 
                         + sin(radians($userLat)) 
                         * sin(radians(latitude))))";
            
            $query->selectRaw("*, {$haversine} AS distance_km")
                  ->orderBy('distance_km');
        } else {
            $query->select('*');
        }

        return $query->paginate($limit, ['*'], 'page', $offset);
    }
}
