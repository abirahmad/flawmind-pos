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
        Schema::create('assets', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned()->primary()->autoIncrement();
            $table->unsignedInteger('business_id')->unsigned();
            $table->string('asset_code', 191);
            $table->string('name', 191);
            $table->decimal('quantity', 22, 4);
            $table->string('model', 191)->nullable()->default(null);
            $table->string('serial_no', 191)->nullable()->default(null);
            $table->unsignedInteger('category_id')->nullable()->default(null)->unsigned();
            $table->unsignedInteger('location_id')->nullable()->default(null)->unsigned();
            $table->date('purchase_date')->nullable()->default(null);
            $table->string('purchase_type', 191)->nullable()->default(null);
            $table->decimal('unit_price', 22, 4);
            $table->decimal('depreciation', 22, 4)->nullable()->default(null);
            $table->boolean('is_allocatable');
            $table->text('description')->nullable()->default(null);
            $table->unsignedInteger('created_by')->unsigned();
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
