<?php

namespace Modules\Procurement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUnitRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'actual_name'          => 'required|string|max:191',
            'short_name'           => 'required|string|max:191',
            'allow_decimal'        => 'nullable|boolean',
            'base_unit_id'         => 'nullable|integer|exists:units,id',
            'base_unit_multiplier' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'actual_name.required' => 'Unit full name is required',
            'short_name.required'  => 'Unit abbreviation is required',
        ];
    }
}
