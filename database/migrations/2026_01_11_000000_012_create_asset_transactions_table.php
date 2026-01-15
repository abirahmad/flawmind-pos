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
        Schema::create('asset_transactions', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->unsignedInteger('business_id')->unsigned();
            $table->unsignedInteger('asset_id')->nullable()->default(null)->unsigned();
            $table->string('transaction_type', 191);
            $table->string('ref_no', 191);
            $table->unsignedInteger('receiver')->nullable()->default(null)->comment('id from users table, who receives asset')->unsigned();
            $table->decimal('quantity', 22, 4);
            $table->dateTime('transaction_datetime');
            $table->date('allocated_upto')->nullable()->default(null);
            $table->text('reason')->nullable()->default(null);
            $table->unsignedInteger('parent_id')->nullable()->default(null)->comment('id from asset_transactions table')->unsigned();
            $table->unsignedInteger('created_by')->comment('id from users table, who allocated asset')->unsigned();
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_transactions');
    }
};
