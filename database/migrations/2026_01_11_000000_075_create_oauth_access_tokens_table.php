<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('oauth_access_tokens')) {
            Schema::create('oauth_access_tokens', function (Blueprint $table) {
                $table->string('id', 100)->primary();
                $table->bigInteger('user_id')->nullable()->default(null)->index();
                $table->unsignedInteger('client_id')->unsigned();
                $table->string('name', 191)->nullable()->default(null);
                $table->text('scopes')->nullable()->default(null);
                $table->boolean('revoked');
                $table->timestamp('created_at')->nullable()->default(null);
                $table->timestamp('updated_at')->nullable()->default(null);
                $table->dateTime('expires_at')->nullable()->default(null);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oauth_access_tokens');
    }
};
