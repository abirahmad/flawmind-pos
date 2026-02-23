<?php

namespace Modules\Business\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBusinessLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $businessId = $this->user()->business_id;
        $locationId = $this->route('id');

        return [
            'location_id'             => [
                'required',
                'string',
                'max:50',
                "unique:business_locations,location_id,NULL,id,business_id,{$businessId},deleted_at,NULL",
            ],
            'name'                    => 'required|string|max:255',
            'landmark'                => 'sometimes|nullable|string|max:255',
            'country'                 => 'sometimes|nullable|string|max:100',
            'state'                   => 'sometimes|nullable|string|max:100',
            'city'                    => 'sometimes|nullable|string|max:100',
            'zip_code'                => 'sometimes|nullable|string|max:20',
            'mobile'                  => 'sometimes|nullable|string|max:20',
            'alternate_number'        => 'sometimes|nullable|string|max:20',
            'email'                   => 'sometimes|nullable|email|max:255',
            'website'                 => 'sometimes|nullable|url|max:255',
            'invoice_scheme_id'       => 'sometimes|nullable|integer',
            'sale_invoice_scheme_id'  => 'sometimes|nullable|integer',
            'invoice_layout_id'       => 'sometimes|nullable|integer',
            'sale_invoice_layout_id'  => 'sometimes|nullable|integer',
            'selling_price_group_id'  => 'sometimes|nullable|integer',
            'print_receipt_on_invoice' => 'sometimes|boolean',
            'receipt_printer_type'    => 'sometimes|nullable|in:browser,printer',
            'printer_id'              => 'sometimes|nullable|integer',
            'featured_products'       => 'sometimes|nullable|array',
            'featured_products.*'     => 'sometimes|integer',
            'default_payment_accounts' => 'sometimes|nullable|array',
            'custom_field1'           => 'sometimes|nullable|string|max:255',
            'custom_field2'           => 'sometimes|nullable|string|max:255',
            'custom_field3'           => 'sometimes|nullable|string|max:255',
            'custom_field4'           => 'sometimes|nullable|string|max:255',
            'accounting_default_map'  => 'sometimes|nullable|array',
        ];
    }
}
