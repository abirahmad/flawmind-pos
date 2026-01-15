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
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->integer('business_id');
            $table->string('template_for', 191);
            $table->text('email_body')->nullable()->default(null);
            $table->text('sms_body')->nullable()->default(null);
            $table->text('whatsapp_text')->nullable()->default(null);
            $table->string('subject', 191)->nullable()->default(null);
            $table->string('cc', 191)->nullable()->default(null);
            $table->string('bcc', 191)->nullable()->default(null);
            $table->boolean('auto_send');
            $table->boolean('auto_send_sms');
            $table->boolean('auto_send_wa_notif');
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};
