<?php

namespace Modules\Procurement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarrantyRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:191',
            'description'   => 'nullable|string',
            'duration'      => 'required|integer|min:1',
            'duration_type' => 'required|string|in:days,months,years',
        ];
    }

    public function messages(): array
    {
        return [
            'duration.required'      => 'Warranty duration is required',
            'duration_type.required' => 'Warranty duration type (days/months/years) is required',
        ];
    }
}
