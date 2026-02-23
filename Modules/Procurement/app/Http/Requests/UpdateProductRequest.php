<?php

namespace Modules\Procurement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Core fields
            'name'                  => 'sometimes|string|max:191',
            'sku'                   => 'sometimes|nullable|string|max:191',
            'unit_id'               => 'sometimes|integer|exists:units,id',
            'secondary_unit_id'     => 'nullable|integer|exists:units,id',
            'sub_unit_ids'          => 'nullable|array',
            'sub_unit_ids.*'        => 'integer|exists:units,id',
            'brand_id'              => 'nullable|integer|exists:brands,id',
            'category_id'           => 'nullable|integer|exists:categories,id',
            'sub_category_id'       => 'nullable|integer|exists:categories,id',
            'tax'                   => 'nullable|integer|exists:tax_rates,id',
            'tax_type'              => 'nullable|string|in:inclusive,exclusive',
            'enable_stock'          => 'nullable|boolean',
            'alert_quantity'        => 'nullable|numeric|min:0',
            'barcode_type'          => 'nullable|string|in:C39,C128,EAN-13,EAN-8,UPC-A,UPC-E,ITF-14',
            'warranty_id'           => 'nullable|integer|exists:warranties,id',
            'weight'                => 'nullable|string|max:191',
            'product_description'   => 'nullable|string',
            'is_inactive'           => 'nullable|boolean',
            'not_for_selling'       => 'nullable|boolean',
            'expiry_period'         => 'nullable|numeric',
            'expiry_period_type'    => 'nullable|string|in:days,months',
            'enable_sr_no'          => 'nullable|boolean',
            'image'                 => 'nullable|image|max:2048',
            'location_ids'          => 'nullable|array',
            'location_ids.*'        => 'integer|exists:business_locations,id',

            // Custom fields 1-20
            'product_custom_field1'  => 'nullable|string|max:191',
            'product_custom_field2'  => 'nullable|string|max:191',
            'product_custom_field3'  => 'nullable|string|max:191',
            'product_custom_field4'  => 'nullable|string|max:191',
            'product_custom_field5'  => 'nullable|string|max:191',
            'product_custom_field6'  => 'nullable|string|max:191',
            'product_custom_field7'  => 'nullable|string|max:191',
            'product_custom_field8'  => 'nullable|string|max:191',
            'product_custom_field9'  => 'nullable|string|max:191',
            'product_custom_field10' => 'nullable|string|max:191',
            'product_custom_field11' => 'nullable|string|max:191',
            'product_custom_field12' => 'nullable|string|max:191',
            'product_custom_field13' => 'nullable|string|max:191',
            'product_custom_field14' => 'nullable|string|max:191',
            'product_custom_field15' => 'nullable|string|max:191',
            'product_custom_field16' => 'nullable|string|max:191',
            'product_custom_field17' => 'nullable|string|max:191',
            'product_custom_field18' => 'nullable|string|max:191',
            'product_custom_field19' => 'nullable|string|max:191',
            'product_custom_field20' => 'nullable|string|max:191',

            // Rack details (replaces all existing racks on save)
            'product_racks'                 => 'nullable|array',
            'product_racks.*.location_id'   => 'required|integer|exists:business_locations,id',
            'product_racks.*.rack'          => 'nullable|string|max:191',
            'product_racks.*.row'           => 'nullable|string|max:191',
            'product_racks.*.position'      => 'nullable|string|max:191',

            // Single/modifier variation price updates (must reference existing variation id)
            'variations'                             => 'nullable|array',
            'variations.*.id'                        => 'required|integer|exists:variations,id',
            'variations.*.default_purchase_price'    => 'nullable|numeric|min:0',
            'variations.*.dpp_inc_tax'               => 'nullable|numeric|min:0',
            'variations.*.profit_percent'            => 'nullable|numeric',
            'variations.*.default_sell_price'        => 'nullable|numeric|min:0',
            'variations.*.sell_price_inc_tax'        => 'nullable|numeric|min:0',

            // Variable product variation price updates
            'product_variations'                                         => 'nullable|array',
            'product_variations.*.id'                                    => 'required|integer|exists:product_variations,id',
            'product_variations.*.variations'                            => 'nullable|array',
            'product_variations.*.variations.*.id'                       => 'required|integer|exists:variations,id',
            'product_variations.*.variations.*.default_purchase_price'   => 'nullable|numeric|min:0',
            'product_variations.*.variations.*.dpp_inc_tax'              => 'nullable|numeric|min:0',
            'product_variations.*.variations.*.profit_percent'           => 'nullable|numeric',
            'product_variations.*.variations.*.default_sell_price'       => 'nullable|numeric|min:0',
            'product_variations.*.variations.*.sell_price_inc_tax'       => 'nullable|numeric|min:0',
            'product_variations.*.variations.*.is_hidden'                => 'nullable|boolean',
        ];
    }
}
