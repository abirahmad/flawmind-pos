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
        Schema::create('barcodes', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->string('name', 191);
            $table->text('description')->nullable()->default(null);
            $table->double('width', 22, 4)->nullable()->default(null);
            $table->double('height', 22, 4)->nullable()->default(null);
            $table->double('paper_width', 22, 4)->nullable()->default(null);
            $table->double('paper_height', 22, 4)->nullable()->default(null);
            $table->double('top_margin', 22, 4)->nullable()->default(null);
            $table->double('left_margin', 22, 4)->nullable()->default(null);
            $table->double('row_distance', 22, 4)->nullable()->default(null);
            $table->double('col_distance', 22, 4)->nullable()->default(null);
            $table->integer('stickers_in_one_row')->nullable()->default(null);
            $table->boolean('is_default');
            $table->boolean('is_continuous');
            $table->integer('stickers_in_one_sheet')->nullable()->default(null);
            $table->unsignedInteger('business_id')->nullable()->default(null)->unsigned();
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barcodes');
    }
};
