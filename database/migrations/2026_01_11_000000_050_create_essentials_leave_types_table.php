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
        Schema::create('essentials_leave_types', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->string('leave_type', 191);
            $table->integer('max_leave_count')->nullable()->default(null);
            $table->enum('leave_count_interval', ['month','year'])->nullable()->default(null);
            $table->integer('business_id');
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('essentials_leave_types');
    }
};
