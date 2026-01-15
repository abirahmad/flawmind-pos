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
        Schema::create('accounting_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->unsigned();
            $table->string('name', 191);
            $table->string('gl_code', 191)->nullable()->default(null);
            $table->integer('business_id');
            $table->string('account_primary_type', 191)->nullable()->default(null);
            $table->bigInteger('account_sub_type_id')->nullable()->default(null);
            $table->bigInteger('detail_type_id')->nullable()->default(null);
            $table->bigInteger('parent_account_id')->nullable()->default(null);
            $table->longText('description')->nullable()->default(null);
            $table->string('status', 191)->nullable()->default(null);
            $table->integer('created_by');
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_accounts');
    }
};
