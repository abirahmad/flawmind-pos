<?php

namespace Modules\Sales\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Sales\Models\Transaction;

class StoreSellRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contact_id' => 'required|integer|exists:contacts,id',
            'location_id' => 'nullable|integer|exists:business_locations,id',
            'status' => 'nullable|string|in:draft,final',
            'sub_status' => 'nullable|string',
            'is_quotation' => 'nullable|boolean',
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
            'exchange_rate' => 'nullable|numeric|min:0',

            // Shipping
            'shipping_details' => 'nullable|string',
            'shipping_address' => 'nullable|string',
            'shipping_status' => 'nullable|string|in:ordered,packed,shipped,delivered,cancelled',
            'shipping_charges' => 'nullable|numeric|min:0',

            // Notes
            'additional_notes' => 'nullable|string',
            'staff_note' => 'nullable|string',

            // Other
            'selling_price_group_id' => 'nullable|integer',
            'pay_term_number' => 'nullable|integer|min:0',
            'pay_term_type' => 'nullable|string|in:days,months',
            'commission_agent' => 'nullable|integer',
            'invoice_scheme_id' => 'nullable|integer',
            'is_suspend' => 'nullable|boolean',
            'is_export' => 'nullable|boolean',

            // Custom fields
            'custom_field_1' => 'nullable|string|max:191',
            'custom_field_2' => 'nullable|string|max:191',
            'custom_field_3' => 'nullable|string|max:191',
            'custom_field_4' => 'nullable|string|max:191',

            // Sell lines
            'sell_lines' => 'required|array|min:1',
            'sell_lines.*.product_id' => 'required|integer|exists:products,id',
            'sell_lines.*.variation_id' => 'required|integer|exists:variations,id',
            'sell_lines.*.quantity' => 'required|numeric|min:0.0001',
            'sell_lines.*.unit_price' => 'required|numeric|min:0',
            'sell_lines.*.unit_price_inc_tax' => 'nullable|numeric|min:0',
            'sell_lines.*.unit_price_before_discount' => 'nullable|numeric|min:0',
            'sell_lines.*.line_discount_type' => 'nullable|string|in:fixed,percentage',
            'sell_lines.*.line_discount_amount' => 'nullable|numeric|min:0',
            'sell_lines.*.item_tax' => 'nullable|numeric|min:0',
            'sell_lines.*.tax_id' => 'nullable|integer|exists:tax_rates,id',
            'sell_lines.*.sell_line_note' => 'nullable|string',
            'sell_lines.*.lot_no_line_id' => 'nullable|integer',
            'sell_lines.*.sub_unit_id' => 'nullable|integer',

            // Payments
            'payments' => 'nullable|array',
            'payments.*.amount' => 'required_with:payments|numeric|min:0.01',
            'payments.*.method' => 'required_with:payments|string|in:cash,card,cheque,bank_transfer,other,advance',
            'payments.*.paid_on' => 'nullable|date',
            'payments.*.note' => 'nullable|string',
            'payments.*.account_id' => 'nullable|integer',
            'payments.*.card_number' => 'nullable|string|max:191',
            'payments.*.card_type' => 'nullable|string|in:visa,master,amex,discover,other',
            'payments.*.card_holder_name' => 'nullable|string|max:191',
            'payments.*.card_transaction_number' => 'nullable|string|max:191',
            'payments.*.cheque_number' => 'nullable|string|max:191',
            'payments.*.bank_account_number' => 'nullable|string|max:191',
        ];
    }

    public function messages(): array
    {
        return [
            'contact_id.required' => 'Customer is required',
            'contact_id.exists' => 'Selected customer does not exist',
            'sell_lines.required' => 'At least one product is required',
            'sell_lines.min' => 'At least one product is required',
            'sell_lines.*.product_id.required' => 'Product is required for each line',
            'sell_lines.*.product_id.exists' => 'Selected product does not exist',
            'sell_lines.*.variation_id.required' => 'Product variation is required',
            'sell_lines.*.quantity.required' => 'Quantity is required for each line',
            'sell_lines.*.quantity.min' => 'Quantity must be greater than 0',
            'sell_lines.*.unit_price.required' => 'Unit price is required for each line',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Set defaults
        $this->merge([
            'status' => $this->input('status', Transaction::STATUS_FINAL),
            'is_quotation' => $this->input('is_quotation', false),
            'is_suspend' => $this->input('is_suspend', false),
            'is_export' => $this->input('is_export', false),
            'exchange_rate' => $this->input('exchange_rate', 1),
        ]);
    }
}
