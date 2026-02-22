<?php

namespace Modules\Procurement\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'short_code'    => $this->short_code,
            'description'   => $this->description,
            'category_type' => $this->category_type,
            'parent_id'     => $this->parent_id,
            'sub_categories'=> $this->when(
                $this->relationLoaded('subCategories'),
                fn() => CategoryResource::collection($this->subCategories)
            ),
            'created_at'    => $this->created_at?->toISOString(),
            'updated_at'    => $this->updated_at?->toISOString(),
        ];
    }
}
