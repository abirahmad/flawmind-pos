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
        Schema::create('products', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->string('name', 191);
            $table->unsignedInteger('business_id')->unsigned();
            $table->enum('type', ['single','variable','modifier','combo'])->nullable()->default(null);
            $table->unsignedInteger('unit_id')->nullable()->default(null)->unsigned();
            $table->integer('secondary_unit_id')->nullable()->default(null);
            $table->text('sub_unit_ids')->nullable()->default(null);
            $table->unsignedInteger('brand_id')->nullable()->default(null)->unsigned();
            $table->unsignedInteger('category_id')->nullable()->default(null)->unsigned();
            $table->unsignedInteger('sub_category_id')->nullable()->default(null)->unsigned();
            $table->unsignedInteger('tax')->nullable()->default(null)->unsigned();
            $table->enum('tax_type', ['inclusive','exclusive']);
            $table->boolean('enable_stock');
            $table->decimal('alert_quantity', 22, 4)->nullable()->default(null);
            $table->string('sku', 191);
            $table->enum('barcode_type', ['c39','c128','ean13','ean8','upca','upce'])->default('C128');
            $table->decimal('expiry_period', 4, 2)->nullable()->default(null);
            $table->enum('expiry_period_type', ['days','months'])->nullable()->default(null);
            $table->boolean('enable_sr_no');
            $table->string('weight', 191)->nullable()->default(null);
            $table->string('product_custom_field1', 191)->nullable()->default(null);
            $table->string('product_custom_field2', 191)->nullable()->default(null);
            $table->string('product_custom_field3', 191)->nullable()->default(null);
            $table->string('product_custom_field4', 191)->nullable()->default(null);
            $table->string('product_custom_field5', 191)->nullable()->default(null);
            $table->string('product_custom_field6', 191)->nullable()->default(null);
            $table->string('product_custom_field7', 191)->nullable()->default(null);
            $table->string('product_custom_field8', 191)->nullable()->default(null);
            $table->string('product_custom_field9', 191)->nullable()->default(null);
            $table->string('product_custom_field10', 191)->nullable()->default(null);
            $table->string('product_custom_field11', 191)->nullable()->default(null);
            $table->string('product_custom_field12', 191)->nullable()->default(null);
            $table->string('product_custom_field13', 191)->nullable()->default(null);
            $table->string('product_custom_field14', 191)->nullable()->default(null);
            $table->string('product_custom_field15', 191)->nullable()->default(null);
            $table->string('product_custom_field16', 191)->nullable()->default(null);
            $table->string('product_custom_field17', 191)->nullable()->default(null);
            $table->string('product_custom_field18', 191)->nullable()->default(null);
            $table->string('product_custom_field19', 191)->nullable()->default(null);
            $table->string('product_custom_field20', 191)->nullable()->default(null);
            $table->string('image', 191)->nullable()->default(null);
            $table->text('product_description')->nullable()->default(null);
            $table->unsignedInteger('created_by')->unsigned();
            $table->integer('preparation_time_in_minutes')->nullable()->default(null);
            $table->integer('warranty_id')->nullable()->default(null);
            $table->boolean('is_inactive');
            $table->boolean('not_for_selling');
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
