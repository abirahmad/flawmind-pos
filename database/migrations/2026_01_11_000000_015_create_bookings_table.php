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
        Schema::create('bookings', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->unsignedInteger('contact_id')->unsigned();
            $table->unsignedInteger('waiter_id')->nullable()->default(null)->unsigned();
            $table->unsignedInteger('table_id')->nullable()->default(null)->unsigned();
            $table->integer('correspondent_id')->nullable()->default(null);
            $table->unsignedInteger('business_id')->unsigned();
            $table->unsignedInteger('location_id')->unsigned();
            $table->dateTime('booking_start');
            $table->dateTime('booking_end');
            $table->unsignedInteger('created_by')->unsigned();
            $table->string('booking_status', 191);
            $table->text('booking_note')->nullable()->default(null);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
