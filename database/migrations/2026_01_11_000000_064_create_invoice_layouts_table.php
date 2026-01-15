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
        Schema::create('invoice_layouts', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->string('name', 191);
            $table->text('header_text')->nullable()->default(null);
            $table->string('invoice_no_prefix', 191)->nullable()->default(null);
            $table->string('quotation_no_prefix', 191)->nullable()->default(null);
            $table->string('invoice_heading', 191)->nullable()->default(null);
            $table->string('sub_heading_line1', 191)->nullable()->default(null);
            $table->string('sub_heading_line2', 191)->nullable()->default(null);
            $table->string('sub_heading_line3', 191)->nullable()->default(null);
            $table->string('sub_heading_line4', 191)->nullable()->default(null);
            $table->string('sub_heading_line5', 191)->nullable()->default(null);
            $table->string('invoice_heading_not_paid', 191)->nullable()->default(null);
            $table->string('invoice_heading_paid', 191)->nullable()->default(null);
            $table->string('quotation_heading', 191)->nullable()->default(null);
            $table->string('sub_total_label', 191)->nullable()->default(null);
            $table->string('discount_label', 191)->nullable()->default(null);
            $table->string('tax_label', 191)->nullable()->default(null);
            $table->string('total_label', 191)->nullable()->default(null);
            $table->string('round_off_label', 191)->nullable()->default(null);
            $table->string('total_due_label', 191)->nullable()->default(null);
            $table->string('paid_label', 191)->nullable()->default(null);
            $table->boolean('show_client_id');
            $table->string('client_id_label', 191)->nullable()->default(null);
            $table->string('client_tax_label', 191)->nullable()->default(null);
            $table->string('date_label', 191)->nullable()->default(null);
            $table->string('date_time_format', 191)->nullable()->default(null);
            $table->boolean('show_time');
            $table->boolean('show_brand');
            $table->boolean('show_sku');
            $table->boolean('show_cat_code');
            $table->boolean('show_expiry');
            $table->boolean('show_lot');
            $table->boolean('show_image');
            $table->boolean('show_sale_description');
            $table->string('sales_person_label', 191)->nullable()->default(null);
            $table->boolean('show_sales_person');
            $table->string('table_product_label', 191)->nullable()->default(null);
            $table->string('table_qty_label', 191)->nullable()->default(null);
            $table->string('table_unit_price_label', 191)->nullable()->default(null);
            $table->string('table_subtotal_label', 191)->nullable()->default(null);
            $table->string('cat_code_label', 191)->nullable()->default(null);
            $table->string('logo', 191)->nullable()->default(null);
            $table->boolean('show_logo');
            $table->boolean('show_business_name');
            $table->boolean('show_location_name');
            $table->boolean('show_landmark');
            $table->boolean('show_city');
            $table->boolean('show_state');
            $table->boolean('show_zip_code');
            $table->boolean('show_country');
            $table->boolean('show_mobile_number');
            $table->boolean('show_alternate_number');
            $table->boolean('show_email');
            $table->boolean('show_tax_1');
            $table->boolean('show_tax_2');
            $table->boolean('show_barcode');
            $table->boolean('show_payments');
            $table->boolean('show_customer');
            $table->string('customer_label', 191)->nullable()->default(null);
            $table->string('commission_agent_label', 191)->nullable()->default(null);
            $table->boolean('show_commission_agent');
            $table->boolean('show_reward_point');
            $table->string('highlight_color', 10)->nullable()->default(null);
            $table->text('footer_text')->nullable()->default(null);
            $table->text('module_info')->nullable()->default(null);
            $table->text('common_settings')->nullable()->default(null);
            $table->boolean('is_default');
            $table->unsignedInteger('business_id')->unsigned();
            $table->boolean('show_letter_head');
            $table->string('letter_head', 191)->nullable()->default(null);
            $table->boolean('show_qr_code');
            $table->text('qr_code_fields')->nullable()->default(null);
            $table->string('design', 190)->default('classic');
            $table->string('cn_heading', 191)->nullable()->default(null)->comment('cn = credit note');
            $table->string('cn_no_label', 191)->nullable()->default(null);
            $table->string('cn_amount_label', 191)->nullable()->default(null);
            $table->text('table_tax_headings')->nullable()->default(null);
            $table->boolean('show_previous_bal');
            $table->string('prev_bal_label', 191)->nullable()->default(null);
            $table->string('change_return_label', 191)->nullable()->default(null);
            $table->text('product_custom_fields')->nullable()->default(null);
            $table->text('contact_custom_fields')->nullable()->default(null);
            $table->text('location_custom_fields')->nullable()->default(null);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_layouts');
    }
};
