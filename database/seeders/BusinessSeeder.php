<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('business')->insert([
            'id' => 1,
            'name' => 'FlawMind Demo Business',
            'currency_id' => 1, // USD
            'start_date' => now()->subYear()->format('Y-m-d'),
            'tax_number_1' => 'TAX123456',
            'tax_label_1' => 'VAT',
            'tax_number_2' => null,
            'tax_label_2' => null,
            'code_label_1' => null,
            'code_1' => null,
            'code_label_2' => null,
            'code_2' => null,
            'default_sales_tax' => null,
            'default_profit_percent' => 25.00,
            'owner_id' => 2, // john@example.com user
            'time_zone' => 'UTC',
            'fy_start_month' => 1,
            'accounting_method' => 'fifo',
            'default_sales_discount' => 0.00,
            'sell_price_tax' => 'includes',
            'logo' => null,
            'sku_prefix' => 'SKU',
            'enable_product_expiry' => false,
            'expiry_type' => 'add_expiry',
            'on_product_expiry' => 'keep_selling',
            'stop_selling_before' => 0,
            'enable_tooltip' => true,
            'purchase_in_diff_currency' => false,
            'purchase_currency_id' => null,
            'p_exchange_rate' => 1.000,
            'transaction_edit_days' => 30,
            'stock_expiry_alert_days' => 30,
            'keyboard_shortcuts' => json_encode([]),
            'pos_settings' => json_encode([]),
            'manufacturing_settings' => null,
            'essentials_settings' => null,
            'weighing_scale_setting' => json_encode([]),
            'enable_brand' => true,
            'enable_category' => true,
            'enable_sub_category' => true,
            'enable_price_tax' => true,
            'enable_purchase_status' => true,
            'enable_lot_number' => false,
            'default_unit' => null,
            'enable_sub_units' => false,
            'enable_racks' => false,
            'enable_row' => false,
            'enable_position' => false,
            'enable_editing_product_from_purchase' => true,
            'sales_cmsn_agnt' => null,
            'item_addition_method' => 1,
            'enable_inline_tax' => true,
            'currency_symbol_placement' => 'before',
            'enabled_modules' => json_encode(['purchases', 'add_sale', 'pos_sale', 'stock_transfers', 'stock_adjustment', 'expenses', 'account']),
            'date_format' => 'm/d/Y',
            'time_format' => '24',
            'currency_precision' => 2,
            'quantity_precision' => 2,
            'ref_no_prefixes' => json_encode([
                'purchase' => 'PO',
                'purchase_return' => 'PR',
                'stock_transfer' => 'ST',
                'stock_adjustment' => 'SA',
                'sell_return' => 'SR',
                'expense' => 'EXP',
                'contacts' => 'CO',
                'purchase_payment' => 'PP',
                'sell_payment' => 'SP',
                'expense_payment' => 'EP',
            ]),
            'theme_color' => null,
            'created_by' => 2,
            'asset_settings' => null,
            'accounting_settings' => null,
            'crm_settings' => null,
            'enable_rp' => false,
            'rp_name' => 'Reward Points',
            'amount_for_unit_rp' => 1.0000,
            'min_order_total_for_rp' => 1.0000,
            'max_rp_per_order' => null,
            'redeem_amount_per_unit_rp' => 1.0000,
            'min_order_total_for_redeem' => 1.0000,
            'min_redeem_point' => null,
            'max_redeem_point' => null,
            'rp_expiry_period' => null,
            'rp_expiry_type' => 'year',
            'email_settings' => null,
            'sms_settings' => null,
            'custom_labels' => null,
            'common_settings' => null,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update user with business_id
        DB::table('users')->where('id', 2)->update(['business_id' => 1]);
    }
}
