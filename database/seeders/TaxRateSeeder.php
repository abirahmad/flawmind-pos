<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taxRates = [
            [
                'id' => 1,
                'business_id' => 1,
                'name' => 'No Tax',
                'amount' => 0.0000,
                'is_tax_group' => false,
                'for_tax_group' => false,
                'created_by' => 2,
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'business_id' => 1,
                'name' => 'VAT 5%',
                'amount' => 5.0000,
                'is_tax_group' => false,
                'for_tax_group' => false,
                'created_by' => 2,
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'business_id' => 1,
                'name' => 'VAT 10%',
                'amount' => 10.0000,
                'is_tax_group' => false,
                'for_tax_group' => false,
                'created_by' => 2,
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'business_id' => 1,
                'name' => 'VAT 15%',
                'amount' => 15.0000,
                'is_tax_group' => false,
                'for_tax_group' => false,
                'created_by' => 2,
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'business_id' => 1,
                'name' => 'Sales Tax 7.5%',
                'amount' => 7.5000,
                'is_tax_group' => false,
                'for_tax_group' => false,
                'created_by' => 2,
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('tax_rates')->insert($taxRates);
    }
}
