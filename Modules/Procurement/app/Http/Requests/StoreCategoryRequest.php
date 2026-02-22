<?php

namespace Modules\Procurement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:191',
            'short_code'    => 'nullable|string|max:191',
            'description'   => 'nullable|string',
            'parent_id'     => 'nullable|integer|min:0',
            'category_type' => 'nullable|string|in:product,expense',
        ];
    }
}
