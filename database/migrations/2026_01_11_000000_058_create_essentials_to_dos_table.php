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
        Schema::create('essentials_to_dos', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->integer('business_id');
            $table->text('task');
            $table->dateTime('date')->nullable()->default(null);
            $table->dateTime('end_date')->nullable()->default(null);
            $table->string('task_id', 191)->nullable()->default(null);
            $table->text('description')->nullable()->default(null);
            $table->string('status', 191)->nullable()->default(null);
            $table->string('estimated_hours', 191)->nullable()->default(null);
            $table->string('priority', 191)->nullable()->default(null);
            $table->integer('created_by')->nullable()->default(null);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('essentials_to_dos');
    }
};
