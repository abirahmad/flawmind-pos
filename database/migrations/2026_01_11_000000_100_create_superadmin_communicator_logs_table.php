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
        Schema::create('superadmin_communicator_logs', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->text('business_ids')->nullable()->default(null);
            $table->string('subject', 191)->nullable()->default(null);
            $table->text('message')->nullable()->default(null);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('superadmin_communicator_logs');
    }
};
