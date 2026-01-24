<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'id' => 1,
                'country' => 'United States',
                'currency' => 'US Dollar',
                'code' => 'USD',
                'symbol' => '$',
                'thousand_separator' => ',',
                'decimal_separator' => '.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'country' => 'Bangladesh',
                'currency' => 'Bangladeshi Taka',
                'code' => 'BDT',
                'symbol' => '৳',
                'thousand_separator' => ',',
                'decimal_separator' => '.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'country' => 'India',
                'currency' => 'Indian Rupee',
                'code' => 'INR',
                'symbol' => '₹',
                'thousand_separator' => ',',
                'decimal_separator' => '.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'country' => 'United Kingdom',
                'currency' => 'British Pound',
                'code' => 'GBP',
                'symbol' => '£',
                'thousand_separator' => ',',
                'decimal_separator' => '.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'country' => 'European Union',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
                'thousand_separator' => '.',
                'decimal_separator' => ',',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('currencies')->insert($currencies);
    }
}
