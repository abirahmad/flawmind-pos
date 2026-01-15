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
        Schema::create('crm_campaigns', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->unsigned();
            $table->unsignedInteger('business_id')->unsigned();
            $table->string('name', 191);
            $table->enum('campaign_type', ['sms','email'])->default('email');
            $table->string('subject', 191)->nullable()->default(null);
            $table->text('email_body')->nullable()->default(null);
            $table->text('sms_body')->nullable()->default(null);
            $table->dateTime('sent_on')->nullable()->default(null);
            $table->text('contact_ids');
            $table->text('additional_info')->nullable()->default(null);
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
        Schema::dropIfExists('crm_campaigns');
    }
};
