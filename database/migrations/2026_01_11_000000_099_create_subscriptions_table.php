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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->unsignedInteger('business_id')->unsigned();
            $table->unsignedInteger('package_id')->unsigned();
            $table->date('start_date')->nullable()->default(null);
            $table->date('trial_end_date')->nullable()->default(null);
            $table->date('end_date')->nullable()->default(null);
            $table->decimal('package_price', 22, 4);
            $table->longText('package_details');
            $table->unsignedInteger('created_id')->unsigned();
            $table->string('paid_via', 191)->nullable()->default(null);
            $table->string('payment_transaction_id', 191)->nullable()->default(null);
            $table->enum('status', ['approved','waiting','declined'])->default('waiting');
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
        Schema::dropIfExists('subscriptions');
    }
};
