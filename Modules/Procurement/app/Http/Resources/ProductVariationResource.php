<?php

namespace Modules\Procurement\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'name'                 => $this->name,
            'is_dummy'             => (bool) $this->is_dummy,
            'variation_template_id'=> $this->variation_template_id,
            'variations'           => $this->when(
                $this->relationLoaded('variations'),
                fn() => VariationResource::collection($this->variations)
            ),
        ];
    }
}
