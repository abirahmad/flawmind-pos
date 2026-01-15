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
        Schema::create('users', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned()->primary()->autoIncrement();
            $table->string('user_type', 191)->default('user');
            $table->string('surname')->nullable()->default(null);
            $table->string('first_name', 191);
            $table->string('last_name', 191)->nullable()->default(null);
            $table->string('username', 191)->nullable()->default(null);
            $table->string('email', 191)->nullable()->default(null);
            $table->string('password', 191)->nullable()->default(null);
            $table->string('language')->default('en');
            $table->string('contact_no')->nullable()->default(null);
            $table->text('address')->nullable()->default(null);
            $table->string('remember_token', 100)->nullable()->default(null);
            $table->unsignedInteger('business_id')->nullable()->default(null)->unsigned();
            $table->integer('essentials_department_id')->nullable()->default(null);
            $table->integer('essentials_designation_id')->nullable()->default(null);
            $table->decimal('essentials_salary', 22, 4)->nullable()->default(null);
            $table->string('essentials_pay_period', 191)->nullable()->default(null);
            $table->string('essentials_pay_cycle', 191)->nullable()->default(null);
            $table->dateTime('available_at')->nullable()->default(null)->comment('Service staff avilable at. Calculated from product preparation_time_in_minutes');
            $table->dateTime('paused_at')->nullable()->default(null)->comment('Service staff available time paused at, Will be nulled on resume.');
            $table->decimal('max_sales_discount_percent', 5, 2)->nullable()->default(null);
            $table->boolean('allow_login');
            $table->enum('status', ['active','inactive','terminated'])->default('active');
            $table->boolean('is_enable_service_staff_pin');
            $table->text('service_staff_pin')->nullable()->default(null);
            $table->unsignedInteger('crm_contact_id')->nullable()->default(null)->unsigned();
            $table->boolean('is_cmmsn_agnt');
            $table->decimal('cmmsn_percent', 4, 2);
            $table->boolean('selected_contacts');
            $table->date('dob')->nullable()->default(null);
            $table->string('gender', 191)->nullable()->default(null);
            $table->enum('marital_status', ['married','unmarried','divorced'])->nullable()->default(null);
            $table->string('blood_group')->nullable()->default(null);
            $table->string('contact_number')->nullable()->default(null);
            $table->string('alt_number', 191)->nullable()->default(null);
            $table->string('family_number', 191)->nullable()->default(null);
            $table->string('fb_link', 191)->nullable()->default(null);
            $table->string('twitter_link', 191)->nullable()->default(null);
            $table->string('social_media_1', 191)->nullable()->default(null);
            $table->string('social_media_2', 191)->nullable()->default(null);
            $table->text('permanent_address')->nullable()->default(null);
            $table->text('current_address')->nullable()->default(null);
            $table->string('guardian_name', 191)->nullable()->default(null);
            $table->string('custom_field_1', 191)->nullable()->default(null);
            $table->string('custom_field_2', 191)->nullable()->default(null);
            $table->string('custom_field_3', 191)->nullable()->default(null);
            $table->string('custom_field_4', 191)->nullable()->default(null);
            $table->longText('bank_details')->nullable()->default(null);
            $table->string('id_proof_name', 191)->nullable()->default(null);
            $table->string('id_proof_number', 191)->nullable()->default(null);
            $table->integer('location_id')->nullable()->default(null)->comment('user primary work location');
            $table->string('crm_department', 191)->nullable()->default(null)->comment('Contact person');
            $table->string('crm_designation', 191)->nullable()->default(null)->comment('Contact person');
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
        Schema::dropIfExists('users');
    }
};
