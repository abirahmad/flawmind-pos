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
        Schema::create('categories', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned()->primary()->autoIncrement();
            $table->string('name', 191);
            $table->unsignedInteger('business_id')->unsigned();
            $table->string('short_code', 191)->nullable()->default(null);
            $table->integer('parent_id');
            $table->unsignedInteger('created_by')->unsigned();
            $table->string('category_type', 191)->nullable()->default(null);
            $table->text('description')->nullable()->default(null);
            $table->string('slug', 191)->nullable()->default(null);
            $table->timestamp('deleted_at')->nullable()->default(null);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
