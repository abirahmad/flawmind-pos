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
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->unsignedInteger('business_id')->unsigned();
            $table->integer('location_id')->nullable()->default(null);
            $table->unsignedInteger('user_id')->nullable()->default(null)->unsigned();
            $table->enum('status', ['close','open'])->default('open');
            $table->dateTime('closed_at')->nullable()->default(null);
            $table->decimal('closing_amount', 22, 4);
            $table->integer('total_card_slips');
            $table->integer('total_cheques');
            $table->text('denominations')->nullable()->default(null);
            $table->text('closing_note')->nullable()->default(null);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_registers');
    }
};
