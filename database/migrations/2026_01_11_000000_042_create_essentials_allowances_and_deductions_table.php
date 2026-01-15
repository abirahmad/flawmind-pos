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
        Schema::create('essentials_allowances_and_deductions', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->integer('business_id');
            $table->string('description', 191);
            $table->enum('type', ['allowance','deduction']);
            $table->decimal('amount', 22, 4);
            $table->enum('amount_type', ['fixed','percent']);
            $table->date('applicable_date')->nullable()->default(null);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('essentials_allowances_and_deductions');
    }
};
