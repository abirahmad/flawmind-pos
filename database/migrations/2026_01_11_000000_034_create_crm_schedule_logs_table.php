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
        Schema::create('crm_schedule_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->unsigned();
            $table->unsignedBigInteger('schedule_id')->unsigned();
            $table->enum('log_type', ['call','sms','meeting','email'])->default('email');
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->string('subject', 191);
            $table->text('description')->nullable()->default(null);
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
        Schema::dropIfExists('crm_schedule_logs');
    }
};
