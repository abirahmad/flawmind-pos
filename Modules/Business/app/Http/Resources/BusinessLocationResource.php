<?php

namespace Modules\Business\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessLocationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                       => $this->id,
            'business_id'              => $this->business_id,
            'location_id'              => $this->location_id,
            'name'                     => $this->name,
            'landmark'                 => $this->landmark,
            'country'                  => $this->country,
            'state'                    => $this->state,
            'city'                     => $this->city,
            'zip_code'                 => $this->zip_code,
            'mobile'                   => $this->mobile,
            'alternate_number'         => $this->alternate_number,
            'email'                    => $this->email,
            'website'                  => $this->website,
            'location_address'         => $this->location_address,
            'invoice_scheme_id'        => $this->invoice_scheme_id,
            'sale_invoice_scheme_id'   => $this->sale_invoice_scheme_id,
            'invoice_layout_id'        => $this->invoice_layout_id,
            'sale_invoice_layout_id'   => $this->sale_invoice_layout_id,
            'selling_price_group_id'   => $this->selling_price_group_id,
            'print_receipt_on_invoice' => $this->print_receipt_on_invoice,
            'receipt_printer_type'     => $this->receipt_printer_type,
            'printer_id'               => $this->printer_id,
            'featured_products'        => $this->featured_products ?? [],
            'default_payment_accounts' => $this->default_payment_accounts ?? [],
            'custom_field1'            => $this->custom_field1,
            'custom_field2'            => $this->custom_field2,
            'custom_field3'            => $this->custom_field3,
            'custom_field4'            => $this->custom_field4,
            'accounting_default_map'   => $this->accounting_default_map ?? [],
            'is_active'                => $this->is_active,
            'created_at'               => $this->created_at?->toISOString(),
            'updated_at'               => $this->updated_at?->toISOString(),
        ];
    }
}
