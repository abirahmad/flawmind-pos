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
        Schema::create('asset_warranties', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->unsigned();
            $table->integer('asset_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('additional_cost', 22, 4);
            $table->text('additional_note')->nullable()->default(null);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_warranties');
    }
};
