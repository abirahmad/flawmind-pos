<?php

namespace Modules\Sales\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SellLineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transaction_id' => $this->transaction_id,
            'product_id' => $this->product_id,
            'variation_id' => $this->variation_id,
            'quantity' => (float) $this->quantity,
            'quantity_returned' => (float) $this->quantity_returned,
            'unit_price' => (float) $this->unit_price,
            'unit_price_before_discount' => (float) $this->unit_price_before_discount,
            'unit_price_inc_tax' => (float) $this->unit_price_inc_tax,
            'line_discount_type' => $this->line_discount_type,
            'line_discount_amount' => (float) $this->line_discount_amount,
            'item_tax' => (float) $this->item_tax,
            'tax_id' => $this->tax_id,
            'line_total' => $this->calculateLineTotal(),
            'product' => $this->when($this->relationLoaded('product'), function () {
                return [
                    'id' => $this->product->id,
                    'name' => $this->product->name,
                    'sku' => $this->product->sku,
                    'type' => $this->product->type,
                ];
            }),
            'variation' => $this->when($this->relationLoaded('variation'), function () {
                return [
                    'id' => $this->variation->id,
                    'name' => $this->variation->name,
                    'sub_sku' => $this->variation->sub_sku,
                ];
            }),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Calculate line total.
     */
    protected function calculateLineTotal(): float
    {
        $quantity = (float) $this->quantity - (float) $this->quantity_returned;
        $unitPrice = (float) $this->unit_price_inc_tax;

        return round($quantity * $unitPrice, 2);
    }
}
