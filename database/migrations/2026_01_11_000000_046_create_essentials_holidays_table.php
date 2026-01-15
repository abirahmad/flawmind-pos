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
        Schema::create('essentials_holidays', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->string('name', 191)->nullable()->default(null);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('business_id');
            $table->integer('location_id')->nullable()->default(null);
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
        Schema::dropIfExists('essentials_holidays');
    }
};
