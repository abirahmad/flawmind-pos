<?php

namespace Modules\Sales\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSellReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transaction_id' => 'required|integer|exists:transactions,id',
            'transaction_date' => 'nullable|date',
            'additional_notes' => 'nullable|string',
            'staff_note' => 'nullable|string',

            // Return lines
            'return_lines' => 'required|array|min:1',
            'return_lines.*.sell_line_id' => 'required|integer|exists:transaction_sell_lines,id',
            'return_lines.*.quantity' => 'required|numeric|min:0.0001',
            'return_lines.*.return_note' => 'nullable|string|max:500',

            // Refund
            'refund_amount' => 'nullable|numeric|min:0',
            'refund_method' => 'nullable|string|in:cash,card,bank_transfer,other',

            // Totals
            'total_before_tax' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'final_total' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'transaction_id.required' => 'Original sale transaction is required',
            'transaction_id.exists' => 'Original sale transaction does not exist',
            'return_lines.required' => 'At least one return line is required',
            'return_lines.min' => 'At least one return line is required',
            'return_lines.*.sell_line_id.required' => 'Sell line ID is required for each return line',
            'return_lines.*.sell_line_id.exists' => 'Sell line does not exist',
            'return_lines.*.quantity.required' => 'Return quantity is required',
            'return_lines.*.quantity.min' => 'Return quantity must be greater than 0',
        ];
    }
}
