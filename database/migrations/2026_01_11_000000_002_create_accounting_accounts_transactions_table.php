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
        Schema::create('accounting_accounts_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->unsigned();
            $table->unsignedBigInteger('accounting_account_id')->unsigned();
            $table->integer('acc_trans_mapping_id')->nullable()->default(null)->comment('id form accounting_acc_trans_mapping table');
            $table->integer('transaction_id')->nullable()->default(null)->comment('id form transactions table');
            $table->integer('transaction_payment_id')->nullable()->default(null)->comment('id form transaction_payments table');
            $table->decimal('amount', 22, 4);
            $table->string('type', 100)->comment('debit, credit etc');
            $table->string('sub_type', 100);
            $table->string('map_type', 100)->nullable()->default(null);
            $table->integer('created_by');
            $table->dateTime('operation_date');
            $table->text('note')->nullable()->default(null);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_accounts_transactions');
    }
};
