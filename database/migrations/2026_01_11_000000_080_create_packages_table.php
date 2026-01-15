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
        Schema::create('packages', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->string('name', 191);
            $table->text('description');
            $table->integer('location_count')->comment('No. of Business Locations, 0 = infinite option.');
            $table->integer('user_count');
            $table->integer('product_count');
            $table->boolean('bookings')->comment('Enable/Disable bookings');
            $table->boolean('kitchen')->comment('Enable/Disable kitchen');
            $table->boolean('order_screen')->comment('Enable/Disable order_screen');
            $table->boolean('tables')->comment('Enable/Disable tables');
            $table->integer('invoice_count');
            $table->enum('interval', ['days','months','years']);
            $table->integer('interval_count');
            $table->integer('trial_days');
            $table->decimal('price', 22, 4);
            $table->longText('custom_permissions');
            $table->integer('created_by');
            $table->integer('sort_order');
            $table->boolean('is_active');
            $table->boolean('is_private');
            $table->boolean('is_one_time');
            $table->boolean('enable_custom_link');
            $table->string('custom_link', 191)->nullable()->default(null);
            $table->string('custom_link_text', 191)->nullable()->default(null);
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
        Schema::dropIfExists('packages');
    }
};
