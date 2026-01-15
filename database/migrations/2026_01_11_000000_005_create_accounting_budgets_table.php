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
        Schema::create('accounting_budgets', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->unsigned();
            $table->unsignedBigInteger('accounting_account_id')->unsigned();
            $table->integer('financial_year');
            $table->decimal('jan', 22, 4)->nullable()->default(null);
            $table->decimal('feb', 22, 4)->nullable()->default(null);
            $table->decimal('mar', 22, 4)->nullable()->default(null);
            $table->decimal('apr', 22, 4)->nullable()->default(null);
            $table->decimal('may', 22, 4)->nullable()->default(null);
            $table->decimal('jun', 22, 4)->nullable()->default(null);
            $table->decimal('jul', 22, 4)->nullable()->default(null);
            $table->decimal('aug', 22, 4)->nullable()->default(null);
            $table->decimal('sep', 22, 4)->nullable()->default(null);
            $table->decimal('oct', 22, 4)->nullable()->default(null);
            $table->decimal('nov', 22, 4)->nullable()->default(null);
            $table->decimal('dec', 22, 4)->nullable()->default(null);
            $table->decimal('quarter_1', 22, 4)->nullable()->default(null);
            $table->decimal('quarter_2', 22, 4)->nullable()->default(null);
            $table->decimal('quarter_3', 22, 4)->nullable()->default(null);
            $table->decimal('quarter_4', 22, 4)->nullable()->default(null);
            $table->decimal('yearly', 22, 4)->nullable()->default(null);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_budgets');
    }
};
