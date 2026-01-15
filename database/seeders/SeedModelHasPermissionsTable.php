<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeedModelHasPermissionsTable extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate table before seeding
        DB::table('model_has_permissions')->truncate();

        // Note: The original SQL contained INSERT statements.
        // You may need to manually review and adjust this data.
        // Uncomment and modify the following to insert seed data:

        /*
        DB::table('model_has_permissions')->insert([
            // Add your seed data here
        ]);
        */
    }
}
