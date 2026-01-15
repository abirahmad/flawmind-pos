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
        Schema::create('selling_price_groups', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->string('name', 191);
            $table->text('description')->nullable()->default(null);
            $table->unsignedInteger('business_id')->unsigned();
            $table->boolean('is_active');
            $table->timestamp('deleted_at')->nullable()->default(null);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('selling_price_groups');
    }
};
