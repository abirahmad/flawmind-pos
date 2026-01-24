<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            [
                'id' => 1,
                'business_id' => 1,
                'actual_name' => 'Pieces',
                'short_name' => 'Pc',
                'allow_decimal' => false,
                'base_unit_id' => null,
                'base_unit_multiplier' => null,
                'created_by' => 2,
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'business_id' => 1,
                'actual_name' => 'Kilogram',
                'short_name' => 'Kg',
                'allow_decimal' => true,
                'base_unit_id' => null,
                'base_unit_multiplier' => null,
                'created_by' => 2,
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'business_id' => 1,
                'actual_name' => 'Gram',
                'short_name' => 'g',
                'allow_decimal' => true,
                'base_unit_id' => 2,
                'base_unit_multiplier' => 0.0010,
                'created_by' => 2,
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'business_id' => 1,
                'actual_name' => 'Liter',
                'short_name' => 'L',
                'allow_decimal' => true,
                'base_unit_id' => null,
                'base_unit_multiplier' => null,
                'created_by' => 2,
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'business_id' => 1,
                'actual_name' => 'Milliliter',
                'short_name' => 'ml',
                'allow_decimal' => true,
                'base_unit_id' => 4,
                'base_unit_multiplier' => 0.0010,
                'created_by' => 2,
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'business_id' => 1,
                'actual_name' => 'Box',
                'short_name' => 'Box',
                'allow_decimal' => false,
                'base_unit_id' => null,
                'base_unit_multiplier' => null,
                'created_by' => 2,
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'business_id' => 1,
                'actual_name' => 'Dozen',
                'short_name' => 'Dz',
                'allow_decimal' => false,
                'base_unit_id' => 1,
                'base_unit_multiplier' => 12.0000,
                'created_by' => 2,
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('units')->insert($units);
    }
}
