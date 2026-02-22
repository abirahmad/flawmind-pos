<?php

namespace Modules\Procurement\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'actual_name'          => $this->actual_name,
            'short_name'           => $this->short_name,
            'allow_decimal'        => (bool) $this->allow_decimal,
            'base_unit_id'         => $this->base_unit_id,
            'base_unit_multiplier' => $this->base_unit_multiplier ? (float) $this->base_unit_multiplier : null,
            'display_name'         => $this->display_name,
            'base_unit'            => $this->when($this->relationLoaded('baseUnit') && $this->baseUnit, fn() => [
                'id'          => $this->baseUnit->id,
                'actual_name' => $this->baseUnit->actual_name,
                'short_name'  => $this->baseUnit->short_name,
            ]),
            'sub_units'            => $this->when(
                $this->relationLoaded('subUnits'),
                fn() => UnitResource::collection($this->subUnits)
            ),
            'created_at'           => $this->created_at?->toISOString(),
            'updated_at'           => $this->updated_at?->toISOString(),
        ];
    }
}
