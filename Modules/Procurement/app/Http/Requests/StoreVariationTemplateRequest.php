<?php

namespace Modules\Procurement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVariationTemplateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:191',
            'values'        => 'nullable|array',
            'values.*.name' => 'required|string|max:191',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'           => 'Template name is required',
            'values.*.name.required'  => 'Each value must have a name',
        ];
    }
}
