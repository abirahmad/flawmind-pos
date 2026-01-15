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
        Schema::create('accounting_acc_trans_mappings', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->unsigned();
            $table->integer('business_id');
            $table->string('ref_no', 100);
            $table->string('type', 100);
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
        Schema::dropIfExists('accounting_acc_trans_mappings');
    }
};
