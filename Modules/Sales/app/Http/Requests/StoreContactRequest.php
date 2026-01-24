<?php

namespace Modules\Sales\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $contactId = $this->route('id');

        return [
            'type' => 'required|string|in:customer,supplier,both,lead',
            'name' => 'nullable|string|max:191',
            'prefix' => 'nullable|string|max:25',
            'first_name' => 'nullable|string|max:191',
            'middle_name' => 'nullable|string|max:191',
            'last_name' => 'nullable|string|max:191',
            'supplier_business_name' => 'nullable|string|max:191',

            // Contact info
            'mobile' => 'required|string|max:191',
            'landline' => 'nullable|string|max:191',
            'alternate_number' => 'nullable|string|max:191',
            'email' => 'nullable|email|max:191',
            'contact_id' => 'nullable|string|max:191',

            // Address
            'address_line_1' => 'nullable|string|max:500',
            'address_line_2' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:191',
            'state' => 'nullable|string|max:191',
            'country' => 'nullable|string|max:191',
            'zip_code' => 'nullable|string|max:191',

            // Tax
            'tax_number' => 'nullable|string|max:191',

            // Credit
            'credit_limit' => 'nullable|numeric|min:0',
            'pay_term_number' => 'nullable|integer|min:0',
            'pay_term_type' => 'nullable|string|in:days,months',

            // Group
            'customer_group_id' => 'nullable|integer|exists:customer_groups,id',

            // DOB
            'dob' => 'nullable|date',

            // Shipping
            'shipping_address' => 'nullable|string',

            // CRM
            'crm_source' => 'nullable|string|max:191',
            'crm_life_stage' => 'nullable|string|max:191',
            'position' => 'nullable|string|max:191',

            // Custom fields
            'custom_field1' => 'nullable|string|max:191',
            'custom_field2' => 'nullable|string|max:191',
            'custom_field3' => 'nullable|string|max:191',
            'custom_field4' => 'nullable|string|max:191',
            'custom_field5' => 'nullable|string|max:191',
            'custom_field6' => 'nullable|string|max:191',
            'custom_field7' => 'nullable|string|max:191',
            'custom_field8' => 'nullable|string|max:191',
            'custom_field9' => 'nullable|string|max:191',
            'custom_field10' => 'nullable|string|max:191',
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Contact type is required',
            'type.in' => 'Invalid contact type',
            'mobile.required' => 'Mobile number is required',
            'email.email' => 'Invalid email format',
        ];
    }
}
