<?php

namespace Modules\Business\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Business extends Model
{
    protected $table = 'business';

    protected $fillable = [
        'name',
        'currency_id',
        'start_date',
        'tax_number_1',
        'tax_number_2',
        'tax_label_1',
        'tax_label_2',
        'code_label_1',
        'code_label_2',
        'code_1',
        'code_2',
        'default_sales_tax',
        'default_profit_percent',
        'owner_id',
        'time_zone',
        'fy_start_month',
        'accounting_method',
        'default_sales_discount',
        'sell_price_tax',
        'logo',
        'sku_prefix',
        'enable_product_expiry',
        'expiry_type',
        'on_product_expiry',
        'stop_selling_before',
        'enable_tooltip',
        'purchase_in_diff_currency',
        'purchase_currency_id',
        'p_exchange_rate',
        'transaction_edit_days',
        'stock_expiry_alert_days',
        'keyboard_shortcuts',
        'pos_settings',
        'weighing_scale_setting',
        'enable_brand',
        'enable_category',
        'enable_sub_category',
        'enable_price_tax',
        'enable_purchase_status',
        'enable_lot_number',
        'default_unit',
        'enable_sub_units',
        'enable_racks',
        'enable_row',
        'enable_position',
        'enable_editing_product_from_purchase',
        'sales_cmsn_agnt',
        'item_addition_method',
        'enable_inline_tax',
        'currency_symbol_placement',
        'enabled_modules',
        'date_format',
        'time_format',
        'currency_precision',
        'quantity_precision',
        'ref_no_prefixes',
        'theme_color',
        'created_by',
        'enable_rp',
        'rp_name',
        'amount_for_unit_rp',
        'min_order_total_for_rp',
        'max_rp_per_order',
        'redeem_amount_per_unit_rp',
        'min_order_total_for_redeem',
        'min_redeem_point',
        'max_redeem_point',
        'rp_expiry_period',
        'rp_expiry_type',
        'email_settings',
        'sms_settings',
        'custom_labels',
        'common_settings',
        'is_active',
    ];

    protected $casts = [
        'keyboard_shortcuts'   => 'array',
        'pos_settings'         => 'array',
        'weighing_scale_setting' => 'array',
        'enabled_modules'      => 'array',
        'ref_no_prefixes'      => 'array',
        'email_settings'       => 'array',
        'sms_settings'         => 'array',
        'custom_labels'        => 'array',
        'common_settings'      => 'array',
        'enable_product_expiry'              => 'boolean',
        'enable_tooltip'                     => 'boolean',
        'purchase_in_diff_currency'          => 'boolean',
        'enable_brand'                       => 'boolean',
        'enable_category'                    => 'boolean',
        'enable_sub_category'                => 'boolean',
        'enable_price_tax'                   => 'boolean',
        'enable_purchase_status'             => 'boolean',
        'enable_lot_number'                  => 'boolean',
        'enable_sub_units'                   => 'boolean',
        'enable_racks'                       => 'boolean',
        'enable_row'                         => 'boolean',
        'enable_position'                    => 'boolean',
        'enable_editing_product_from_purchase' => 'boolean',
        'enable_inline_tax'                  => 'boolean',
        'enable_rp'                          => 'boolean',
        'is_active'                          => 'boolean',
        'default_profit_percent'             => 'decimal:4',
        'default_sales_discount'             => 'decimal:4',
        'p_exchange_rate'                    => 'decimal:4',
        'amount_for_unit_rp'                 => 'decimal:4',
        'redeem_amount_per_unit_rp'          => 'decimal:4',
        'min_order_total_for_rp'             => 'decimal:4',
        'min_order_total_for_redeem'         => 'decimal:4',
        'start_date'                         => 'date',
    ];

    protected $hidden = ['email_settings', 'sms_settings'];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'owner_id');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(BusinessLocation::class, 'business_id');
    }

    public function activeLocations(): HasMany
    {
        return $this->hasMany(BusinessLocation::class, 'business_id')->where('is_active', 1);
    }
}
