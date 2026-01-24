<?php

namespace Modules\Sales\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'business_id' => $this->business_id,
            'location_id' => $this->location_id,
            'type' => $this->type,
            'sub_type' => $this->sub_type,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'invoice_no' => $this->invoice_no,
            'ref_no' => $this->ref_no,
            'transaction_date' => $this->transaction_date?->toISOString(),
            'contact_id' => $this->contact_id,
            'contact' => $this->when($this->relationLoaded('contact'), function () {
                return new ContactResource($this->contact);
            }),
            'total_before_tax' => (float) $this->total_before_tax,
            'tax_id' => $this->tax_id,
            'tax_amount' => (float) $this->tax_amount,
            'discount_type' => $this->discount_type,
            'discount_amount' => (float) $this->discount_amount,
            'final_total' => (float) $this->final_total,
            'total_paid' => $this->getTotalPaid(),
            'total_due' => $this->getTotalDue(),
            'shipping_charges' => (float) $this->shipping_charges,
            'shipping_details' => $this->shipping_details,
            'shipping_address' => $this->shipping_address,
            'shipping_status' => $this->shipping_status,
            'delivered_to' => $this->delivered_to,
            'additional_notes' => $this->additional_notes,
            'staff_note' => $this->staff_note,
            'is_quotation' => (bool) $this->is_quotation,
            'is_suspend' => (bool) $this->is_suspend,
            'is_direct_sale' => (bool) $this->is_direct_sale,
            'exchange_rate' => (float) $this->exchange_rate,
            'selling_price_group_id' => $this->selling_price_group_id,
            'pay_term_number' => $this->pay_term_number,
            'pay_term_type' => $this->pay_term_type,
            'return_parent_id' => $this->return_parent_id,
            'sell_lines' => $this->when($this->relationLoaded('sellLines'), function () {
                return SellLineResource::collection($this->sellLines);
            }),
            'payment_lines' => $this->when($this->relationLoaded('paymentLines'), function () {
                return PaymentResource::collection($this->paymentLines);
            }),
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Get total paid amount.
     */
    protected function getTotalPaid(): float
    {
        if ($this->relationLoaded('paymentLines')) {
            return (float) $this->paymentLines->where('is_return', false)->sum('amount');
        }

        return 0.0;
    }

    /**
     * Get total due amount.
     */
    protected function getTotalDue(): float
    {
        return max(0, (float) $this->final_total - $this->getTotalPaid());
    }
}
