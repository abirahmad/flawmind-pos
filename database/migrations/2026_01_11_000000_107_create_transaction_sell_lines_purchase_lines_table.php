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
        Schema::create('transaction_sell_lines_purchase_lines', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->unsignedInteger('sell_line_id')->nullable()->default(null)->comment('id from transaction_sell_lines')->unsigned();
            $table->unsignedInteger('stock_adjustment_line_id')->nullable()->default(null)->comment('id from stock_adjustment_lines')->unsigned();
            $table->unsignedInteger('purchase_line_id')->comment('id from purchase_lines')->unsigned();
            $table->decimal('quantity', 22, 4);
            $table->decimal('qty_returned', 22, 4);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_sell_lines_purchase_lines');
    }
};
