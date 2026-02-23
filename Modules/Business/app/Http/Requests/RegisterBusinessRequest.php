<?php

namespace Modules\Business\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterBusinessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // ── Business details ─────────────────────────────────────────
            'name'               => 'required|string|max:255',
            'currency_id'        => 'required|integer|exists:currencies,id',
            'time_zone'          => 'required|string|timezone',
            'fy_start_month'     => 'required|integer|min:1|max:12',
            'accounting_method'  => 'required|in:fifo,lifo,avco',
            'start_date'         => 'sometimes|nullable|date',
            'tax_label_1'        => 'sometimes|nullable|string|max:50',
            'tax_number_1'       => 'sometimes|nullable|string|max:100',
            'tax_label_2'        => 'sometimes|nullable|string|max:50',
            'tax_number_2'       => 'sometimes|nullable|string|max:100',

            // ── Owner user ───────────────────────────────────────────────
            'surname'            => 'sometimes|nullable|string|max:10',
            'first_name'         => 'required|string|max:191',
            'last_name'          => 'sometimes|nullable|string|max:191',
            'username'           => 'required|string|min:4|max:191|unique:users,username',
            'email'              => 'sometimes|nullable|email|max:191|unique:users,email',
            'password'           => 'required|string|min:4|max:255',
            'language'           => 'sometimes|nullable|string|max:10',

            // ── First business location ───────────────────────────────────
            'location_name'      => 'required|string|max:255',
            'landmark'           => 'sometimes|nullable|string|max:255',
            'country'            => 'sometimes|nullable|string|max:100',
            'state'              => 'sometimes|nullable|string|max:100',
            'city'               => 'sometimes|nullable|string|max:100',
            'zip_code'           => 'sometimes|nullable|string|max:20',
            'mobile'             => 'sometimes|nullable|string|max:20',
            'alternate_number'   => 'sometimes|nullable|string|max:20',
            'website'            => 'sometimes|nullable|url|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'              => 'Business name is required.',
            'currency_id.required'       => 'Currency is required.',
            'currency_id.exists'         => 'Selected currency is invalid.',
            'time_zone.required'         => 'Time zone is required.',
            'time_zone.timezone'         => 'Please provide a valid time zone.',
            'fy_start_month.required'    => 'Fiscal year start month is required.',
            'accounting_method.required' => 'Accounting method is required.',
            'first_name.required'        => 'Owner first name is required.',
            'username.required'          => 'Username is required.',
            'username.min'               => 'Username must be at least 4 characters.',
            'username.unique'            => 'Username is already taken.',
            'email.unique'               => 'Email address is already registered.',
            'password.required'          => 'Password is required.',
            'password.min'               => 'Password must be at least 4 characters.',
            'location_name.required'     => 'Business location name is required.',
        ];
    }
}
