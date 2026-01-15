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
        Schema::create('variation_location_details', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->unsignedInteger('product_id')->unsigned();
            $table->unsignedInteger('product_variation_id')->comment('id from product_variations table')->unsigned();
            $table->unsignedInteger('variation_id')->unsigned();
            $table->unsignedInteger('location_id')->unsigned();
            $table->decimal('qty_available', 22, 4);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variation_location_details');
    }
};
