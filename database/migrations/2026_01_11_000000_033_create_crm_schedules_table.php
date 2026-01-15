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
        Schema::create('crm_schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->unsigned();
            $table->unsignedInteger('business_id')->unsigned();
            $table->integer('contact_id')->nullable()->default(null);
            $table->string('title', 191);
            $table->string('status', 191)->nullable()->default(null);
            $table->dateTime('start_datetime')->nullable()->default(null);
            $table->dateTime('end_datetime')->nullable()->default(null);
            $table->text('description')->nullable()->default(null);
            $table->enum('schedule_type', ['call','sms','meeting','email'])->default('email');
            $table->integer('followup_category_id')->nullable()->default(null);
            $table->boolean('allow_notification');
            $table->text('notify_via')->nullable()->default(null);
            $table->integer('notify_before')->nullable()->default(null);
            $table->enum('notify_type', ['minute','hour','day'])->default('hour');
            $table->integer('created_by');
            $table->boolean('is_recursive');
            $table->integer('recursion_days')->nullable()->default(null);
            $table->text('followup_additional_info')->nullable()->default(null);
            $table->string('follow_up_by', 191)->nullable()->default(null);
            $table->string('follow_up_by_value', 191)->nullable()->default(null);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_schedules');
    }
};
