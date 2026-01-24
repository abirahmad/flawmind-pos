<?php

namespace Modules\Sales\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSellRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contact_id' => 'nullable|integer|exists:contacts,id',
            'location_id' => 'nullable|integer|exists:business_locations,id',
            'status' => 'nullable|string|in:draft,final',
            'sub_status' => 'nullable|string',
            'transaction_date' => 'nullable|date',
            'ref_no' => 'nullable|string|max:191',

            // Pricing
            'discount_type' => 'nullable|string|in:fixed,percentage',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_id' => 'nullable|integer|exists:tax_rates,id',
            'tax_amount' => 'nullable|numeric|min:0',
            'total_before_tax' => 'nullable|numeric|min:0',
            'final_total' => 'nullable|numeric|min:0',
            'round_off_amount' => 'nullable|numeric',

            // Shipping
            'shipping_details' => 'nullable|string',
            'shipping_address' => 'nullable|string',
            'shipping_status' => 'nullable|string|in:ordered,packed,shipped,delivered,cancelled',
            'shipping_charges' => 'nullable|numeric|min:0',

            // Notes
            'additional_notes' => 'nullable|string',
            'staff_note' => 'nullable|string',

            // Custom fields
            'custom_field_1' => 'nullable|string|max:191',
            'custom_field_2' => 'nullable|string|max:191',
            'custom_field_3' => 'nullable|string|max:191',
            'custom_field_4' => 'nullable|string|max:191',

            // Sell lines (optional for update)
            'sell_lines' => 'nullable|array|min:1',
            'sell_lines.*.product_id' => 'required_with:sell_lines|integer|exists:products,id',
            'sell_lines.*.variation_id' => 'required_with:sell_lines|integer|exists:variations,id',
            'sell_lines.*.quantity' => 'required_with:sell_lines|numeric|min:0.0001',
            'sell_lines.*.unit_price' => 'required_with:sell_lines|numeric|min:0',
            'sell_lines.*.unit_price_inc_tax' => 'nullable|numeric|min:0',
            'sell_lines.*.line_discount_type' => 'nullable|string|in:fixed,percentage',
            'sell_lines.*.line_discount_amount' => 'nullable|numeric|min:0',
            'sell_lines.*.item_tax' => 'nullable|numeric|min:0',
            'sell_lines.*.tax_id' => 'nullable|integer|exists:tax_rates,id',
            'sell_lines.*.sell_line_note' => 'nullable|string',
        ];
    }
}
