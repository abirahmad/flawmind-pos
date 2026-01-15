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
        Schema::create('asset_maintenances', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->unsigned();
            $table->integer('business_id');
            $table->integer('asset_id');
            $table->string('maitenance_id', 191)->nullable()->default(null);
            $table->string('status', 191)->nullable()->default(null);
            $table->string('priority', 191)->nullable()->default(null);
            $table->integer('created_by');
            $table->integer('assigned_to')->nullable()->default(null);
            $table->text('details')->nullable()->default(null);
            $table->text('maintenance_note')->nullable()->default(null);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_maintenances');
    }
};
