<?php

namespace Modules\Procurement\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VariationGroupPriceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'price_group_id'   => $this->price_group_id,
            'price_group_name' => $this->when($this->relationLoaded('priceGroup'), fn() => $this->priceGroup?->name),
            'price_inc_tax'    => (float) $this->price_inc_tax,
            'price_type'       => $this->price_type,
            'calculated_price' => $this->calculated_price,
        ];
    }
}
