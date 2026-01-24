<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'id' => 1,
                'name' => 'Electronics',
                'business_id' => 1,
                'short_code' => 'ELEC',
                'parent_id' => 0,
                'created_by' => 2,
                'category_type' => 'product',
                'description' => 'Electronic devices and accessories',
                'slug' => 'electronics',
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Clothing',
                'business_id' => 1,
                'short_code' => 'CLTH',
                'parent_id' => 0,
                'created_by' => 2,
                'category_type' => 'product',
                'description' => 'Apparel and fashion items',
                'slug' => 'clothing',
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Food & Beverages',
                'business_id' => 1,
                'short_code' => 'FOOD',
                'parent_id' => 0,
                'created_by' => 2,
                'category_type' => 'product',
                'description' => 'Food items and drinks',
                'slug' => 'food-beverages',
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Smartphones',
                'business_id' => 1,
                'short_code' => 'SMRT',
                'parent_id' => 1, // Sub-category of Electronics
                'created_by' => 2,
                'category_type' => 'product',
                'description' => 'Mobile phones and smartphones',
                'slug' => 'smartphones',
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Laptops',
                'business_id' => 1,
                'short_code' => 'LPTP',
                'parent_id' => 1, // Sub-category of Electronics
                'created_by' => 2,
                'category_type' => 'product',
                'description' => 'Laptops and notebooks',
                'slug' => 'laptops',
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('categories')->insert($categories);
    }
}
