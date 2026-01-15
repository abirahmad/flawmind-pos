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
        Schema::create('transaction_payments', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->unsignedInteger('transaction_id')->nullable()->default(null)->unsigned();
            $table->integer('business_id')->nullable()->default(null);
            $table->boolean('is_return')->comment('Used during sales to return the change');
            $table->decimal('amount', 22, 4);
            $table->string('method', 191)->nullable()->default(null);
            $table->string('payment_type', 191)->nullable()->default(null);
            $table->string('transaction_no', 191)->nullable()->default(null);
            $table->string('card_transaction_number', 191)->nullable()->default(null);
            $table->string('card_number', 191)->nullable()->default(null);
            $table->string('card_type', 191)->nullable()->default(null);
            $table->string('card_holder_name', 191)->nullable()->default(null);
            $table->string('card_month', 191)->nullable()->default(null);
            $table->string('card_year', 191)->nullable()->default(null);
            $table->string('card_security', 5)->nullable()->default(null);
            $table->string('cheque_number', 191)->nullable()->default(null);
            $table->string('bank_account_number', 191)->nullable()->default(null);
            $table->dateTime('paid_on')->nullable()->default(null);
            $table->integer('created_by')->nullable()->default(null);
            $table->boolean('paid_through_link');
            $table->string('gateway', 191)->nullable()->default(null);
            $table->boolean('is_advance');
            $table->integer('payment_for')->nullable()->default(null)->comment('stores the contact id');
            $table->integer('parent_id')->nullable()->default(null);
            $table->string('note', 191)->nullable()->default(null);
            $table->string('document', 191)->nullable()->default(null);
            $table->string('payment_ref_no', 191)->nullable()->default(null);
            $table->integer('account_id')->nullable()->default(null);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_payments');
    }
};
