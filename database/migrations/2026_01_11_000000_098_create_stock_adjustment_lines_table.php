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
        Schema::create('stock_adjustment_lines', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->unsignedInteger('transaction_id')->unsigned();
            $table->unsignedInteger('product_id')->unsigned();
            $table->unsignedInteger('variation_id')->unsigned();
            $table->decimal('quantity', 22, 4);
            $table->decimal('secondary_unit_quantity', 22, 4);
            $table->decimal('unit_price', 22, 4)->nullable()->default(null)->comment('Last purchase unit price');
            $table->integer('removed_purchase_line')->nullable()->default(null);
            $table->integer('lot_no_line_id')->nullable()->default(null);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustment_lines');
    }
};
