<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    /**
     * Seed the application's database with demo data.
     * Run with: php artisan db:seed --class=DemoDataSeeder
     */
    public function run(): void
    {
        // Disable foreign key checks for MySQL
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->call([
            CurrencySeeder::class,
            BusinessSeeder::class,
            InvoiceSchemeSeeder::class,
            InvoiceLayoutSeeder::class,
            BusinessLocationSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            TaxRateSeeder::class,
            UnitSeeder::class,
            ProductSeeder::class,
            ContactSeeder::class,
        ]);

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Demo data seeded successfully!');
        $this->command->info('');
        $this->command->info('Test Data Summary:');
        $this->command->info('- 5 Currencies (USD, BDT, INR, GBP, EUR)');
        $this->command->info('- 1 Business (FlawMind Demo Business)');
        $this->command->info('- 1 Business Location (Main Store)');
        $this->command->info('- 5 Categories (Electronics, Clothing, Food & Beverages, Smartphones, Laptops)');
        $this->command->info('- 5 Brands (Apple, Samsung, Sony, Nike, Generic)');
        $this->command->info('- 5 Tax Rates (No Tax, VAT 5%, VAT 10%, VAT 15%, Sales Tax 7.5%)');
        $this->command->info('- 7 Units (Pieces, Kg, g, L, ml, Box, Dozen)');
        $this->command->info('- 5 Products with Variations:');
        $this->command->info('  * iPhone 15 Pro ($1179.94)');
        $this->command->info('  * Samsung Galaxy S24 ($983.06)');
        $this->command->info('  * MacBook Pro 14" ($2110.68)');
        $this->command->info('  * Nike Air Max ($126.00)');
        $this->command->info('  * Coca Cola 500ml ($1.50)');
        $this->command->info('- 5 Contacts:');
        $this->command->info('  * Walk-In Customer (ID: 1, Default)');
        $this->command->info('  * Alice Johnson (ID: 2, Customer)');
        $this->command->info('  * Bob Smith (ID: 3, Customer)');
        $this->command->info('  * Tech Supplies Inc. (ID: 4, Supplier)');
        $this->command->info('  * Global Trading Co. (ID: 5, Both)');
        $this->command->info('');
        $this->command->info('You can now test the Sales APIs with:');
        $this->command->info('- contact_id: 1, 2, or 3 (for customers)');
        $this->command->info('- location_id: 1');
        $this->command->info('- product_id: 1-5, variation_id: 1-5');
        $this->command->info('- tax_id: 1-5 (or omit for no tax)');
    }
}
