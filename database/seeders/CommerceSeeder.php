<?php

namespace Database\Seeders;

use App\Models\PaymentBank;
use App\Models\SkinType;
use Illuminate\Database\Seeder;

class CommerceSeeder extends Seeder
{
    public function run(): void
    {
        $skinTypes = [
            ['name' => 'Dry', 'description' => 'Skin that lacks moisture and may feel tight.'],
            ['name' => 'Oily', 'description' => 'Skin with excess sebum and visible shine.'],
            ['name' => 'Sensitive', 'description' => 'Skin prone to redness and irritation.'],
            ['name' => 'Normal', 'description' => 'Balanced skin with few concerns.'],
            ['name' => 'Combination', 'description' => 'Oily T-zone with drier cheeks.'],
        ];

        foreach ($skinTypes as $type) {
            SkinType::firstOrCreate(['name' => $type['name']], $type);
        }

        $banks = [
            [
                'bank_name' => 'KBZ Pay',
                'account_name' => 'Syrn Cosmetics',
                'account_number' => '09-123-456-789',
                'qr_image' => null,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'bank_name' => 'AYA Pay',
                'account_name' => 'Syrn Cosmetics',
                'account_number' => '09-987-654-321',
                'qr_image' => null,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'bank_name' => 'CB Bank',
                'account_name' => 'Syrn Cosmetics Co., Ltd.',
                'account_number' => '1234567890123',
                'qr_image' => null,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($banks as $bank) {
            PaymentBank::firstOrCreate(
                ['bank_name' => $bank['bank_name']],
                $bank
            );
        }
    }
}
