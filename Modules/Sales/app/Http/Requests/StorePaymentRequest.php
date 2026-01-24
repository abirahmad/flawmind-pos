<?php

namespace Modules\Sales\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|string|in:cash,card,cheque,bank_transfer,other,advance,custom_pay_1,custom_pay_2,custom_pay_3',
            'paid_on' => 'nullable|date',
            'note' => 'nullable|string|max:500',
            'account_id' => 'nullable|integer|exists:accounts,id',
            'payment_ref_no' => 'nullable|string|max:191',

            // Card payment fields
            'card_transaction_number' => 'nullable|required_if:method,card|string|max:191',
            'card_number' => 'nullable|string|max:191',
            'card_type' => 'nullable|string|in:visa,master,amex,discover,other',
            'card_holder_name' => 'nullable|string|max:191',
            'card_month' => 'nullable|string|max:2',
            'card_year' => 'nullable|string|max:4',

            // Cheque fields
            'cheque_number' => 'nullable|required_if:method,cheque|string|max:191',

            // Bank transfer fields
            'bank_account_number' => 'nullable|string|max:191',
            'transaction_no' => 'nullable|string|max:191',

            // Cash register
            'cash_register_id' => 'nullable|integer|exists:cash_registers,id',

            // Document
            'document' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Payment amount is required',
            'amount.min' => 'Payment amount must be greater than 0',
            'method.required' => 'Payment method is required',
            'method.in' => 'Invalid payment method',
            'card_transaction_number.required_if' => 'Card transaction number is required for card payments',
            'cheque_number.required_if' => 'Cheque number is required for cheque payments',
        ];
    }
}
