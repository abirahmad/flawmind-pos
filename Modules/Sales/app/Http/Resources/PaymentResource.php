<?php

namespace Modules\Sales\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transaction_id' => $this->transaction_id,
            'business_id' => $this->business_id,
            'amount' => (float) $this->amount,
            'method' => $this->method,
            'payment_ref_no' => $this->payment_ref_no,
            'card_transaction_number' => $this->card_transaction_number,
            'card_number' => $this->card_number,
            'card_type' => $this->card_type,
            'card_holder_name' => $this->card_holder_name,
            'card_month' => $this->card_month,
            'card_year' => $this->card_year,
            'card_security' => $this->card_security,
            'cheque_number' => $this->cheque_number,
            'bank_account_number' => $this->bank_account_number,
            'paid_on' => $this->paid_on?->toISOString(),
            'is_return' => (bool) $this->is_return,
            'payment_for' => $this->payment_for,
            'parent_id' => $this->parent_id,
            'note' => $this->note,
            'document' => $this->document,
            'transaction' => $this->when($this->relationLoaded('transaction'), function () {
                return [
                    'id' => $this->transaction->id,
                    'invoice_no' => $this->transaction->invoice_no,
                    'type' => $this->transaction->type,
                    'final_total' => (float) $this->transaction->final_total,
                ];
            }),
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
