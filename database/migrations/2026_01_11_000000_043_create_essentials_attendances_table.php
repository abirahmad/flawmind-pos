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
        Schema::create('essentials_attendances', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->integer('user_id');
            $table->integer('business_id');
            $table->dateTime('clock_in_time')->nullable()->default(null);
            $table->dateTime('clock_out_time')->nullable()->default(null);
            $table->integer('essentials_shift_id')->nullable()->default(null);
            $table->string('ip_address', 191)->nullable()->default(null);
            $table->text('clock_in_note')->nullable()->default(null);
            $table->text('clock_out_note')->nullable()->default(null);
            $table->text('clock_in_location')->nullable()->default(null);
            $table->text('clock_out_location')->nullable()->default(null);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('essentials_attendances');
    }
};
