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
        Schema::create('superadmin_frontend_pages', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->string('title', 191)->nullable()->default(null);
            $table->string('slug', 191);
            $table->longText('content');
            $table->boolean('is_shown');
            $table->integer('menu_order');
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('superadmin_frontend_pages');
    }
};
