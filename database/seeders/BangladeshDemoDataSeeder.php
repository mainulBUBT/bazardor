<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Market;
use App\Models\Product;
use App\Models\ProductMarketPrice;
use App\Models\Setting;
use App\Models\Unit;
use App\Models\Zone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;

class BangladeshDemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Units with bilingual translations
        $unitsBilingual = [
            ['en' => ['name' => 'Kilogram', 'symbol' => 'kg'], 'bn' => ['name' => 'কিলোগ্রাম', 'symbol' => 'কেজি'], 'unit_type' => 'weight'],
            ['en' => ['name' => 'Gram', 'symbol' => 'gm'], 'bn' => ['name' => 'গ্রাম', 'symbol' => 'গ্রা'], 'unit_type' => 'weight'],
            ['en' => ['name' => 'Liter', 'symbol' => 'L'], 'bn' => ['name' => 'লিটার', 'symbol' => 'লি'], 'unit_type' => 'volume'],
            ['en' => ['name' => 'Milliliter', 'symbol' => 'ml'], 'bn' => ['name' => 'মিলিলিটার', 'symbol' => 'মিলি'], 'unit_type' => 'volume'],
            ['en' => ['name' => 'Piece', 'symbol' => 'pc'], 'bn' => ['name' => 'পিস', 'symbol' => 'টি'], 'unit_type' => 'quantity'],
            ['en' => ['name' => 'Dozen', 'symbol' => 'doz'], 'bn' => ['name' => 'ডজন', 'symbol' => 'ডজন'], 'unit_type' => 'quantity'],
            ['en' => ['name' => 'Hali', 'symbol' => 'hali'], 'bn' => ['name' => 'হালি', 'symbol' => 'হালি'], 'unit_type' => 'quantity'],
        ];

        foreach ($unitsBilingual as $item) {
            $unit = Unit::factory()->create(array_merge([
                'is_active' => true,
            ], $item['en']));
            $t = $unit->translateOrNew('bn');
            foreach ($item['bn'] as $field => $value) {
                $t->setAttribute($field, $value);
            }
            $t->save();
        }

        // Retrieve for later use
        $unitKg = Unit::where('symbol', 'kg')->first();
        $unitL = Unit::where('symbol', 'L')->first();
        $unitPc = Unit::where('symbol', 'pc')->first();
        $unitDoz = Unit::where('symbol', 'doz')->first();

        // 2. Create Zones (Dhaka) with bilingual translations
        $dhakaPolygon = new Polygon([
            new LineString([
                new Point(23.8859, 90.3800),
                new Point(23.8859, 90.4500),
                new Point(23.7000, 90.4500),
                new Point(23.7000, 90.3800),
                new Point(23.8859, 90.3800),
            ]),
        ]);

        $zone = Zone::factory()->create([
            'name' => 'Dhaka City',
            'is_active' => true,
            'coordinates' => $dhakaPolygon,
        ]);
        $zt = $zone->translateOrNew('bn');
        $zt->name = 'ঢাকা সিটি';
        $zt->save();

        // 3. Create Categories with bilingual translations
        $categoriesData = [
            ['en' => ['name' => 'Vegetables', 'description' => 'Fresh vegetables directly from farmers'], 'bn' => ['name' => 'সবজি', 'description' => 'কৃষকদের কাছ থেকে সরাসরি তাজা সবজি'], 'slug' => 'vegetables', 'image_path' => null],
            ['en' => ['name' => 'Fruits', 'description' => 'Seasonal and imported fruits'], 'bn' => ['name' => 'ফলমূল', 'description' => 'মৌসুমী এবং আমদানিকৃত ফল'], 'slug' => 'fruits', 'image_path' => null],
            ['en' => ['name' => 'Fish', 'description' => 'Freshwater and sea fish'], 'bn' => ['name' => 'মাছ', 'description' => 'মিঠা পানি ও সামুদ্রিক মাছ'], 'slug' => 'fish', 'image_path' => null],
            ['en' => ['name' => 'Meat', 'description' => 'Beef, Chicken, Mutton and others'], 'bn' => ['name' => 'মাংস', 'description' => 'গরু, মুরগি, খাসি ও অন্যান্য মাংস'], 'slug' => 'meat', 'image_path' => null],
            ['en' => ['name' => 'Grocery', 'description' => 'Daily essentials'], 'bn' => ['name' => 'মুদি দ্রব্য', 'description' => 'দৈনন্দিন প্রয়োজনীয় সামগ্রী'], 'slug' => 'grocery', 'image_path' => null],
            ['en' => ['name' => 'Spices', 'description' => 'Authentic spices'], 'bn' => ['name' => 'মশলা', 'description' => 'অরিজিনাল মশলা ও গুঁড়া'], 'slug' => 'spices', 'image_path' => null],
        ];

        foreach ($categoriesData as $catData) {
            $en = $catData['en'];
            $bn = $catData['bn'];
            $cat = Category::factory()->create([
                'name' => $en['name'],
                'description' => $en['description'],
                'slug' => $catData['slug'],
                'image_path' => $catData['image_path'],
                'is_active' => true,
                'position' => 0,
            ]);
            $ct = $cat->translateOrNew('bn');
            foreach ($bn as $field => $value) {
                $ct->setAttribute($field, $value);
            }
            $ct->save();
        }

        $catVeg = Category::where('slug', 'vegetables')->first();
        $catFruit = Category::where('slug', 'fruits')->first();
        $catFish = Category::where('slug', 'fish')->first();
        $catMeat = Category::where('slug', 'meat')->first();
        $catGrocery = Category::where('slug', 'grocery')->first();
        $catSpices = Category::where('slug', 'spices')->first();

        // 4. Create Markets with bilingual translations
        $marketsData = [
            [
                'en' => ['name' => 'Karwan Bazar', 'description' => 'One of the largest wholesale marketplaces in Dhaka city.', 'address' => 'Karwan Bazar, Dhaka 1215'],
                'bn' => ['name' => 'কারওয়ান বাজার', 'description' => 'ঢাকা শহরের অন্যতম বৃহত্তম পাইকারি বাজার।', 'address' => 'কারওয়ান বাজার, ঢাকা ১২১৫'],
                'slug' => 'karwan-bazar', 'latitude' => 23.7516, 'longitude' => 90.3936,
                'division' => 'Dhaka', 'district' => 'Dhaka', 'upazila_or_thana' => 'Tejgaon',
                'image_path' => null,
            ],
            [
                'en' => ['name' => 'Hatirpool Kacha Bazar', 'description' => 'Popular market for fresh produce and fish.', 'address' => 'Hatirpool, Dhaka 1205'],
                'bn' => ['name' => 'হাতিরপুল কাঁচা বাজার', 'description' => 'তাজা শাকসবজি ও মাছের জন্য জনপ্রিয় বাজার।', 'address' => 'হাতিরপুল, ঢাকা ১২০৫'],
                'slug' => 'hatirpool-kacha-bazar', 'latitude' => 23.7390, 'longitude' => 90.3900,
                'division' => 'Dhaka', 'district' => 'Dhaka', 'upazila_or_thana' => 'Dhanmondi',
                'image_path' => null,
            ],
            [
                'en' => ['name' => 'Mohammadpur Krishi Market', 'description' => 'Government regulated agricultural market.', 'address' => 'Mohammadpur, Dhaka 1207'],
                'bn' => ['name' => 'মোহাম্মদপুর কৃষি মার্কেট', 'description' => 'সরকার নিয়ন্ত্রিত কৃষি বাজার।', 'address' => 'মোহাম্মদপুর, ঢাকা ১২০৭'],
                'slug' => 'mohammadpur-krishi-market', 'latitude' => 23.7665, 'longitude' => 90.3587,
                'division' => 'Dhaka', 'district' => 'Dhaka', 'upazila_or_thana' => 'Mohammadpur',
                'image_path' => null,
            ],
            [
                'en' => ['name' => 'Shantinagar Bazar', 'description' => 'Well organized kitchen market.', 'address' => 'Shantinagar, Dhaka 1217'],
                'bn' => ['name' => 'শান্তিনগর বাজার', 'description' => 'সুসংগঠিত কিচেন মার্কেট।', 'address' => 'শান্তিনগর, ঢাকা ১২১৭'],
                'slug' => 'shantinagar-bazar', 'latitude' => 23.7368, 'longitude' => 90.4132,
                'division' => 'Dhaka', 'district' => 'Dhaka', 'upazila_or_thana' => 'Paltan',
                'image_path' => null,
            ],
            [
                'en' => ['name' => 'New Market', 'description' => 'Historic market complex.', 'address' => 'New Market, Dhaka 1205'],
                'bn' => ['name' => 'নিউ মার্কেট', 'description' => 'ঐতিহাসিক মার্কেট কমপ্লেক্স।', 'address' => 'নিউ মার্কেট, ঢাকা ১২০৫'],
                'slug' => 'new-market', 'latitude' => 23.7328, 'longitude' => 90.3852,
                'division' => 'Dhaka', 'district' => 'Dhaka', 'upazila_or_thana' => 'New Market',
                'image_path' => null,
            ],
        ];

        $createdMarkets = [];
        foreach ($marketsData as $marketData) {
            $en = $marketData['en'];
            $bn = $marketData['bn'];
            $market = Market::factory()->create([
                'name' => $en['name'],
                'description' => $en['description'],
                'address' => $en['address'],
                'slug' => $marketData['slug'],
                'latitude' => $marketData['latitude'],
                'longitude' => $marketData['longitude'],
                'division' => $marketData['division'],
                'district' => $marketData['district'],
                'upazila_or_thana' => $marketData['upazila_or_thana'],
                'zone_id' => $zone->id,
                'image_path' => $marketData['image_path'],
                'is_active' => true,
                'is_featured' => true,
                'visibility' => true,
            ]);
            $mt = $market->translateOrNew('bn');
            foreach ($bn as $field => $value) {
                $mt->setAttribute($field, $value);
            }
            $mt->save();
            $createdMarkets[] = $market;
        }

        // 5. Create Products with bilingual translations
        $productsData = [
            // Rice & Grocery
            ['en' => ['name' => 'Miniket Rice', 'description' => 'Premium quality Miniket rice.'], 'bn' => ['name' => 'মিনিকেট চাল', 'description' => 'প্রিমিয়াম মানের মিনিকেট চাল।'], 'category_id' => $catGrocery->id, 'unit_id' => $unitKg->id, 'base_price' => 75.00, 'sku' => 'RICE-MIN-001'],
            ['en' => ['name' => 'Nazirshail Rice', 'description' => 'Fine grain Nazirshail rice.'], 'bn' => ['name' => 'নাজিরশাইল চাল', 'description' => 'সরু দানার নাজিরশাইল চাল।'], 'category_id' => $catGrocery->id, 'unit_id' => $unitKg->id, 'base_price' => 85.00, 'sku' => 'RICE-NAZ-002'],
            ['en' => ['name' => 'Masoor Dal (Deshi)', 'description' => 'Local variety Masoor lentils.'], 'bn' => ['name' => 'মসুর ডাল (দেশি)', 'description' => 'স্থানীয় জাতের মসুর ডাল।'], 'category_id' => $catGrocery->id, 'unit_id' => $unitKg->id, 'base_price' => 140.00, 'sku' => 'DAL-MAS-001'],
            ['en' => ['name' => 'Soybean Oil (Rupchanda)', 'description' => 'Fortified Soybean Oil.'], 'bn' => ['name' => 'সয়াবিন তেল (রুপচাঁদা)', 'description' => 'ফোর্টিফাইড সয়াবিন তেল।'], 'category_id' => $catGrocery->id, 'unit_id' => $unitL->id, 'base_price' => 170.00, 'sku' => 'OIL-SOY-001'],
            ['en' => ['name' => 'Mustard Oil (Radhuni)', 'description' => 'Pure Mustard Oil.'], 'bn' => ['name' => 'সরিষার তেল (রাধুনি)', 'description' => 'খাঁটি সরিষার তেল।'], 'category_id' => $catGrocery->id, 'unit_id' => $unitL->id, 'base_price' => 280.00, 'sku' => 'OIL-MUS-001'],
            // Vegetables
            ['en' => ['name' => 'Potato (Diamond)', 'description' => 'Fresh Diamond potatoes.'], 'bn' => ['name' => 'আলু (ডায়মন্ড)', 'description' => 'তাজা ডায়মন্ড আলু।'], 'category_id' => $catVeg->id, 'unit_id' => $unitKg->id, 'base_price' => 45.00, 'sku' => 'VEG-POT-001'],
            ['en' => ['name' => 'Onion (Deshi)', 'description' => 'Local onions.'], 'bn' => ['name' => 'পেঁয়াজ (দেশি)', 'description' => 'স্থানীয় পেঁয়াজ।'], 'category_id' => $catVeg->id, 'unit_id' => $unitKg->id, 'base_price' => 90.00, 'sku' => 'VEG-ONI-001'],
            ['en' => ['name' => 'Tomato (Ripe)', 'description' => 'Fresh ripe tomatoes.'], 'bn' => ['name' => 'টমেটো (পাকা)', 'description' => 'তাজা পাকা টমেটো।'], 'category_id' => $catVeg->id, 'unit_id' => $unitKg->id, 'base_price' => 60.00, 'sku' => 'VEG-TOM-001'],
            ['en' => ['name' => 'Green Chili', 'description' => 'Spicy green chilies.'], 'bn' => ['name' => 'কাঁচামরিচ', 'description' => 'ঝাল সবুজ কাঁচামরিচ।'], 'category_id' => $catVeg->id, 'unit_id' => $unitKg->id, 'base_price' => 120.00, 'sku' => 'VEG-CHI-001'],
            ['en' => ['name' => 'Brinjal (Long)', 'description' => 'Fresh long brinjals.'], 'bn' => ['name' => 'বেগুন (লম্বা)', 'description' => 'তাজা লম্বা বেগুন।'], 'category_id' => $catVeg->id, 'unit_id' => $unitKg->id, 'base_price' => 50.00, 'sku' => 'VEG-BRI-001'],
            // Fish
            ['en' => ['name' => 'Hilsha Fish (1kg+)', 'description' => 'Padma river Hilsha.'], 'bn' => ['name' => 'ইলিশ মাছ (১কেজি+)', 'description' => 'পদ্মা নদীর ইলিশ।'], 'category_id' => $catFish->id, 'unit_id' => $unitPc->id, 'base_price' => 1500.00, 'sku' => 'FISH-HIL-001'],
            ['en' => ['name' => 'Rui Fish', 'description' => 'Fresh Rui fish.'], 'bn' => ['name' => 'রুই মাছ', 'description' => 'তাজা রুই মাছ।'], 'category_id' => $catFish->id, 'unit_id' => $unitKg->id, 'base_price' => 350.00, 'sku' => 'FISH-RUI-001'],
            ['en' => ['name' => 'Tilapia', 'description' => 'Farm fresh Tilapia.'], 'bn' => ['name' => 'তেলাপিয়া মাছ', 'description' => 'খামারের তাজা তেলাপিয়া।'], 'category_id' => $catFish->id, 'unit_id' => $unitKg->id, 'base_price' => 180.00, 'sku' => 'FISH-TIL-001'],
            // Meat
            ['en' => ['name' => 'Beef (Bone-in)', 'description' => 'Fresh beef with bone.'], 'bn' => ['name' => 'গরুর মাংস (হাড়সহ)', 'description' => 'হাড়সহ তাজা গরুর মাংস।'], 'category_id' => $catMeat->id, 'unit_id' => $unitKg->id, 'base_price' => 750.00, 'sku' => 'MEAT-BEE-001'],
            ['en' => ['name' => 'Broiler Chicken', 'description' => 'Farm fresh broiler chicken.'], 'bn' => ['name' => 'ব্রয়লার মুরগি', 'description' => 'খামারের তাজা ব্রয়লার মুরগি।'], 'category_id' => $catMeat->id, 'unit_id' => $unitKg->id, 'base_price' => 200.00, 'sku' => 'MEAT-CHI-001'],
            ['en' => ['name' => 'Mutton', 'description' => 'Fresh mutton.'], 'bn' => ['name' => 'খাসির মাংস', 'description' => 'তাজা খাসির মাংস।'], 'category_id' => $catMeat->id, 'unit_id' => $unitKg->id, 'base_price' => 1100.00, 'sku' => 'MEAT-MUT-001'],
            // Fruits
            ['en' => ['name' => 'Mango (Himsagar)', 'description' => 'Sweet Himsagar mangoes.'], 'bn' => ['name' => 'আম (হিমসাগর)', 'description' => 'মিষ্টি হিমসাগর আম।'], 'category_id' => $catFruit->id, 'unit_id' => $unitKg->id, 'base_price' => 120.00, 'sku' => 'FRU-MAN-001'],
            ['en' => ['name' => 'Banana (Sagor)', 'description' => 'Ripe Sagor bananas.'], 'bn' => ['name' => 'কলা (সাগর)', 'description' => 'পাকা সাগর কলা।'], 'category_id' => $catFruit->id, 'unit_id' => $unitDoz->id, 'base_price' => 100.00, 'sku' => 'FRU-BAN-001'],
            // Spices
            ['en' => ['name' => 'Turmeric Powder', 'description' => 'Pure turmeric powder.'], 'bn' => ['name' => 'হলুদ গুঁড়া', 'description' => 'খাঁটি হলুদ গুঁড়া।'], 'category_id' => $catSpices->id, 'unit_id' => $unitKg->id, 'base_price' => 300.00, 'sku' => 'SPI-TUR-001'],
            ['en' => ['name' => 'Chili Powder', 'description' => 'Spicy chili powder.'], 'bn' => ['name' => 'মরিচের গুঁড়া', 'description' => 'ঝাল মরিচের গুঁড়া।'], 'category_id' => $catSpices->id, 'unit_id' => $unitKg->id, 'base_price' => 400.00, 'sku' => 'SPI-CHI-001'],
        ];

        $createdProducts = [];
        foreach ($productsData as $prodData) {
            $en = $prodData['en'];
            $bn = $prodData['bn'];
            $product = Product::factory()->create([
                'name' => $en['name'],
                'description' => $en['description'],
                'category_id' => $prodData['category_id'],
                'unit_id' => $prodData['unit_id'],
                'base_price' => $prodData['base_price'],
                'sku' => $prodData['sku'],
                'status' => 'active',
                'is_visible' => true,
                'is_featured' => rand(0, 1),
                'country_of_origin' => 'Bangladesh',
                'brand' => 'Local',
                'image_path' => null,
            ]);
            $pt = $product->translateOrNew('bn');
            foreach ($bn as $field => $value) {
                $pt->setAttribute($field, $value);
            }
            $pt->save();
            $createdProducts[] = $product;
        }

        // 6. Create Product Market Prices
        foreach ($createdProducts as $product) {
            foreach ($createdMarkets as $market) {
                $variation = rand(-5, 10);
                $price = $product->base_price * (1 + $variation / 100);

                ProductMarketPrice::factory()->create([
                    'product_id' => $product->id,
                    'market_id' => $market->id,
                    'price' => round($price, 2),
                    'discount_price' => rand(0, 10) > 8 ? round($price * 0.95, 2) : null,
                    'price_date' => now(),
                ]);
            }
        }

        // 7. Create Banners with bilingual translations
        $bannersData = [
            ['en' => ['title' => 'Fresh Vegetables'], 'bn' => ['title' => 'তাজা সবজি'], 'is_featured' => true, 'image_path' => null],
            ['en' => ['title' => 'Eid Special Discount'], 'bn' => ['title' => 'ঈদ স্পেশাল ডিসকাউন্ট'], 'is_featured' => false, 'image_path' => null],
            ['en' => ['title' => 'Winter Collection'], 'bn' => ['title' => 'শীতকালীন সংগ্রহ'], 'is_featured' => true, 'image_path' => null],
        ];

        foreach ($bannersData as $bannerData) {
            $banner = Banner::factory()->create([
                'title' => $bannerData['en']['title'],
                'is_featured' => $bannerData['is_featured'],
                'is_active' => true,
                'image_path' => $bannerData['image_path'],
            ]);
            $bt = $banner->translateOrNew('bn');
            $bt->title = $bannerData['bn']['title'];
            $bt->save();
            $banner->zones()->attach($zone->id);
        }

        // 8. Seed language settings
        Setting::updateOrCreate(
            ['key_name' => 'default_language', 'settings_type' => 'languages'],
            ['value' => 'en']
        );
        Setting::updateOrCreate(
            ['key_name' => 'enabled_languages', 'settings_type' => 'languages'],
            ['value' => [
                ['code' => 'en', 'name' => 'English', 'direction' => 'ltr'],
                ['code' => 'bn', 'name' => 'বাংলা (Bengali)', 'direction' => 'ltr'],
            ]]
        );

        // 9. Create Admin User with role
        $admin = Admin::where('email', 'admin@bazardor.com')->first();

        if (! $admin) {
            $admin = Admin::factory()->create([
                'name' => 'Super Admin',
                'email' => 'admin@bazardor.com',
                'password' => Hash::make('12345678'),
                'is_active' => true,
            ]);
        }

        // Ensure admin has the super_admin role
        if (! $admin->hasRole('super_admin')) {
            $admin->assignRole('super_admin');
        }

        $this->command->info('Downloading product images...');
        Artisan::call('products:download-images', [], $this->command->getOutput());

        $this->command->info('Downloading market images...');
        Artisan::call('markets:download-images', [], $this->command->getOutput());

        $this->command->info('Downloading banner images...');
        Artisan::call('banners:download-images', [], $this->command->getOutput());

        $this->command->info('Downloading category images...');
        Artisan::call('categories:download-images', [], $this->command->getOutput());
    }
}
