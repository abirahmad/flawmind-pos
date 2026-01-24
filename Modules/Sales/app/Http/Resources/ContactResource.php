<?php

namespace Modules\Sales\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'business_id' => $this->business_id,
            'type' => $this->type,
            'supplier_business_name' => $this->supplier_business_name,
            'name' => $this->name,
            'prefix' => $this->prefix,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'contact_id' => $this->contact_id,
            'contact_status' => $this->contact_status,
            'tax_number' => $this->tax_number,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'zip_code' => $this->zip_code,
            'mobile' => $this->mobile,
            'landline' => $this->landline,
            'alternate_number' => $this->alternate_number,
            'pay_term_number' => $this->pay_term_number,
            'pay_term_type' => $this->pay_term_type,
            'credit_limit' => $this->credit_limit ? (float) $this->credit_limit : null,
            'total_rp' => (int) $this->total_rp,
            'total_rp_used' => (int) $this->total_rp_used,
            'total_rp_expired' => (int) $this->total_rp_expired,
            'available_rp' => $this->getAvailableRewardPoints(),
            'custom_field1' => $this->custom_field1,
            'custom_field2' => $this->custom_field2,
            'custom_field3' => $this->custom_field3,
            'custom_field4' => $this->custom_field4,
            'dob' => $this->dob?->format('Y-m-d'),
            'shipping_address' => $this->shipping_address,
            'balance' => $this->when(isset($this->balance), fn() => (float) $this->balance),
            'total_due' => $this->when(isset($this->total_due), fn() => (float) $this->total_due),
            'is_default' => (bool) $this->is_default,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Get available reward points.
     */
    protected function getAvailableRewardPoints(): int
    {
        return max(0, (int) $this->total_rp - (int) $this->total_rp_used - (int) $this->total_rp_expired);
    }
}
