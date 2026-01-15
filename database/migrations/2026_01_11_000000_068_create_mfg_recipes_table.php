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
        Schema::create('mfg_recipes', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->integer('product_id');
            $table->integer('variation_id');
            $table->text('instructions')->nullable()->default(null);
            $table->decimal('waste_percent', 10, 2);
            $table->decimal('ingredients_cost', 22, 4);
            $table->decimal('extra_cost', 22, 4);
            $table->string('production_cost_type', 191)->default('percentage');
            $table->decimal('total_quantity', 22, 4);
            $table->decimal('final_price', 22, 4);
            $table->integer('sub_unit_id')->nullable()->default(null);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mfg_recipes');
    }
};
