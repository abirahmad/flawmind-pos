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
        Schema::create('business', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned()->primary()->autoIncrement();
            $table->string('name', 191);
            $table->unsignedInteger('currency_id')->unsigned();
            $table->date('start_date')->nullable()->default(null);
            $table->string('tax_number_1', 100)->nullable()->default(null);
            $table->string('tax_label_1', 10)->nullable()->default(null);
            $table->string('tax_number_2', 100)->nullable()->default(null);
            $table->string('tax_label_2', 10)->nullable()->default(null);
            $table->string('code_label_1', 191)->nullable()->default(null);
            $table->string('code_1', 191)->nullable()->default(null);
            $table->string('code_label_2', 191)->nullable()->default(null);
            $table->string('code_2', 191)->nullable()->default(null);
            $table->unsignedInteger('default_sales_tax')->nullable()->default(null)->unsigned();
            $table->double('default_profit_percent', 5, 2);
            $table->unsignedInteger('owner_id')->unsigned();
            $table->string('time_zone', 191)->default('Asia/Kolkata');
            $table->tinyInteger('fy_start_month');
            $table->enum('accounting_method', ['fifo','lifo','avco'])->default('fifo');
            $table->decimal('default_sales_discount', 5, 2)->nullable()->default(null);
            $table->enum('sell_price_tax', ['includes','excludes'])->default('includes');
            $table->string('logo', 191)->nullable()->default(null);
            $table->string('sku_prefix', 191)->nullable()->default(null);
            $table->boolean('enable_product_expiry');
            $table->enum('expiry_type', ['add_expiry','add_manufacturing'])->default('add_expiry');
            $table->enum('on_product_expiry', ['keep_selling','stop_selling','auto_delete'])->default('keep_selling');
            $table->integer('stop_selling_before')->comment('Stop selling expied item n days before expiry');
            $table->boolean('enable_tooltip');
            $table->boolean('purchase_in_diff_currency')->comment('Allow purchase to be in different currency then the business currency');
            $table->unsignedInteger('purchase_currency_id')->nullable()->default(null)->unsigned();
            $table->decimal('p_exchange_rate', 20, 3);
            $table->unsignedInteger('transaction_edit_days')->unsigned();
            $table->unsignedInteger('stock_expiry_alert_days')->unsigned();
            $table->text('keyboard_shortcuts')->nullable()->default(null);
            $table->text('pos_settings')->nullable()->default(null);
            $table->text('manufacturing_settings')->nullable()->default(null);
            $table->longText('essentials_settings')->nullable()->default(null);
            $table->text('weighing_scale_setting')->comment('used to store the configuration of weighing scale');
            $table->boolean('enable_brand');
            $table->boolean('enable_category');
            $table->boolean('enable_sub_category');
            $table->boolean('enable_price_tax');
            $table->boolean('enable_purchase_status');
            $table->boolean('enable_lot_number');
            $table->integer('default_unit')->nullable()->default(null);
            $table->boolean('enable_sub_units');
            $table->boolean('enable_racks');
            $table->boolean('enable_row');
            $table->boolean('enable_position');
            $table->boolean('enable_editing_product_from_purchase');
            $table->enum('sales_cmsn_agnt', ['logged_in_user','user','cmsn_agnt'])->nullable()->default(null);
            $table->boolean('item_addition_method');
            $table->boolean('enable_inline_tax');
            $table->enum('currency_symbol_placement', ['before','after'])->default('before');
            $table->text('enabled_modules')->nullable()->default(null);
            $table->string('date_format', 191)->default('m/d/Y');
            $table->enum('time_format', ['12','24'])->default('24');
            $table->tinyInteger('currency_precision');
            $table->tinyInteger('quantity_precision');
            $table->text('ref_no_prefixes')->nullable()->default(null);
            $table->string('theme_color')->nullable()->default(null);
            $table->integer('created_by')->nullable()->default(null);
            $table->text('asset_settings')->nullable()->default(null);
            $table->text('accounting_settings')->nullable()->default(null);
            $table->text('crm_settings')->nullable()->default(null);
            $table->boolean('enable_rp')->comment('rp is the short form of reward points');
            $table->string('rp_name', 191)->nullable()->default(null)->comment('rp is the short form of reward points');
            $table->decimal('amount_for_unit_rp', 22, 4)->comment('rp is the short form of reward points');
            $table->decimal('min_order_total_for_rp', 22, 4)->comment('rp is the short form of reward points');
            $table->integer('max_rp_per_order')->nullable()->default(null)->comment('rp is the short form of reward points');
            $table->decimal('redeem_amount_per_unit_rp', 22, 4)->comment('rp is the short form of reward points');
            $table->decimal('min_order_total_for_redeem', 22, 4)->comment('rp is the short form of reward points');
            $table->integer('min_redeem_point')->nullable()->default(null)->comment('rp is the short form of reward points');
            $table->integer('max_redeem_point')->nullable()->default(null)->comment('rp is the short form of reward points');
            $table->integer('rp_expiry_period')->nullable()->default(null)->comment('rp is the short form of reward points');
            $table->enum('rp_expiry_type', ['month','year'])->default('year')->comment('rp is the short form of reward points');
            $table->text('email_settings')->nullable()->default(null);
            $table->text('sms_settings')->nullable()->default(null);
            $table->text('custom_labels')->nullable()->default(null);
            $table->text('common_settings')->nullable()->default(null);
            $table->boolean('is_active');
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business');
    }
};
