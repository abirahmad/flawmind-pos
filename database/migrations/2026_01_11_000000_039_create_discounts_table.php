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
        Schema::create('discounts', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->string('name', 191);
            $table->integer('business_id');
            $table->integer('brand_id')->nullable()->default(null);
            $table->integer('category_id')->nullable()->default(null);
            $table->integer('location_id')->nullable()->default(null);
            $table->integer('priority')->nullable()->default(null);
            $table->string('discount_type', 191)->nullable()->default(null);
            $table->decimal('discount_amount', 22, 4);
            $table->dateTime('starts_at')->nullable()->default(null);
            $table->dateTime('ends_at')->nullable()->default(null);
            $table->boolean('is_active');
            $table->string('spg', 100)->nullable()->default(null)->comment('Applicable in specified selling price group only. Use of applicable_in_spg column is discontinued');
            $table->boolean('applicable_in_cg');
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
