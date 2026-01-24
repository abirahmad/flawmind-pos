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
        // Add primary key and auto_increment to transactions table
        DB::statement('ALTER TABLE transactions MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');

        // Also fix other tables that might have the same issue
        DB::statement('ALTER TABLE transaction_payments MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
        DB::statement('ALTER TABLE transaction_sell_lines MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back (though this is rarely needed)
        DB::statement('ALTER TABLE transactions MODIFY id INT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE transaction_payments MODIFY id INT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE transaction_sell_lines MODIFY id INT UNSIGNED NOT NULL');
    }
};
