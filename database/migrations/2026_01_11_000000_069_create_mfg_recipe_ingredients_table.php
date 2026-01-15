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
        Schema::create('mfg_recipe_ingredients', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->unsignedInteger('mfg_recipe_id')->unsigned();
            $table->integer('variation_id');
            $table->integer('mfg_ingredient_group_id')->nullable()->default(null);
            $table->decimal('quantity', 22, 4);
            $table->decimal('waste_percent', 22, 4);
            $table->integer('sub_unit_id')->nullable()->default(null);
            $table->integer('sort_order')->nullable()->default(null);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mfg_recipe_ingredients');
    }
};
