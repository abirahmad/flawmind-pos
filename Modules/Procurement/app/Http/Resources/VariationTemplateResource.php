<?php

namespace Modules\Procurement\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VariationTemplateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'business_id'=> $this->business_id,
            'values'     => $this->when(
                $this->relationLoaded('values'),
                fn() => $this->values->map(fn($v) => [
                    'id'   => $v->id,
                    'name' => $v->name,
                ])
            ),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
