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
        Schema::create('variations', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->string('name', 191);
            $table->unsignedInteger('product_id')->unsigned();
            $table->string('sub_sku', 191)->nullable()->default(null);
            $table->unsignedInteger('product_variation_id')->unsigned();
            $table->integer('variation_value_id')->nullable()->default(null);
            $table->decimal('default_purchase_price', 22, 4)->nullable()->default(null);
            $table->decimal('dpp_inc_tax', 22, 4);
            $table->decimal('profit_percent', 22, 4);
            $table->decimal('default_sell_price', 22, 4)->nullable()->default(null);
            $table->decimal('sell_price_inc_tax', 22, 4)->nullable()->default(null)->comment('Sell price including tax');
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
            $table->timestamp('deleted_at')->nullable()->default(null);
            $table->text('combo_variations')->nullable()->default(null)->comment('Contains the combo variation details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variations');
    }
};
