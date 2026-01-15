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
        // Clear existing data since we're changing primary key types
        DB::table('oauth_personal_access_clients')->truncate();
        DB::table('oauth_refresh_tokens')->truncate();
        DB::table('oauth_access_tokens')->truncate();
        DB::table('oauth_auth_codes')->truncate();
        DB::table('oauth_clients')->truncate();

        // Update oauth_clients table - must use raw SQL to handle auto_increment removal
        DB::statement('ALTER TABLE oauth_clients MODIFY id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE oauth_clients DROP PRIMARY KEY');
        DB::statement('ALTER TABLE oauth_clients MODIFY id CHAR(36) NOT NULL');
        DB::statement('ALTER TABLE oauth_clients ADD PRIMARY KEY (id)');

        // Update oauth_access_tokens table - change client_id type
        if (Schema::hasColumn('oauth_access_tokens', 'client_id')) {
            DB::statement('ALTER TABLE oauth_access_tokens MODIFY client_id CHAR(36) NOT NULL');
        }

        // Update oauth_auth_codes table - change client_id type
        if (Schema::hasColumn('oauth_auth_codes', 'client_id')) {
            DB::statement('ALTER TABLE oauth_auth_codes MODIFY client_id CHAR(36) NOT NULL');
        }

        // Update oauth_personal_access_clients table - change client_id type
        if (Schema::hasColumn('oauth_personal_access_clients', 'client_id')) {
            DB::statement('ALTER TABLE oauth_personal_access_clients MODIFY client_id CHAR(36) NOT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not easily reversible
        // as it changes primary key types and truncates data
    }
};
