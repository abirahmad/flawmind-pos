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
        Schema::create('transactions', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->unsignedInteger('business_id')->unsigned();
            $table->unsignedInteger('location_id')->nullable()->default(null)->unsigned();
            $table->boolean('is_kitchen_order');
            $table->unsignedInteger('res_table_id')->nullable()->default(null)->comment('fields to restaurant module')->unsigned();
            $table->unsignedInteger('res_waiter_id')->nullable()->default(null)->comment('fields to restaurant module')->unsigned();
            $table->enum('res_order_status', ['received','cooked','served'])->nullable()->default(null);
            $table->string('type', 191)->nullable()->default(null);
            $table->string('sub_type', 20)->nullable()->default(null);
            $table->string('status', 191);
            $table->string('sub_status', 191)->nullable()->default(null);
            $table->boolean('is_quotation');
            $table->enum('payment_status', ['paid','due','partial'])->nullable()->default(null);
            $table->enum('adjustment_type', ['normal','abnormal'])->nullable()->default(null);
            $table->unsignedInteger('contact_id')->nullable()->default(null)->unsigned();
            $table->integer('customer_group_id')->nullable()->default(null)->comment('used to add customer group while selling');
            $table->string('invoice_no', 191)->nullable()->default(null);
            $table->string('ref_no', 191)->nullable()->default(null);
            $table->string('source', 191)->nullable()->default(null);
            $table->string('subscription_no', 191)->nullable()->default(null);
            $table->string('subscription_repeat_on', 191)->nullable()->default(null);
            $table->dateTime('transaction_date');
            $table->decimal('total_before_tax', 22, 4)->comment('Total before the purchase/invoice tax, this includeds the indivisual product tax');
            $table->unsignedInteger('tax_id')->nullable()->default(null)->unsigned();
            $table->decimal('tax_amount', 22, 4);
            $table->enum('discount_type', ['fixed','percentage'])->nullable()->default(null);
            $table->decimal('discount_amount', 22, 4);
            $table->integer('rp_redeemed')->comment('rp is the short form of reward points');
            $table->decimal('rp_redeemed_amount', 22, 4)->comment('rp is the short form of reward points');
            $table->string('shipping_details', 191)->nullable()->default(null);
            $table->text('shipping_address')->nullable()->default(null);
            $table->dateTime('delivery_date')->nullable()->default(null);
            $table->string('shipping_status', 191)->nullable()->default(null);
            $table->string('delivered_to', 191)->nullable()->default(null);
            $table->bigInteger('delivery_person')->nullable()->default(null);
            $table->decimal('shipping_charges', 22, 4);
            $table->string('shipping_custom_field_1', 191)->nullable()->default(null);
            $table->string('shipping_custom_field_2', 191)->nullable()->default(null);
            $table->string('shipping_custom_field_3', 191)->nullable()->default(null);
            $table->string('shipping_custom_field_4', 191)->nullable()->default(null);
            $table->string('shipping_custom_field_5', 191)->nullable()->default(null);
            $table->text('additional_notes')->nullable()->default(null);
            $table->text('staff_note')->nullable()->default(null);
            $table->boolean('is_export');
            $table->longText('export_custom_fields_info')->nullable()->default(null);
            $table->decimal('round_off_amount', 22, 4)->comment('Difference of rounded total and actual total');
            $table->string('additional_expense_key_1', 191)->nullable()->default(null);
            $table->decimal('additional_expense_value_1', 22, 4);
            $table->string('additional_expense_key_2', 191)->nullable()->default(null);
            $table->decimal('additional_expense_value_2', 22, 4);
            $table->string('additional_expense_key_3', 191)->nullable()->default(null);
            $table->decimal('additional_expense_value_3', 22, 4);
            $table->string('additional_expense_key_4', 191)->nullable()->default(null);
            $table->decimal('additional_expense_value_4', 22, 4);
            $table->decimal('final_total', 22, 4);
            $table->unsignedInteger('expense_category_id')->nullable()->default(null)->unsigned();
            $table->integer('expense_sub_category_id')->nullable()->default(null);
            $table->unsignedInteger('expense_for')->nullable()->default(null)->unsigned();
            $table->integer('commission_agent')->nullable()->default(null);
            $table->string('document', 191)->nullable()->default(null);
            $table->boolean('is_direct_sale');
            $table->boolean('is_suspend');
            $table->decimal('exchange_rate', 20, 3);
            $table->decimal('total_amount_recovered', 22, 4)->nullable()->default(null)->comment('Used for stock adjustment.');
            $table->integer('transfer_parent_id')->nullable()->default(null);
            $table->integer('return_parent_id')->nullable()->default(null);
            $table->integer('opening_stock_product_id')->nullable()->default(null);
            $table->unsignedInteger('created_by')->unsigned();
            $table->integer('mfg_parent_production_purchase_id')->nullable()->default(null);
            $table->decimal('mfg_wasted_units', 22, 4)->nullable()->default(null);
            $table->decimal('mfg_production_cost', 22, 4);
            $table->string('mfg_production_cost_type', 191)->default('percentage');
            $table->boolean('mfg_is_final');
            $table->decimal('essentials_duration', 8, 2);
            $table->string('essentials_duration_unit', 20)->nullable()->default(null);
            $table->decimal('essentials_amount_per_unit_duration', 22, 4);
            $table->text('essentials_allowances')->nullable()->default(null);
            $table->text('essentials_deductions')->nullable()->default(null);
            $table->text('purchase_requisition_ids')->nullable()->default(null);
            $table->boolean('crm_is_order_request');
            $table->string('prefer_payment_method', 191)->nullable()->default(null);
            $table->integer('prefer_payment_account')->nullable()->default(null);
            $table->text('sales_order_ids')->nullable()->default(null);
            $table->text('purchase_order_ids')->nullable()->default(null);
            $table->string('custom_field_1', 191)->nullable()->default(null);
            $table->string('custom_field_2', 191)->nullable()->default(null);
            $table->string('custom_field_3', 191)->nullable()->default(null);
            $table->string('custom_field_4', 191)->nullable()->default(null);
            $table->integer('import_batch')->nullable()->default(null);
            $table->dateTime('import_time')->nullable()->default(null);
            $table->integer('types_of_service_id')->nullable()->default(null);
            $table->decimal('packing_charge', 22, 4)->nullable()->default(null);
            $table->enum('packing_charge_type', ['fixed','percent'])->nullable()->default(null);
            $table->text('service_custom_field_1')->nullable()->default(null);
            $table->text('service_custom_field_2')->nullable()->default(null);
            $table->text('service_custom_field_3')->nullable()->default(null);
            $table->text('service_custom_field_4')->nullable()->default(null);
            $table->text('service_custom_field_5')->nullable()->default(null);
            $table->text('service_custom_field_6')->nullable()->default(null);
            $table->boolean('is_created_from_api');
            $table->integer('rp_earned')->comment('rp is the short form of reward points');
            $table->text('order_addresses')->nullable()->default(null);
            $table->boolean('is_recurring');
            $table->double('recur_interval', 22, 4)->nullable()->default(null);
            $table->enum('recur_interval_type', ['days','months','years'])->nullable()->default(null);
            $table->integer('recur_repetitions')->nullable()->default(null);
            $table->dateTime('recur_stopped_on')->nullable()->default(null);
            $table->integer('recur_parent_id')->nullable()->default(null);
            $table->string('invoice_token', 191)->nullable()->default(null);
            $table->integer('pay_term_number')->nullable()->default(null);
            $table->enum('pay_term_type', ['days','months'])->nullable()->default(null);
            $table->integer('selling_price_group_id')->nullable()->default(null);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
