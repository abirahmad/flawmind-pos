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
        Schema::create('account_transactions', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->integer('account_id');
            $table->enum('type', ['debit','credit']);
            $table->enum('sub_type', ['opening_balance','fund_transfer','deposit'])->nullable()->default(null);
            $table->decimal('amount', 22, 4);
            $table->string('reff_no', 191)->nullable()->default(null);
            $table->dateTime('operation_date');
            $table->integer('created_by');
            $table->integer('transaction_id')->nullable()->default(null);
            $table->integer('transaction_payment_id')->nullable()->default(null);
            $table->integer('transfer_transaction_id')->nullable()->default(null);
            $table->text('note')->nullable()->default(null);
            $table->timestamp('deleted_at')->nullable()->default(null);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_transactions');
    }
};
