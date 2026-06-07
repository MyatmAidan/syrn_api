<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Disable foreign key checks for clean truncation in SQLite or MySQL/MariaDB
        $dbConnection = config('database.default');
        if ($dbConnection === 'sqlite') {
            \Illuminate\Support\Facades\DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        }

        // Truncate tables to avoid duplicates
        \App\Models\OrderPayment::truncate();
        \App\Models\OrderItem::truncate();
        \App\Models\Order::truncate();
        \App\Models\CartItem::truncate();
        \App\Models\Cart::truncate();
        \App\Models\PaymentBank::truncate();
        \App\Models\Review::truncate();
        \App\Models\Favourite::truncate();
        \App\Models\RoutineStep::truncate();
        \App\Models\Routine::truncate();
        \App\Models\Product::truncate();
        \App\Models\SkinType::truncate();
        \App\Models\Category::truncate();
        \App\Models\Brand::truncate();
        \App\Models\User::truncate();

        if ($dbConnection === 'sqlite') {
            \Illuminate\Support\Facades\DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        }

        // 1. Seed Brands
        $brandsData = [
            'CeraVe' => 'Developed with dermatologists, CeraVe has a complete line of skincare products containing three essential ceramides.',
            'La Roche-Posay' => 'Recommended by 90,000 dermatologists worldwide, La Roche-Posay offers skin care formulated with mineral-rich thermal water.',
            'Cetaphil' => 'Recommended by dermatologists for over 70 years, Cetaphil offers gentle skincare for all skin types.',
            "Paula's Choice" => 'Smart, Safe Beauty. Paula\'s Choice Skincare products are effective, fragrance-free, and cruelty-free.',
            "I'm From" => 'Korean skincare brand showcasing the purity of nature with high-quality ingredients sourced from local farms.',
            'The Ordinary' => 'Clinical formulations with integrity. The Ordinary offers effective skincare solutions at honest prices.',
            'KraveBeauty' => 'Focused on simple, skin-barrier-first formulations to simplify your skincare routine.',
            'Neutrogena' => 'A dermatologist-recommended brand offering a wide range of skin and hair care products.',
            'EltaMD' => 'Broad-spectrum sunscreens and skin care products formulated for every skin type and concern.',
        ];

        $brandIds = [];
        foreach ($brandsData as $name => $desc) {
            $brand = \App\Models\Brand::create([
                'brand_name' => $name,
                'description' => $desc,
            ]);
            $brandIds[$name] = $brand->brand_id;
        }

        $this->call(CommerceSeeder::class);

        $skinTypeIds = \App\Models\SkinType::pluck('skin_type_id', 'name');

        // 2. Seed Categories
        $categories = [
            [
                'category_id' => 1,
                'category_name' => 'Cleansers',
                'description' => 'Gentle facial cleansers to purify and refresh the skin'
            ],
            [
                'category_id' => 2,
                'category_name' => 'Toners',
                'description' => 'Hydrating and balancing toners to prepare the skin'
            ],
            [
                'category_id' => 3,
                'category_name' => 'Serums',
                'description' => 'Concentrated active treatments to target specific skin concerns'
            ],
            [
                'category_id' => 4,
                'category_name' => 'Moisturizers',
                'description' => 'Nourishing creams and gels to hydrate and lock in moisture'
            ],
            [
                'category_id' => 5,
                'category_name' => 'Sunscreens',
                'description' => 'Broad-spectrum UV protection shields for daily defense'
            ],
        ];

        foreach ($categories as $cat) {
            \App\Models\Category::create($cat);
        }

        // 3. Seed Products
        $products = [
            // Cleansers
            [
                'category_id' => 1,
                'product_name' => 'Hydrating Facial Cleanser',
                'brand_id' => $brandIds['CeraVe'],
                'ingredients' => 'Aqua, Glycerin, Cetearyl Alcohol, Ceramide NP, Ceramide AP, Ceramide EOP, Hyaluronic Acid, Phytosphingosine',
                'skin_type_id' => $skinTypeIds['Dry'] ?? null,
                'skin_concern' => 'Dryness',
                'price' => 14.99,
                'qty' => 50,
                'description' => 'A gentle, non-foaming cleanser designed to cleanse and refresh the skin without over-stripping or leaving it tight and dry.',
                'images' => [],
            ],
            [
                'category_id' => 1,
                'product_name' => 'Toleriane Purifying Foaming Cleanser',
                'brand_id' => $brandIds['La Roche-Posay'],
                'ingredients' => 'Aqua, Glycerin, Coco-Betaine, Niacinamide, Sodium Chloride, Sodium Cocoyl Glycinate, Ceramide NP',
                'skin_type_id' => $skinTypeIds['Oily'] ?? null,
                'qty' => 40,
                'skin_concern' => 'Acne & Blemishes',
                'price' => 16.99,
                'description' => 'A daily foaming face wash for normal to oily sensitive skin. Gently removes makeup, dirt, impurities, and excess oil.',
                'images' => [],
            ],
            [
                'category_id' => 1,
                'product_name' => 'Gentle Skin Cleanser',
                'brand_id' => $brandIds['Cetaphil'],
                'ingredients' => 'Aqua, Glycerin, Cetearyl Alcohol, Panthenol, Niacinamide, Pantolactone, Sodium Cocoyl Isethionate',
                'skin_type_id' => $skinTypeIds['Sensitive'] ?? null,
                'qty' => 35,
                'skin_concern' => 'Redness & Skin Barrier Repair',
                'price' => 12.49,
                'description' => 'This clinically proven formula gently removes dirt, makeup, and impurities while preserving the skin\'s natural moisture barrier.',
                'images' => [],
            ],
            // Toners
            [
                'category_id' => 2,
                'product_name' => 'Skin Perfecting 2% BHA Liquid Exfoliant',
                'brand_id' => $brandIds["Paula's Choice"],
                'ingredients' => 'Water, Methylpropanediol, Butylene Glycol, Salicylic Acid, Polysorbate 20, Camellia Oleifera (Green Tea) Leaf Extract',
                'skin_type_id' => $skinTypeIds['Oily'] ?? null,
                'qty' => 40,
                'skin_concern' => 'Acne & Blemishes',
                'price' => 34.00,
                'description' => 'A unique leave-on formula gentle enough for daily use on all skin types. Exfoliates dead skin cells while clearing pores for a more even, radiant tone.',
                'images' => [],
            ],
            [
                'category_id' => 2,
                'product_name' => 'Rice Toner',
                'brand_id' => $brandIds["I'm From"],
                'ingredients' => 'Oryza Sativa (Rice) Extract, Methylpropanediol, Triethylhexanoin, Hydrogenated Poly(C6-14 Olefin), Niacinamide, Adenosine',
                'skin_type_id' => $skinTypeIds['Dry'] ?? null,
                'qty' => 30,
                'skin_concern' => 'Dryness',
                'price' => 28.00,
                'description' => 'Formulated with 77.78% Rice Extract, this toner helps skin retain moisture and form a protective barrier against dryness.',
                'images' => [],
            ],
            // Serums
            [
                'category_id' => 3,
                'product_name' => 'Niacinamide 10% + Zinc 1%',
                'brand_id' => $brandIds['The Ordinary'],
                'ingredients' => 'Aqua, Niacinamide, Pentylene Glycol, Zinc PCA, Tamarindus Indica Seed Gum, Xanthan Gum, Phenoxyethanol',
                'skin_type_id' => $skinTypeIds['Oily'] ?? null,
                'qty' => 40,
                'skin_concern' => 'Acne & Blemishes',
                'price' => 8.90,
                'description' => 'A high-strength vitamin and mineral blemish formula with 10% pure niacinamide and 1% zinc PCA to visibly regulate sebum and target blemishes.',
                'images' => [],
            ],
            [
                'category_id' => 3,
                'product_name' => 'Great Barrier Relief',
                'brand_id' => $brandIds['KraveBeauty'],
                'ingredients' => 'Water, Calophyllum Inophyllum (Tamanu) Seed Oil, Squalane, Niacinamide, Rosehip Oil, Ceramide NP, Centella Asiatica Extract',
                'skin_type_id' => $skinTypeIds['Sensitive'] ?? null,
                'qty' => 35,
                'skin_concern' => 'Redness & Skin Barrier Repair',
                'price' => 28.00,
                'description' => 'A skin-soothing serum that restores your damaged skin barrier while evening out skin tone and calming irritation.',
                'images' => [],
            ],
            // Moisturizers
            [
                'category_id' => 4,
                'product_name' => 'Moisturizing Cream',
                'brand_id' => $brandIds['CeraVe'],
                'ingredients' => 'Water, Glycerin, Cetearyl Alcohol, Caprylic/Capric Triglyceride, Ceramide NP, Ceramide AP, Hyaluronic Acid',
                'skin_type_id' => $skinTypeIds['Dry'] ?? null,
                'qty' => 30,
                'skin_concern' => 'Dryness',
                'price' => 17.50,
                'description' => 'A rich, non-greasy, fast-absorbing moisturizing cream developed with dermatologists to help hydrate and restore the skin barrier.',
                'images' => [],
            ],
            [
                'category_id' => 4,
                'product_name' => 'Hydro Boost Water Gel',
                'brand_id' => $brandIds['Neutrogena'],
                'ingredients' => 'Water, Dimethicone, Glycerin, Dimethicone Crosspolymer, Sodium Hyaluronate, Tocopheryl Acetate, Laureth-7',
                'skin_type_id' => $skinTypeIds['Oily'] ?? null,
                'qty' => 40,
                'skin_concern' => 'Dryness',
                'price' => 19.99,
                'description' => 'A lightweight, oil-free gel formula that delivers intense, instant hydration to quench dry skin and keep it looking smooth and supple.',
                'images' => [],
            ],
            // Sunscreens
            [
                'category_id' => 5,
                'product_name' => 'Anthelios Ultra-Light Fluid SPF 50',
                'brand_id' => $brandIds['La Roche-Posay'],
                'ingredients' => 'Aqua, Diisopropyl Sebacate, Alcohol Denat, Silica, Ethylhexyl Salicylate, Bis-Ethylhexyloxyphenol Methoxyphenyl Triazine',
                'skin_type_id' => $skinTypeIds['Normal'] ?? null,
                'qty' => 25,
                'skin_concern' => 'Sun Protection',
                'price' => 29.99,
                'description' => 'An ultra-lightweight, fluid sunscreen that offers high broad-spectrum UV protection with an invisible matte finish for sensitive skin.',
                'images' => [],
            ],
            [
                'category_id' => 5,
                'product_name' => 'UV Clear Broad-Spectrum SPF 46',
                'brand_id' => $brandIds['EltaMD'],
                'ingredients' => 'Zinc Oxide, Octinoxate, Purified Water, Cyclopentasiloxane, Niacinamide, Octyldodecyl Neopentanoate',
                'skin_type_id' => $skinTypeIds['Sensitive'] ?? null,
                'qty' => 35,
                'skin_concern' => 'Sun Protection',
                'price' => 39.00,
                'description' => 'Oil-free EltaMD UV Clear helps calm and protect sensitive skin types prone to discoloration and breakouts associated with acne and rosacea.',
                'images' => [],
            ],
        ];

        $productModels = [];
        foreach ($products as $prod) {
            $productModels[] = \App\Models\Product::create($prod);
        }

        // 3. Seed Default Test User
        $testUser = \App\Models\User::create([
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password_hash' => \Illuminate\Support\Facades\Hash::make('password'),
            'skin_type' => 'Sensitive',
            'skin_concern' => 'Redness & Skin Barrier Repair',
        ]);

        // 4. Seed Random Users
        $users = \App\Models\User::factory(5)->create();
        $allUsers = collect([$testUser])->merge($users);

        // 5. Seed Reviews
        $comments = [
            5 => [
                'Absolutely love this! It transformed my skin barrier in a week.',
                'A holy grail product for me. Highly recommend to everyone.',
                'Perfect texture, doesn\'t cause breakouts, and is extremely soothing.',
            ],
            4 => [
                'Very good product, works well but is a bit expensive.',
                'Fits my routine perfectly. Moisturizing without being greasy.',
                'Great ingredients list, noticed a solid improvement in skin texture.',
            ],
            3 => [
                'It\'s decent, but I didn\'t see any dramatic changes.',
                'Okay product. It does the job but there are better options out there.',
            ]
        ];

        foreach ($productModels as $product) {
            // Seed 2-3 reviews per product
            $numReviews = rand(2, 3);
            $chosenUsers = $allUsers->random($numReviews);

            foreach ($chosenUsers as $user) {
                $rating = rand(3, 5);
                $commentList = $comments[$rating];
                $comment = $commentList[array_rand($commentList)];

                \App\Models\Review::create([
                    'user_id' => $user->user_id,
                    'product_id' => $product->product_id,
                    'rating' => $rating,
                    'comment' => $comment,
                    'review_date' => now()->subDays(rand(1, 30)),
                ]);
            }
        }
        // 6. Seed Admins
        $this->call(AdminSeeder::class);
    }
}
