<?php

namespace Modules\Business\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBusinessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                               => 'sometimes|required|string|max:255',
            'currency_id'                        => 'sometimes|required|integer|exists:currencies,id',
            'start_date'                         => 'sometimes|nullable|date',
            'tax_number_1'                       => 'sometimes|nullable|string|max:100',
            'tax_number_2'                       => 'sometimes|nullable|string|max:100',
            'tax_label_1'                        => 'sometimes|nullable|string|max:50',
            'tax_label_2'                        => 'sometimes|nullable|string|max:50',
            'code_label_1'                       => 'sometimes|nullable|string|max:50',
            'code_label_2'                       => 'sometimes|nullable|string|max:50',
            'code_1'                             => 'sometimes|nullable|string|max:100',
            'code_2'                             => 'sometimes|nullable|string|max:100',
            'default_sales_tax'                  => 'sometimes|nullable|integer',
            'default_profit_percent'             => 'sometimes|nullable|numeric|min:0|max:100',
            'time_zone'                          => 'sometimes|required|string|timezone',
            'fy_start_month'                     => 'sometimes|required|integer|min:1|max:12',
            'accounting_method'                  => 'sometimes|required|in:fifo,lifo,avco',
            'default_sales_discount'             => 'sometimes|nullable|numeric|min:0|max:100',
            'sell_price_tax'                     => 'sometimes|nullable|in:includes,excludes',
            'logo'                               => 'sometimes|nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'sku_prefix'                         => 'sometimes|nullable|string|max:10',
            'enable_product_expiry'              => 'sometimes|boolean',
            'expiry_type'                        => 'sometimes|nullable|in:add_expiry,keep_selling',
            'on_product_expiry'                  => 'sometimes|nullable|in:keep_selling,stop_selling,auto_deactivate',
            'stop_selling_before'                => 'sometimes|nullable|integer|min:0',
            'enable_tooltip'                     => 'sometimes|boolean',
            'purchase_in_diff_currency'          => 'sometimes|boolean',
            'purchase_currency_id'               => 'sometimes|nullable|integer|exists:currencies,id',
            'p_exchange_rate'                    => 'sometimes|nullable|numeric|min:0',
            'transaction_edit_days'              => 'sometimes|nullable|integer|min:0',
            'stock_expiry_alert_days'            => 'sometimes|nullable|integer|min:0',
            'keyboard_shortcuts'                 => 'sometimes|nullable|array',
            'pos_settings'                       => 'sometimes|nullable|array',
            'weighing_scale_setting'             => 'sometimes|nullable|array',
            'enable_brand'                       => 'sometimes|boolean',
            'enable_category'                    => 'sometimes|boolean',
            'enable_sub_category'                => 'sometimes|boolean',
            'enable_price_tax'                   => 'sometimes|boolean',
            'enable_purchase_status'             => 'sometimes|boolean',
            'enable_lot_number'                  => 'sometimes|boolean',
            'default_unit'                       => 'sometimes|nullable|integer',
            'enable_sub_units'                   => 'sometimes|boolean',
            'enable_racks'                       => 'sometimes|boolean',
            'enable_row'                         => 'sometimes|boolean',
            'enable_position'                    => 'sometimes|boolean',
            'enable_editing_product_from_purchase' => 'sometimes|boolean',
            'sales_cmsn_agnt'                    => 'sometimes|nullable|in:logged_in_user,custom',
            'item_addition_method'               => 'sometimes|nullable|integer',
            'enable_inline_tax'                  => 'sometimes|boolean',
            'currency_symbol_placement'          => 'sometimes|nullable|in:before,after',
            'enabled_modules'                    => 'sometimes|nullable|array',
            'date_format'                        => 'sometimes|nullable|string|max:20',
            'time_format'                        => 'sometimes|nullable|in:12,24',
            'currency_precision'                 => 'sometimes|nullable|integer|min:0|max:4',
            'quantity_precision'                 => 'sometimes|nullable|integer|min:0|max:4',
            'ref_no_prefixes'                    => 'sometimes|nullable|array',
            'theme_color'                        => 'sometimes|nullable|string|max:50',
            'enable_rp'                          => 'sometimes|boolean',
            'rp_name'                            => 'sometimes|nullable|string|max:100',
            'amount_for_unit_rp'                 => 'sometimes|nullable|numeric|min:0',
            'min_order_total_for_rp'             => 'sometimes|nullable|numeric|min:0',
            'max_rp_per_order'                   => 'sometimes|nullable|integer|min:0',
            'redeem_amount_per_unit_rp'          => 'sometimes|nullable|numeric|min:0',
            'min_order_total_for_redeem'         => 'sometimes|nullable|numeric|min:0',
            'min_redeem_point'                   => 'sometimes|nullable|integer|min:0',
            'max_redeem_point'                   => 'sometimes|nullable|integer|min:0',
            'rp_expiry_period'                   => 'sometimes|nullable|integer|min:0',
            'rp_expiry_type'                     => 'sometimes|nullable|in:months,years',
            'email_settings'                     => 'sometimes|nullable|array',
            'email_settings.host'                => 'sometimes|nullable|string',
            'email_settings.port'                => 'sometimes|nullable|integer',
            'email_settings.username'            => 'sometimes|nullable|string',
            'email_settings.password'            => 'sometimes|nullable|string',
            'email_settings.encryption'          => 'sometimes|nullable|in:tls,ssl,null',
            'email_settings.from_address'        => 'sometimes|nullable|email',
            'email_settings.from_name'           => 'sometimes|nullable|string',
            'sms_settings'                       => 'sometimes|nullable|array',
            'custom_labels'                      => 'sometimes|nullable|array',
            'common_settings'                    => 'sometimes|nullable|array',
        ];
    }
}
