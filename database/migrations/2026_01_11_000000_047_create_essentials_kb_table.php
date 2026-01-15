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
        Schema::create('essentials_kb', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->unsigned();
            $table->unsignedBigInteger('business_id')->unsigned();
            $table->string('title', 191);
            $table->longText('content')->nullable()->default(null);
            $table->string('status', 191);
            $table->string('kb_type', 191);
            $table->unsignedBigInteger('parent_id')->nullable()->default(null)->comment('id from essentials_kb table')->unsigned();
            $table->string('share_with', 191)->nullable()->default(null)->comment('public, private, only_with');
            $table->unsignedBigInteger('created_by')->unsigned();
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('essentials_kb');
    }
};
