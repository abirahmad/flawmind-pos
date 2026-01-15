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
        Schema::create('accounts', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->integer('business_id');
            $table->string('name', 191);
            $table->string('account_number', 191);
            $table->text('account_details')->nullable()->default(null);
            $table->integer('account_type_id')->nullable()->default(null);
            $table->text('note')->nullable()->default(null);
            $table->integer('created_by');
            $table->boolean('is_closed');
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
        Schema::dropIfExists('accounts');
    }
};
