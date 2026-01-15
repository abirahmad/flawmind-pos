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
        Schema::create('business_locations', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->unsignedInteger('business_id')->unsigned();
            $table->string('location_id', 191)->nullable()->default(null);
            $table->string('name', 256);
            $table->text('landmark')->nullable()->default(null);
            $table->string('country', 100);
            $table->string('state', 100);
            $table->string('city', 100);
            $table->string('zip_code');
            $table->unsignedInteger('invoice_scheme_id')->unsigned();
            $table->integer('sale_invoice_scheme_id')->nullable()->default(null);
            $table->unsignedInteger('invoice_layout_id')->unsigned();
            $table->integer('sale_invoice_layout_id')->nullable()->default(null);
            $table->integer('selling_price_group_id')->nullable()->default(null);
            $table->boolean('print_receipt_on_invoice');
            $table->enum('receipt_printer_type', ['browser','printer'])->default('browser');
            $table->integer('printer_id')->nullable()->default(null);
            $table->string('mobile', 191)->nullable()->default(null);
            $table->string('alternate_number', 191)->nullable()->default(null);
            $table->string('email', 191)->nullable()->default(null);
            $table->string('website', 191)->nullable()->default(null);
            $table->text('featured_products')->nullable()->default(null);
            $table->boolean('is_active');
            $table->text('default_payment_accounts')->nullable()->default(null);
            $table->string('custom_field1', 191)->nullable()->default(null);
            $table->string('custom_field2', 191)->nullable()->default(null);
            $table->string('custom_field3', 191)->nullable()->default(null);
            $table->string('custom_field4', 191)->nullable()->default(null);
            $table->text('accounting_default_map')->nullable()->default(null)->comment('Default transactions mapping of accounting module');
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
        Schema::dropIfExists('business_locations');
    }
};
