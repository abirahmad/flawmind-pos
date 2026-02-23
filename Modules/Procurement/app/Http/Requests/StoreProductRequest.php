<?php

namespace Modules\Procurement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Procurement\Models\Product;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Core fields
            'name'                  => 'required|string|max:191',
            'type'                  => 'required|string|in:single,variable,modifier,combo',
            'sku'                   => 'nullable|string|max:191',
            'sku_type'              => 'nullable|string|in:with_variation,with_out_variation',
            'unit_id'               => 'required|integer|exists:units,id',
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

            // Product racks (warehouse position per business location)
            'product_racks'                 => 'nullable|array',
            'product_racks.*.location_id'   => 'required|integer|exists:business_locations,id',
            'product_racks.*.rack'          => 'nullable|string|max:191',
            'product_racks.*.row'           => 'nullable|string|max:191',
            'product_racks.*.position'      => 'nullable|string|max:191',

            // Single / Modifier — one DUMMY variation with pricing
            'variations'                             => 'required_if:type,single,modifier|array',
            'variations.*.sub_sku'                   => 'nullable|string|max:191',
            'variations.*.default_purchase_price'    => 'nullable|numeric|min:0',
            'variations.*.dpp_inc_tax'               => 'nullable|numeric|min:0',
            'variations.*.profit_percent'            => 'nullable|numeric',
            'variations.*.default_sell_price'        => 'nullable|numeric|min:0',
            'variations.*.sell_price_inc_tax'        => 'nullable|numeric|min:0',

            // Variable — named attribute groups each with multiple values
            'product_variations'                                         => 'required_if:type,variable|array',
            'product_variations.*.name'                                  => 'required|string|max:191',
            'product_variations.*.variation_template_id'                 => 'nullable|integer|exists:variation_templates,id',
            'product_variations.*.variations'                            => 'required|array|min:1',
            'product_variations.*.variations.*.name'                     => 'required|string|max:191',
            'product_variations.*.variations.*.variation_value_id'       => 'nullable|integer|exists:variation_value_templates,id',
            'product_variations.*.variations.*.sub_sku'                  => 'nullable|string|max:191',
            'product_variations.*.variations.*.default_purchase_price'   => 'nullable|numeric|min:0',
            'product_variations.*.variations.*.dpp_inc_tax'              => 'nullable|numeric|min:0',
            'product_variations.*.variations.*.profit_percent'           => 'nullable|numeric',
            'product_variations.*.variations.*.default_sell_price'       => 'nullable|numeric|min:0',
            'product_variations.*.variations.*.sell_price_inc_tax'       => 'nullable|numeric|min:0',
            'product_variations.*.variations.*.is_hidden'                => 'nullable|boolean',

            // Combo — list of component variations with quantities
            'combo_variations'                        => 'required_if:type,combo|array|min:1',
            'combo_variations.*.variation_id'         => 'required|integer|exists:variations,id',
            'combo_variations.*.quantity'             => 'required|numeric|min:0.001',
            'combo_variations.*.unit_id'              => 'nullable|integer|exists:units,id',
            // Combo pricing stored on the DUMMY variation
            'item_level_purchase_price_total'         => 'nullable|numeric|min:0',
            'purchase_price_inc_tax'                  => 'nullable|numeric|min:0',
            'selling_price'                           => 'nullable|numeric|min:0',
            'selling_price_inc_tax'                   => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'                          => 'Product name is required',
            'type.required'                          => 'Product type is required',
            'type.in'                                => 'Product type must be one of: single, variable, modifier, combo',
            'unit_id.required'                       => 'Unit of measurement is required',
            'variations.required_if'                 => 'Variation details are required for single/modifier products',
            'product_variations.required_if'         => 'Product variations are required for variable products',
            'combo_variations.required_if'           => 'Combo items are required for combo products',
            'combo_variations.*.variation_id.exists' => 'One or more combo variation IDs are invalid',
            'combo_variations.*.quantity.min'        => 'Combo item quantity must be greater than 0',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'sku_type'        => $this->input('sku_type', 'with_out_variation'),
            'tax_type'        => $this->input('tax_type', Product::TAX_EXCLUSIVE),
            'enable_stock'    => $this->boolean('enable_stock', false),
            'is_inactive'     => $this->boolean('is_inactive', false),
            'not_for_selling' => $this->boolean('not_for_selling', false),
            'enable_sr_no'    => $this->boolean('enable_sr_no', false),
        ]);
    }
}
