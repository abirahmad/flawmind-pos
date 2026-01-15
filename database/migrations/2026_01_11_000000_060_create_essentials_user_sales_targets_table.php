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
        Schema::create('essentials_user_sales_targets', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->unsigned();
            $table->integer('user_id');
            $table->decimal('target_start', 22, 4);
            $table->decimal('target_end', 22, 4);
            $table->decimal('commission_percent', 22, 4);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('essentials_user_sales_targets');
    }
};
