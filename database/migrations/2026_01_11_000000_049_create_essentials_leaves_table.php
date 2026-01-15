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
        Schema::create('essentials_leaves', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->integer('essentials_leave_type_id')->nullable()->default(null);
            $table->integer('business_id');
            $table->integer('user_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('ref_no', 191)->nullable()->default(null);
            $table->enum('status', ['pending','approved','cancelled'])->nullable()->default(null);
            $table->text('reason')->nullable()->default(null);
            $table->text('status_note')->nullable()->default(null);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('essentials_leaves');
    }
};
