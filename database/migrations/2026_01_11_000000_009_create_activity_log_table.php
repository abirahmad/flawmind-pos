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
        Schema::create('activity_log', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->string('log_name', 191)->nullable()->default(null);
            $table->text('description');
            $table->integer('subject_id')->nullable()->default(null);
            $table->string('subject_type', 191)->nullable()->default(null);
            $table->string('event', 191)->nullable()->default(null);
            $table->integer('business_id')->nullable()->default(null);
            $table->integer('causer_id')->nullable()->default(null);
            $table->string('causer_type', 191)->nullable()->default(null);
            $table->text('properties')->nullable()->default(null);
            $table->string('batch_uuid')->nullable()->default(null);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_log');
    }
};
