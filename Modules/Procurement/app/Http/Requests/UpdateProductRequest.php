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
            'name'                  => 'sometimes|string|max:191',
            'unit_id'               => 'sometimes|integer|exists:units,id',
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
            'product_custom_field1'  => 'nullable|string|max:191',
            'product_custom_field2'  => 'nullable|string|max:191',
            'product_custom_field3'  => 'nullable|string|max:191',
            'product_custom_field4'  => 'nullable|string|max:191',
            'product_custom_field5'  => 'nullable|string|max:191',
        ];
    }
}
