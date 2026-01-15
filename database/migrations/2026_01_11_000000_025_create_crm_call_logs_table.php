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
        Schema::create('crm_call_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->unsigned();
            $table->integer('business_id');
            $table->integer('user_id')->nullable()->default(null);
            $table->string('call_type', 191)->nullable()->default(null);
            $table->string('mobile_number', 191);
            $table->string('mobile_name', 191)->nullable()->default(null);
            $table->integer('contact_id')->nullable()->default(null);
            $table->dateTime('start_time')->nullable()->default(null);
            $table->dateTime('end_time')->nullable()->default(null);
            $table->integer('duration')->nullable()->default(null);
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
        Schema::dropIfExists('crm_call_logs');
    }
};
