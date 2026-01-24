<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceSchemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('invoice_schemes')->insert([
            'id' => 1,
            'business_id' => 1,
            'name' => 'Default Invoice Scheme',
            'scheme_type' => 'blank',
            'number_type' => 'sequential',
            'prefix' => 'INV-',
            'start_number' => 1,
            'invoice_count' => 0,
            'total_digits' => 6,
            'is_default' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
