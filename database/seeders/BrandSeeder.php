<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'id' => 1,
                'business_id' => 1,
                'name' => 'Apple',
                'description' => 'Apple Inc. - Technology company',
                'created_by' => 2,
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'business_id' => 1,
                'name' => 'Samsung',
                'description' => 'Samsung Electronics',
                'created_by' => 2,
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'business_id' => 1,
                'name' => 'Sony',
                'description' => 'Sony Corporation',
                'created_by' => 2,
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'business_id' => 1,
                'name' => 'Nike',
                'description' => 'Nike, Inc. - Sports apparel',
                'created_by' => 2,
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'business_id' => 1,
                'name' => 'Generic',
                'description' => 'Generic/Unbranded products',
                'created_by' => 2,
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('brands')->insert($brands);
    }
}
