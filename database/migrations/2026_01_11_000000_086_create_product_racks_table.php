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
        Schema::create('product_racks', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->unsignedInteger('business_id')->unsigned();
            $table->unsignedInteger('location_id')->unsigned();
            $table->unsignedInteger('product_id')->unsigned();
            $table->string('rack', 191)->nullable()->default(null);
            $table->string('row', 191)->nullable()->default(null);
            $table->string('position', 191)->nullable()->default(null);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_racks');
    }
};
