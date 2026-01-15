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
        Schema::create('types_of_services', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->string('name', 191);
            $table->text('description')->nullable()->default(null);
            $table->integer('business_id');
            $table->text('location_price_group')->nullable()->default(null);
            $table->decimal('packing_charge', 22, 4)->nullable()->default(null);
            $table->enum('packing_charge_type', ['fixed','percent'])->nullable()->default(null);
            $table->boolean('enable_custom_fields');
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('types_of_services');
    }
};
