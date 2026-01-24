<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add AUTO_INCREMENT to the id column and make it the primary key
        DB::statement('ALTER TABLE contacts MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE contacts MODIFY id INT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE contacts DROP PRIMARY KEY');
    }
};
