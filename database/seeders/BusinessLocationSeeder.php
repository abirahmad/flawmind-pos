<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('business_locations')->insert([
            'id' => 1,
            'business_id' => 1,
            'location_id' => 'LOC-001',
            'name' => 'Main Store',
            'landmark' => 'Near City Center',
            'country' => 'United States',
            'state' => 'California',
            'city' => 'Los Angeles',
            'zip_code' => '90001',
            'invoice_scheme_id' => 1,
            'sale_invoice_scheme_id' => 1,
            'invoice_layout_id' => 1,
            'sale_invoice_layout_id' => 1,
            'selling_price_group_id' => null,
            'print_receipt_on_invoice' => true,
            'receipt_printer_type' => 'browser',
            'printer_id' => null,
            'mobile' => '+1234567890',
            'alternate_number' => null,
            'email' => 'store@flawmind.com',
            'website' => 'https://flawmind.com',
            'featured_products' => null,
            'is_active' => true,
            'default_payment_accounts' => null,
            'custom_field1' => null,
            'custom_field2' => null,
            'custom_field3' => null,
            'custom_field4' => null,
            'accounting_default_map' => null,
            'deleted_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
