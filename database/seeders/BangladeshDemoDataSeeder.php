<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;
use App\Models\Zone;
use App\Models\Category;
use App\Models\Product;
use App\Models\Market;
use App\Models\Banner;
use Illuminate\Support\Str;
use MatanYadaev\EloquentSpatial\Objects\Polygon;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Objects\Point;

class BangladeshDemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Units
        $units = [
            ['name' => 'Kilogram', 'symbol' => 'kg', 'unit_type' => 'weight', 'is_active' => true],
            ['name' => 'Gram', 'symbol' => 'gm', 'unit_type' => 'weight', 'is_active' => true],
            ['name' => 'Liter', 'symbol' => 'L', 'unit_type' => 'volume', 'is_active' => true],
            ['name' => 'Milliliter', 'symbol' => 'ml', 'unit_type' => 'volume', 'is_active' => true],
            ['name' => 'Piece', 'symbol' => 'pc', 'unit_type' => 'quantity', 'is_active' => true],
            ['name' => 'Dozen', 'symbol' => 'doz', 'unit_type' => 'quantity', 'is_active' => true],
            ['name' => 'Hali', 'symbol' => 'hali', 'unit_type' => 'quantity', 'is_active' => true], // 4 pcs
        ];

        foreach ($units as $unitData) {
            Unit::firstOrCreate(['symbol' => $unitData['symbol']], $unitData);
        }
        $unitKg = Unit::where('symbol', 'kg')->first();
        $unitL = Unit::where('symbol', 'L')->first();
        $unitPc = Unit::where('symbol', 'pc')->first();
        $unitDoz = Unit::where('symbol', 'doz')->first();
        $unitHali = Unit::where('symbol', 'hali')->first();

        // 2. Create Zones (Dhaka)
        // Approximate polygon for Dhaka
        $dhakaPolygon = new Polygon([
            new LineString([
                new Point(23.8859, 90.3800),
                new Point(23.8859, 90.4500),
                new Point(23.7000, 90.4500),
                new Point(23.7000, 90.3800),
                new Point(23.8859, 90.3800),
            ])
        ]);

        $zone = Zone::firstOrCreate(
            ['name' => 'Dhaka City'],
            [
                'is_active' => true,
                'coordinates' => $dhakaPolygon
            ]
        );

        // 3. Create Categories
        $categories = [
            ['name' => 'Vegetables', 'slug' => 'vegetables', 'description' => 'Fresh vegetables directly from farmers', 'image_path' => 'demo-veg.png'],
            ['name' => 'Fruits', 'slug' => 'fruits', 'description' => 'Seasonal and imported fruits', 'image_path' => 'demo-fruit.png'],
            ['name' => 'Fish', 'slug' => 'fish', 'description' => 'Freshwater and sea fish', 'image_path' => 'demo-fish.png'],
            ['name' => 'Meat', 'slug' => 'meat', 'description' => 'Beef, Chicken, Mutton and others', 'image_path' => 'demo-meat.png'],
            ['name' => 'Grocery', 'slug' => 'grocery', 'description' => 'Daily essentials', 'image_path' => 'demo-veg.png'],
            ['name' => 'Spices', 'slug' => 'spices', 'description' => 'Authentic spices', 'image_path' => 'demo-veg.png'],
        ];

        foreach ($categories as $catData) {
            Category::updateOrCreate(['slug' => $catData['slug']], array_merge($catData, ['is_active' => true, 'position' => 0]));
        }

        $catVeg = Category::where('slug', 'vegetables')->first();
        $catFruit = Category::where('slug', 'fruits')->first();
        $catFish = Category::where('slug', 'fish')->first();
        $catMeat = Category::where('slug', 'meat')->first();
        $catGrocery = Category::where('slug', 'grocery')->first();
        $catSpices = Category::where('slug', 'spices')->first();

        // 4. Create Markets
        $markets = [
            [
                'name' => 'Karwan Bazar',
                'slug' => 'karwan-bazar',
                'description' => 'One of the largest wholesale marketplaces in Dhaka city.',
                'address' => 'Karwan Bazar, Dhaka 1215',
                'latitude' => 23.7516,
                'longitude' => 90.3936,
                'division' => 'Dhaka',
                'district' => 'Dhaka',
                'upazila_or_thana' => 'Tejgaon',
                'zone_id' => $zone->id,
                'image_path' => 'demo-veg.png',
            ],
            [
                'name' => 'Hatirpool Kacha Bazar',
                'slug' => 'hatirpool-kacha-bazar',
                'description' => 'Popular market for fresh produce and fish.',
                'address' => 'Hatirpool, Dhaka 1205',
                'latitude' => 23.7390,
                'longitude' => 90.3900,
                'division' => 'Dhaka',
                'district' => 'Dhaka',
                'upazila_or_thana' => 'Dhanmondi',
                'zone_id' => $zone->id,
                'image_path' => 'demo-eid.png',
            ],
            [
                'name' => 'Mohammadpur Krishi Market',
                'slug' => 'mohammadpur-krishi-market',
                'description' => 'Government regulated agricultural market.',
                'address' => 'Mohammadpur, Dhaka 1207',
                'latitude' => 23.7665,
                'longitude' => 90.3587,
                'division' => 'Dhaka',
                'district' => 'Dhaka',
                'upazila_or_thana' => 'Mohammadpur',
                'zone_id' => $zone->id,
                'image_path' => 'demo-winter.png',
            ],
            [
                'name' => 'Shantinagar Bazar',
                'slug' => 'shantinagar-bazar',
                'description' => 'Well organized kitchen market.',
                'address' => 'Shantinagar, Dhaka 1217',
                'latitude' => 23.7368,
                'longitude' => 90.4132,
                'division' => 'Dhaka',
                'district' => 'Dhaka',
                'upazila_or_thana' => 'Paltan',
                'zone_id' => $zone->id,
                'image_path' => 'demo-veg.png',
            ],
            [
                'name' => 'New Market',
                'slug' => 'new-market',
                'description' => 'Historic market complex.',
                'address' => 'New Market, Dhaka 1205',
                'latitude' => 23.7328,
                'longitude' => 90.3852,
                'division' => 'Dhaka',
                'district' => 'Dhaka',
                'upazila_or_thana' => 'New Market',
                'zone_id' => $zone->id,
                'image_path' => 'demo-eid.png',
            ],
        ];

        foreach ($markets as $marketData) {
            Market::updateOrCreate(
                ['slug' => $marketData['slug']],
                array_merge($marketData, ['is_active' => true, 'is_featured' => true, 'visibility' => true])
            );
        }

        // 5. Create Products (20 items)
        $products = [
            // Rice & Grocery
            [
                'name' => 'Miniket Rice',
                'category_id' => $catGrocery->id,
                'unit_id' => $unitKg->id,
                'base_price' => 75.00,
                'description' => 'Premium quality Miniket rice.',
                'sku' => 'RICE-MIN-001',
                'image_path' => 'categories/demo-veg.png',
            ],
            [
                'name' => 'Nazirshail Rice',
                'category_id' => $catGrocery->id,
                'unit_id' => $unitKg->id,
                'base_price' => 85.00,
                'description' => 'Fine grain Nazirshail rice.',
                'sku' => 'RICE-NAZ-002',
                'image_path' => 'categories/demo-veg.png',
            ],
            [
                'name' => 'Masoor Dal (Deshi)',
                'category_id' => $catGrocery->id,
                'unit_id' => $unitKg->id,
                'base_price' => 140.00,
                'description' => 'Local variety Masoor lentils.',
                'sku' => 'DAL-MAS-001',
                'image_path' => 'categories/demo-veg.png',
            ],
            [
                'name' => 'Soybean Oil (Rupchanda)',
                'category_id' => $catGrocery->id,
                'unit_id' => $unitL->id,
                'base_price' => 170.00,
                'description' => 'Fortified Soybean Oil.',
                'sku' => 'OIL-SOY-001',
                'image_path' => 'categories/demo-veg.png',
            ],
            [
                'name' => 'Mustard Oil (Radhuni)',
                'category_id' => $catGrocery->id,
                'unit_id' => $unitL->id,
                'base_price' => 280.00,
                'description' => 'Pure Mustard Oil.',
                'sku' => 'OIL-MUS-001',
                'image_path' => 'categories/demo-veg.png',
            ],
            // Vegetables
            [
                'name' => 'Potato (Diamond)',
                'category_id' => $catVeg->id,
                'unit_id' => $unitKg->id,
                'base_price' => 45.00,
                'description' => 'Fresh Diamond potatoes.',
                'sku' => 'VEG-POT-001',
                'image_path' => 'categories/demo-veg.png',
            ],
            [
                'name' => 'Onion (Deshi)',
                'category_id' => $catVeg->id,
                'unit_id' => $unitKg->id,
                'base_price' => 90.00,
                'description' => 'Local onions.',
                'sku' => 'VEG-ONI-001',
                'image_path' => 'categories/demo-veg.png',
            ],
            [
                'name' => 'Tomato (Ripe)',
                'category_id' => $catVeg->id,
                'unit_id' => $unitKg->id,
                'base_price' => 60.00,
                'description' => 'Fresh ripe tomatoes.',
                'sku' => 'VEG-TOM-001',
                'image_path' => 'categories/demo-veg.png',
            ],
            [
                'name' => 'Green Chili',
                'category_id' => $catVeg->id,
                'unit_id' => $unitKg->id,
                'base_price' => 120.00,
                'description' => 'Spicy green chilies.',
                'sku' => 'VEG-CHI-001',
                'image_path' => 'categories/demo-veg.png',
            ],
            [
                'name' => 'Brinjal (Long)',
                'category_id' => $catVeg->id,
                'unit_id' => $unitKg->id,
                'base_price' => 50.00,
                'description' => 'Fresh long brinjals.',
                'sku' => 'VEG-BRI-001',
                'image_path' => 'demo-veg.png',
            ],
            // Fish
            [
                'name' => 'Hilsha Fish (1kg+)',
                'category_id' => $catFish->id,
                'unit_id' => $unitPc->id,
                'base_price' => 1500.00,
                'description' => 'Padma river Hilsha.',
                'sku' => 'FISH-HIL-001',
                'image_path' => 'demo-fish.png',
            ],
            [
                'name' => 'Rui Fish',
                'category_id' => $catFish->id,
                'unit_id' => $unitKg->id,
                'base_price' => 350.00,
                'description' => 'Fresh Rui fish.',
                'sku' => 'FISH-RUI-001',
                'image_path' => 'demo-fish.png',
            ],
            [
                'name' => 'Tilapia',
                'category_id' => $catFish->id,
                'unit_id' => $unitKg->id,
                'base_price' => 180.00,
                'description' => 'Farm fresh Tilapia.',
                'sku' => 'FISH-TIL-001',
                'image_path' => 'demo-fish.png',
            ],
            // Meat
            [
                'name' => 'Beef (Bone-in)',
                'category_id' => $catMeat->id,
                'unit_id' => $unitKg->id,
                'base_price' => 750.00,
                'description' => 'Fresh beef with bone.',
                'sku' => 'MEAT-BEE-001',
                'image_path' => 'demo-meat.png',
            ],
            [
                'name' => 'Broiler Chicken',
                'category_id' => $catMeat->id,
                'unit_id' => $unitKg->id,
                'base_price' => 200.00,
                'description' => 'Farm fresh broiler chicken.',
                'sku' => 'MEAT-CHI-001',
                'image_path' => 'demo-meat.png',
            ],
            [
                'name' => 'Mutton',
                'category_id' => $catMeat->id,
                'unit_id' => $unitKg->id,
                'base_price' => 1100.00,
                'description' => 'Fresh mutton.',
                'sku' => 'MEAT-MUT-001',
                'image_path' => 'demo-meat.png',
            ],
            // Fruits
            [
                'name' => 'Mango (Himsagar)',
                'category_id' => $catFruit->id,
                'unit_id' => $unitKg->id,
                'base_price' => 120.00,
                'description' => 'Sweet Himsagar mangoes.',
                'sku' => 'FRU-MAN-001',
                'image_path' => 'demo-fruit.png',
            ],
            [
                'name' => 'Banana (Sagor)',
                'category_id' => $catFruit->id,
                'unit_id' => $unitDoz->id,
                'base_price' => 100.00,
                'description' => 'Ripe Sagor bananas.',
                'sku' => 'FRU-BAN-001',
                'image_path' => 'demo-fruit.png',
            ],
            // Spices
            [
                'name' => 'Turmeric Powder',
                'category_id' => $catSpices->id,
                'unit_id' => $unitKg->id,
                'base_price' => 300.00,
                'description' => 'Pure turmeric powder.',
                'sku' => 'SPI-TUR-001',
                'image_path' => 'demo-veg.png',
            ],
            [
                'name' => 'Chili Powder',
                'category_id' => $catSpices->id,
                'unit_id' => $unitKg->id,
                'base_price' => 400.00,
                'description' => 'Spicy chili powder.',
                'sku' => 'SPI-CHI-001',
                'image_path' => 'demo-veg.png',
            ],
        ];

        foreach ($products as $prodData) {
            Product::updateOrCreate(
                ['sku' => $prodData['sku']],
                array_merge($prodData, [
                    'status' => 'active',
                    'is_visible' => true,
                    'is_featured' => rand(0, 1),
                    'country_of_origin' => 'Bangladesh',
                    'brand' => 'Local',
                ])
            );
        }

        // 6. Create Banners
        $banners = [
            [
                'title' => 'Fresh Vegetables',
                'type' => 'featured',
                'is_active' => true,
                'zone_id' => $zone->id,
                'image_path' => 'banners/demo-veg.png',
            ],
            [
                'title' => 'Eid Special Discount',
                'type' => 'general',
                'is_active' => true,
                'zone_id' => $zone->id,
                'image_path' => 'banners/demo-eid.png',
            ],
            [
                'title' => 'Winter Collection',
                'type' => 'featured',
                'is_active' => true,
                'zone_id' => $zone->id,
                'image_path' => 'banners/demo-winter.png',
            ],
        ];

        foreach ($banners as $bannerData) {
            Banner::updateOrCreate(
                ['title' => $bannerData['title']],
                $bannerData
            );
        }
    }
}
