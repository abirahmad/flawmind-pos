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
        Schema::create('contacts', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->unsignedInteger('business_id')->unsigned();
            $table->string('type', 191);
            $table->string('contact_type', 191)->nullable()->default(null);
            $table->string('supplier_business_name', 191)->nullable()->default(null);
            $table->string('name', 191)->nullable()->default(null);
            $table->string('prefix', 191)->nullable()->default(null);
            $table->string('first_name', 191)->nullable()->default(null);
            $table->string('middle_name', 191)->nullable()->default(null);
            $table->string('last_name', 191)->nullable()->default(null);
            $table->string('email', 191)->nullable()->default(null);
            $table->string('contact_id', 191)->nullable()->default(null);
            $table->string('contact_status', 191)->default('active');
            $table->string('tax_number', 191)->nullable()->default(null);
            $table->string('city', 191)->nullable()->default(null);
            $table->string('state', 191)->nullable()->default(null);
            $table->string('country', 191)->nullable()->default(null);
            $table->text('address_line_1')->nullable()->default(null);
            $table->text('address_line_2')->nullable()->default(null);
            $table->string('zip_code', 191)->nullable()->default(null);
            $table->date('dob')->nullable()->default(null);
            $table->string('mobile', 191);
            $table->string('landline', 191)->nullable()->default(null);
            $table->string('alternate_number', 191)->nullable()->default(null);
            $table->integer('pay_term_number')->nullable()->default(null);
            $table->enum('pay_term_type', ['days','months'])->nullable()->default(null);
            $table->decimal('credit_limit', 22, 4)->nullable()->default(null);
            $table->unsignedInteger('created_by')->unsigned();
            $table->integer('converted_by')->nullable()->default(null);
            $table->dateTime('converted_on')->nullable()->default(null);
            $table->decimal('balance', 22, 4);
            $table->integer('total_rp')->comment('rp is the short form of reward points');
            $table->integer('total_rp_used')->comment('rp is the short form of reward points');
            $table->integer('total_rp_expired')->comment('rp is the short form of reward points');
            $table->boolean('is_default');
            $table->text('shipping_address')->nullable()->default(null);
            $table->longText('shipping_custom_field_details')->nullable()->default(null);
            $table->boolean('is_export');
            $table->string('export_custom_field_1', 191)->nullable()->default(null);
            $table->string('export_custom_field_2', 191)->nullable()->default(null);
            $table->string('export_custom_field_3', 191)->nullable()->default(null);
            $table->string('export_custom_field_4', 191)->nullable()->default(null);
            $table->string('export_custom_field_5', 191)->nullable()->default(null);
            $table->string('export_custom_field_6', 191)->nullable()->default(null);
            $table->string('position', 191)->nullable()->default(null);
            $table->integer('customer_group_id')->nullable()->default(null);
            $table->string('crm_source', 191)->nullable()->default(null);
            $table->string('crm_life_stage', 191)->nullable()->default(null);
            $table->string('custom_field1', 191)->nullable()->default(null);
            $table->string('custom_field2', 191)->nullable()->default(null);
            $table->string('custom_field3', 191)->nullable()->default(null);
            $table->string('custom_field4', 191)->nullable()->default(null);
            $table->string('custom_field5', 191)->nullable()->default(null);
            $table->string('custom_field6', 191)->nullable()->default(null);
            $table->string('custom_field7', 191)->nullable()->default(null);
            $table->string('custom_field8', 191)->nullable()->default(null);
            $table->string('custom_field9', 191)->nullable()->default(null);
            $table->string('custom_field10', 191)->nullable()->default(null);
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
        Schema::dropIfExists('contacts');
    }
};
