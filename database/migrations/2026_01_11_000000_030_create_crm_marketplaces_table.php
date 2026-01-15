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
        Schema::create('crm_marketplaces', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->unsigned();
            $table->integer('business_id');
            $table->string('marketplace', 191)->nullable()->default(null);
            $table->string('site_key', 191)->nullable()->default(null);
            $table->string('site_id', 191)->nullable()->default(null);
            $table->text('assigned_users')->nullable()->default(null);
            $table->integer('crm_source_id')->nullable()->default(null);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_marketplaces');
    }
};
