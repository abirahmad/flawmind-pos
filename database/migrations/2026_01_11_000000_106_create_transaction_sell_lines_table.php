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
        Schema::create('transaction_sell_lines', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->unsignedInteger('transaction_id')->unsigned();
            $table->unsignedInteger('product_id')->unsigned();
            $table->unsignedInteger('variation_id')->unsigned();
            $table->decimal('quantity', 22, 4);
            $table->decimal('mfg_waste_percent', 22, 4);
            $table->integer('mfg_ingredient_group_id')->nullable()->default(null);
            $table->decimal('secondary_unit_quantity', 22, 4);
            $table->decimal('quantity_returned', 20, 4);
            $table->decimal('unit_price_before_discount', 22, 4);
            $table->decimal('unit_price', 22, 4)->nullable()->default(null)->comment('Sell price excluding tax');
            $table->enum('line_discount_type', ['fixed','percentage'])->nullable()->default(null);
            $table->decimal('line_discount_amount', 22, 4);
            $table->decimal('unit_price_inc_tax', 22, 4)->nullable()->default(null)->comment('Sell price including tax');
            $table->decimal('item_tax', 22, 4)->comment('Tax for one quantity');
            $table->unsignedInteger('tax_id')->nullable()->default(null)->unsigned();
            $table->integer('discount_id')->nullable()->default(null);
            $table->integer('lot_no_line_id')->nullable()->default(null);
            $table->text('sell_line_note')->nullable()->default(null);
            $table->integer('so_line_id')->nullable()->default(null);
            $table->decimal('so_quantity_invoiced', 22, 4);
            $table->integer('res_service_staff_id')->nullable()->default(null);
            $table->string('res_line_order_status', 191)->nullable()->default(null);
            $table->integer('parent_sell_line_id')->nullable()->default(null);
            $table->string('children_type', 191)->comment('Type of children for the parent, like modifier or combo');
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
        Schema::dropIfExists('transaction_sell_lines');
    }
};
