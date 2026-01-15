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
        Schema::create('crm_proposal_templates', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->unsigned();
            $table->unsignedInteger('business_id')->unsigned();
            $table->text('subject');
            $table->longText('body');
            $table->text('cc')->nullable()->default(null);
            $table->text('bcc')->nullable()->default(null);
            $table->integer('created_by');
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_proposal_templates');
    }
};
