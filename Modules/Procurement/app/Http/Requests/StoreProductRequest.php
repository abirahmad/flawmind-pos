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
            // Core
            'name'                  => 'required|string|max:191',
            'type'                  => 'required|string|in:single,variable,modifier,combo',
            'sku'                   => 'required|string|max:191',
            'unit_id'               => 'required|integer|exists:units,id',
            'secondary_unit_id'     => 'nullable|integer|exists:units,id',
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

            // Custom fields
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

            // Single / Modifier variations
            'variations'                             => 'required_if:type,single,modifier|array',
            'variations.*.sub_sku'                   => 'nullable|string|max:191',
            'variations.*.default_purchase_price'    => 'nullable|numeric|min:0',
            'variations.*.dpp_inc_tax'               => 'nullable|numeric|min:0',
            'variations.*.profit_percent'            => 'nullable|numeric',
            'variations.*.default_sell_price'        => 'nullable|numeric|min:0',
            'variations.*.sell_price_inc_tax'        => 'nullable|numeric|min:0',

            // Variable product_variations
            'product_variations'                                => 'required_if:type,variable|array',
            'product_variations.*.name'                         => 'required|string|max:191',
            'product_variations.*.variation_template_id'        => 'nullable|integer|exists:variation_templates,id',
            'product_variations.*.variations'                   => 'required|array|min:1',
            'product_variations.*.variations.*.name'            => 'required|string|max:191',
            'product_variations.*.variations.*.variation_value_id' => 'nullable|integer|exists:variation_value_templates,id',
            'product_variations.*.variations.*.sub_sku'         => 'nullable|string|max:191',
            'product_variations.*.variations.*.default_purchase_price' => 'nullable|numeric|min:0',
            'product_variations.*.variations.*.dpp_inc_tax'    => 'nullable|numeric|min:0',
            'product_variations.*.variations.*.profit_percent' => 'nullable|numeric',
            'product_variations.*.variations.*.default_sell_price' => 'nullable|numeric|min:0',
            'product_variations.*.variations.*.sell_price_inc_tax' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'          => 'Product name is required',
            'type.required'          => 'Product type is required',
            'type.in'                => 'Product type must be one of: single, variable, modifier, combo',
            'sku.required'           => 'SKU is required',
            'unit_id.required'       => 'Unit of measurement is required',
            'variations.required_if' => 'Variation details are required for single/modifier products',
            'product_variations.required_if' => 'Product variations are required for variable products',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'tax_type'       => $this->input('tax_type', Product::TAX_EXCLUSIVE),
            'enable_stock'   => $this->input('enable_stock', false),
            'is_inactive'    => $this->input('is_inactive', false),
            'not_for_selling'=> $this->input('not_for_selling', false),
            'enable_sr_no'   => $this->input('enable_sr_no', false),
        ]);
    }
}
