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
        Schema::create('accounting_account_types', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->unsigned();
            $table->string('name', 191);
            $table->integer('business_id')->nullable()->default(null);
            $table->integer('created_by')->nullable()->default(null);
            $table->string('account_primary_type', 191)->nullable()->default(null);
            $table->string('account_type', 191)->nullable()->default(null);
            $table->bigInteger('parent_id')->nullable()->default(null);
            $table->text('description')->nullable()->default(null);
            $table->boolean('show_balance');
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_account_types');
    }
};
