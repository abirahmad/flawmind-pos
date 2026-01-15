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
        Schema::create('essentials_reminders', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->integer('business_id');
            $table->integer('user_id');
            $table->string('name', 191);
            $table->date('date');
            $table->time('time');
            $table->time('end_time')->nullable()->default(null);
            $table->enum('repeat', ['one_time','every_day','every_week','every_month']);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('essentials_reminders');
    }
};
