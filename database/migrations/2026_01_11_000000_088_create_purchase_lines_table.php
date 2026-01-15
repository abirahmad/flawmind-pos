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
        Schema::create('purchase_lines', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->unsignedInteger('transaction_id')->unsigned();
            $table->unsignedInteger('product_id')->unsigned();
            $table->unsignedInteger('variation_id')->unsigned();
            $table->decimal('quantity', 22, 4);
            $table->decimal('secondary_unit_quantity', 22, 4);
            $table->decimal('pp_without_discount', 22, 4)->comment('Purchase price before inline discounts');
            $table->decimal('discount_percent', 5, 2)->comment('Inline discount percentage');
            $table->decimal('purchase_price', 22, 4);
            $table->decimal('purchase_price_inc_tax', 22, 4);
            $table->decimal('item_tax', 22, 4)->comment('Tax for one quantity');
            $table->unsignedInteger('tax_id')->nullable()->default(null)->unsigned();
            $table->integer('purchase_requisition_line_id')->nullable()->default(null);
            $table->integer('purchase_order_line_id')->nullable()->default(null);
            $table->decimal('quantity_sold', 22, 4)->comment('Quanity sold from this purchase line');
            $table->decimal('quantity_adjusted', 22, 4)->comment('Quanity adjusted in stock adjustment from this purchase line');
            $table->decimal('quantity_returned', 22, 4);
            $table->decimal('po_quantity_purchased', 22, 4);
            $table->decimal('mfg_quantity_used', 22, 4);
            $table->date('mfg_date')->nullable()->default(null);
            $table->date('exp_date')->nullable()->default(null);
            $table->string('lot_number', 191)->nullable()->default(null);
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
        Schema::dropIfExists('purchase_lines');
    }
};
