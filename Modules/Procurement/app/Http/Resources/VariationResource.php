<?php

namespace Modules\Procurement\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VariationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'name'                   => $this->name,
            'sub_sku'                => $this->sub_sku,
            'default_purchase_price' => (float) $this->default_purchase_price,
            'dpp_inc_tax'            => (float) $this->dpp_inc_tax,
            'profit_percent'         => (float) $this->profit_percent,
            'default_sell_price'     => (float) $this->default_sell_price,
            'sell_price_inc_tax'     => (float) $this->sell_price_inc_tax,
            'combo_variations'       => $this->combo_variations,
            'stock'                  => $this->when(
                $this->relationLoaded('locationDetails'),
                fn() => VariationLocationResource::collection($this->locationDetails)
            ),
            'group_prices'           => $this->when(
                $this->relationLoaded('groupPrices'),
                fn() => VariationGroupPriceResource::collection($this->groupPrices)
            ),
        ];
    }
}
