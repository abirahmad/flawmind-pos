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
        Schema::create('invoice_schemes', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->unsignedInteger('business_id')->unsigned();
            $table->string('name', 191);
            $table->enum('scheme_type', ['blank','year']);
            $table->string('number_type', 100)->default('sequential');
            $table->string('prefix', 191)->nullable()->default(null);
            $table->integer('start_number')->nullable()->default(null);
            $table->integer('invoice_count');
            $table->integer('total_digits')->nullable()->default(null);
            $table->boolean('is_default');
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_schemes');
    }
};
